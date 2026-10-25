<?php

use App\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// V1 API Endpoints (OpenAPI / Swagger documented)
Route::prefix('v1')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'database' => 'connected',
            'cache' => 'redis_healthy',
        ]);
    });

    // Public Destinations & Services (used to dynamically populate forms on lead generation page)
    Route::get('/destinations', function () {
        $countries = \App\Models\Country::where('is_active', true)
            ->with(['services' => function ($q) {
                $q->where('is_active', true)
                  ->select('id', 'country_id', 'name', 'code', 'default_government_fee', 'default_consultancy_fee', 'standard_sla_days');
            }])
            ->select('id', 'name', 'iso2', 'iso3', 'currency_code', 'official_vac_name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'count' => $countries->count(),
            'data' => $countries,
        ]);
    });

    // GoHighLevel Scheduling Engine: Available Slots & Booking API
    Route::get('/appointments/available-slots', [\App\Http\Controllers\AppointmentBookingController::class, 'getAvailableSlots']);
    Route::post('/appointments/book', [\App\Http\Controllers\AppointmentBookingController::class, 'book']);

    // Public / Website Inbound Lead Capture
    Route::post('/leads', function (Request $request, LeadService $leadService) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'nationality' => 'nullable|string|max:100',
            'qatar_residence_status' => 'nullable|string|max:50',
            'destination_country' => 'nullable|string|max:100',
            'visa_or_service_type' => 'nullable|string|max:100',
            'planned_travel_date' => 'nullable|date',
            'passport_no' => 'nullable|string|max:50',
            'qid_no' => 'nullable|string|max:50',
            'source_code' => 'nullable|string|max:50',
            'campaign' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['source_code'] = $validated['source_code'] ?? 'website';
        $lead = $leadService->ingestLead($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Lead received and processed successfully',
            'lead_number' => $lead->lead_number,
            'is_duplicate' => $lead->is_duplicate,
            'assigned_agent' => $lead->assignedTo?->name ?? 'Senior Immigration Consultant',
        ], 201);
    });

    // Public Lead Tracking / Inquiry Status Lookup
    Route::get('/leads/lookup', function (Request $request) {
        $query = trim((string) $request->input('query', ''));
        if (empty($query)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please provide a valid Lead Reference (e.g. LD-2026-0001) or registered phone number.',
            ], 422);
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $query);

        $lead = \App\Models\Lead::query()
            ->where('lead_number', $query)
            ->orWhere('phone', $query)
            ->orWhere('whatsapp', $query)
            ->when(!empty($cleanPhone) && strlen($cleanPhone) >= 7, function ($q) use ($cleanPhone) {
                $q->orWhere('phone', 'like', "%{$cleanPhone}%")
                  ->orWhere('whatsapp', 'like', "%{$cleanPhone}%");
            })
            ->with(['assignedTo:id,name', 'source:id,name'])
            ->latest()
            ->first();

        if (! $lead) {
            return response()->json([
                'status' => 'error',
                'message' => 'No consultation record found matching: ' . htmlspecialchars($query),
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'lead' => [
                'lead_number' => $lead->lead_number,
                'name' => $lead->name,
                'destination_country' => $lead->destination_country ?? 'Unspecified',
                'visa_or_service_type' => $lead->visa_or_service_type ?? 'General Consultation',
                'status' => ucfirst($lead->status),
                'assigned_consultant' => $lead->assignedTo?->name ?? 'Immigration Advisory Desk',
                'source' => $lead->source?->name ?? 'Online Portal',
                'created_at' => $lead->created_at?->format('M d, Y h:i A') ?? 'Recent',
                'travel_date' => $lead->planned_travel_date?->format('M d, Y') ?? 'Flexible',
            ],
        ]);
    });

    // Live Inbound Leads Feed (Where CRM fetches recent leads from all channels)
    Route::get('/leads', function (Request $request) {
        $limit = min(50, max(1, (int) $request->input('limit', 15)));
        $source = $request->input('source');
        $status = $request->input('status');

        $query = \App\Models\Lead::with(['assignedTo:id,name', 'source:id,name,code'])->latest();

        if (! empty($source)) {
            $query->whereHas('source', fn($q) => $q->where('code', $source));
        }

        if (! empty($status)) {
            $query->where('status', $status);
        }

        $leads = $query->take($limit)->get()->map(function ($l) {
            return [
                'id' => $l->id,
                'lead_number' => $l->lead_number,
                'name' => $l->name,
                'phone_masked' => substr($l->phone ?? $l->whatsapp ?? 'Unknown', 0, 6) . '****',
                'destination_country' => $l->destination_country,
                'visa_or_service_type' => $l->visa_or_service_type,
                'status' => $l->status,
                'source' => $l->source?->name ?? ($l->source_id ? 'Partner' : 'Website'),
                'source_code' => $l->source?->code ?? 'website',
                'assigned_agent' => $l->assignedTo?->name ?? 'Pending Assignment',
                'is_duplicate' => $l->is_duplicate,
                'created_at' => $l->created_at?->toIso8601String(),
                'created_at_human' => $l->created_at?->diffForHumans(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'count' => $leads->count(),
            'leads' => $leads,
        ]);
    });

    // Webhooks for Leads & Omnichannel Inbox
    Route::prefix('webhooks')->group(function () {
        Route::post('/meta-leads', function (Request $request, LeadService $leadService) {
            $data = [
                'name' => $request->input('full_name') ?? $request->input('name') ?? 'Meta Lead',
                'phone' => $request->input('phone_number') ?? $request->input('phone'),
                'email' => $request->input('email'),
                'destination_country' => $request->input('destination'),
                'visa_or_service_type' => $request->input('service_interest'),
                'source_code' => 'meta_ads',
                'campaign' => $request->input('campaign_name') ?? 'Meta Lead Ad Campaign',
            ];

            $lead = $leadService->ingestLead($data);

            return response()->json([
                'status' => 'success',
                'lead_number' => $lead->lead_number,
                'is_duplicate' => $lead->is_duplicate,
            ]);
        });

        Route::post('/whatsapp', function (Request $request, LeadService $leadService) {
            // Evolution API payload format
            $remoteJid = $request->input('data.key.remoteJid') ?? $request->input('sender') ?? '';
            $pushName = $request->input('data.pushName') ?? $request->input('name') ?? 'WhatsApp User';
            $messageText = $request->input('data.message.conversation') 
                ?? $request->input('data.message.extendedTextMessage.text') 
                ?? $request->input('message') 
                ?? '';

            // Clean phone number from remoteJid (e.g. 97455551234@s.whatsapp.net -> +97455551234)
            $phone = preg_replace('/[^0-9]/', '', explode('@', $remoteJid)[0]);
            if ($phone && ! str_starts_with($phone, '+')) {
                $phone = '+' . $phone;
            }

            if (! $phone && ! $request->has('phone')) {
                return response()->json(['status' => 'ignored', 'reason' => 'No phone number provided']);
            }

            $data = [
                'name' => $pushName,
                'phone' => $phone ?: $request->input('phone'),
                'whatsapp' => $phone ?: $request->input('phone'),
                'source_code' => 'whatsapp',
                'campaign' => 'WhatsApp Direct Inbound',
            ];

            $lead = $leadService->ingestLead($data);

            return response()->json([
                'status' => 'success',
                'lead_number' => $lead->lead_number,
                'message_received' => $messageText ? true : false,
            ]);
        });
    });
});
