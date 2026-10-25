<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\ReviewRequest;
use App\Models\VisaCase;
use Illuminate\Support\Str;

class ReputationService
{
    /**
     * Dispatch an automated review request to a Lead, Client, or VisaCase applicant.
     */
    public function dispatchReviewRequest(mixed $entity, string $channel = 'whatsapp'): ReviewRequest
    {
        $token = Str::random(32);

        $leadId = null;
        $caseId = null;
        $clientId = null;
        $name = 'Valued Client';
        $phone = null;
        $email = null;

        if ($entity instanceof Lead) {
            $leadId = $entity->id;
            $name = $entity->name;
            $phone = $entity->phone ?? $entity->whatsapp;
            $email = $entity->email;
        } elseif ($entity instanceof VisaCase) {
            $caseId = $entity->id;
            $clientId = $entity->client_id;
            $name = $entity->client?->full_name ?? 'Client';
            $phone = $entity->client?->phone ?? $entity->client?->whatsapp;
            $email = $entity->client?->email;
        } elseif ($entity instanceof Client) {
            $clientId = $entity->id;
            $name = $entity->full_name;
            $phone = $entity->phone ?? $entity->whatsapp;
            $email = $entity->email;
        }

        $googleReviewUrl = config('services.google.review_url') 
            ?: 'https://search.google.com/local/writereview?placeid=ChIJQV_VentureVisionaryQatar';

        $reviewRequest = ReviewRequest::create([
            'token' => $token,
            'lead_id' => $leadId,
            'case_id' => $caseId,
            'client_id' => $clientId,
            'recipient_name' => $name,
            'recipient_phone' => $phone,
            'recipient_email' => $email,
            'channel' => $channel,
            'status' => 'sent',
            'google_review_url' => $googleReviewUrl,
            'sent_at' => now(),
        ]);

        // If it was a lead, record the activity
        if ($leadId) {
            LeadActivity::create([
                'lead_id' => $leadId,
                'activity_type' => 'whatsapp',
                'title' => 'Automated Review Request Sent',
                'details' => "Automated 5-star review invitation dispatched via {$channel}. Review link: /review/{$token}",
                'metadata' => ['token' => $token, 'channel' => $channel],
            ]);
        }

        return $reviewRequest;
    }

    /**
     * Process feedback submitted by client via the public review funnel.
     */
    public function processFeedback(string $token, int $rating, ?string $feedback = null): array
    {
        $request = ReviewRequest::where('token', $token)->firstOrFail();

        $rating = max(1, min(5, $rating));
        $isPositive = $rating >= 4;

        $request->update([
            'rating' => $rating,
            'feedback' => $feedback,
            'status' => 'completed',
            'completed_at' => now(),
            'public_review_redirected' => $isPositive,
        ]);

        if ($isPositive) {
            return [
                'status' => 'success',
                'is_positive' => true,
                'redirect_url' => $request->google_review_url,
                'message' => 'Thank you for your generous 5-star rating! Please consider copying your review to our official Google Business page.',
            ];
        }

        return [
            'status' => 'success',
            'is_positive' => false,
            'redirect_url' => null,
            'message' => 'Thank you for sharing your experience. Your feedback has been escalated directly to our Executive Quality Control team.',
        ];
    }
}
