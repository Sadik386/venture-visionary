<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Country;
use App\Models\Lead;
use App\Models\User;
use App\Services\LeadService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentBookingController extends Controller
{
    /**
     * Display public interactive booking portal.
     */
    public function show(Request $request)
    {
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $consultants = User::role('sales_agent')->where('is_active', true)->get();

        return view('appointments.book', compact('countries', 'consultants'));
    }

    /**
     * API: Get open time slots for a given date (09:00 to 17:00 Qatar time).
     */
    public function getAvailableSlots(Request $request)
    {
        $dateStr = $request->query('date', now()->addDay()->toDateString());
        $date = Carbon::parse($dateStr);

        // Standard consultation slots
        $slotHours = ['09:30', '11:00', '13:00', '14:30', '16:00', '17:30'];
        $slots = [];

        foreach ($slotHours as $time) {
            $slotDateTime = Carbon::parse("{$dateStr} {$time}:00", 'Asia/Qatar');

            // Skip past slots
            if ($slotDateTime->isPast()) {
                continue;
            }

            // Check if slot already taken
            $taken = Appointment::where('appointment_time', $slotDateTime)
                ->whereNotIn('status', ['cancelled'])
                ->exists();

            $slots[] = [
                'time' => $time,
                'datetime' => $slotDateTime->toIso8601String(),
                'display' => $slotDateTime->format('h:i A'),
                'available' => ! $taken,
            ];
        }

        return response()->json([
            'status' => 'success',
            'date' => $dateStr,
            'slots' => $slots,
        ]);
    }

    /**
     * Handle appointment reservation from public portal or API.
     */
    public function book(Request $request, LeadService $leadService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'destination_country' => 'nullable|string|max:100',
            'service_type' => 'nullable|string|max:100',
            'appointment_time' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'consultant_id' => 'nullable|uuid',
        ]);

        $appointmentTime = Carbon::parse($validated['appointment_time']);

        // Ingest or link lead
        $lead = $leadService->ingestLead([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'whatsapp' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'destination_country' => $validated['destination_country'] ?? 'General Consultation',
            'visa_or_service_type' => $validated['service_type'] ?? 'Advisory Meeting',
            'source_code' => 'website',
            'campaign' => 'Online Calendar Booking',
            'notes' => $validated['notes'] ?? 'Booked via public appointment portal',
            'assigned_to' => $validated['consultant_id'] ?? null,
        ]);

        // Create appointment record
        $title = ($validated['destination_country'] ? "{$validated['destination_country']} Visa" : 'Immigration') 
            . ' Strategy Consultation';

        $appointment = Appointment::create([
            'lead_id' => $lead->id,
            'assigned_to_id' => $lead->assigned_to,
            'title' => $title,
            'appointment_type' => 'consultation',
            'location_name' => 'Venture Visionary Advisory Office, Doha (or Online)',
            'client_name' => $lead->name,
            'client_phone' => $lead->phone ?? $lead->whatsapp,
            'client_email' => $lead->email,
            'appointment_time' => $appointmentTime,
            'status' => 'scheduled',
            'notes' => $validated['notes'] ?? 'Booked online',
        ]);

        // Advance lead to 'consultation' stage automatically
        $leadService->updateLeadStage($lead, 'consultation', $lead->assigned_to);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Consultation appointment confirmed successfully.',
                'appointment_id' => $appointment->id,
                'appointment_time' => $appointment->appointment_time->toIso8601String(),
                'lead_number' => $lead->lead_number,
                'assigned_consultant' => $lead->assignedTo?->name ?? 'Immigration Specialist',
            ], 201);
        }

        return back()->with('booking_success', [
            'lead_number' => $lead->lead_number,
            'time' => $appointmentTime->format('l, F j, Y \a\t h:i A'),
            'consultant' => $lead->assignedTo?->name ?? 'Senior Immigration Consultant',
        ]);
    }
}
