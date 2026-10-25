<?php

namespace App\Http\Controllers;

use App\Models\ReviewRequest;
use App\Services\ReputationService;
use Illuminate\Http\Request;

class ReputationController extends Controller
{
    /**
     * Display public interactive review page for client.
     */
    public function show(string $token)
    {
        $review = ReviewRequest::where('token', $token)->firstOrFail();

        return view('reputation.review', compact('review'));
    }

    /**
     * Submit star rating and optional testimonial.
     */
    public function submit(Request $request, string $token, ReputationService $reputationService)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:2000',
        ]);

        $result = $reputationService->processFeedback(
            $token,
            (int) $validated['rating'],
            $validated['feedback'] ?? null
        );

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->route('reviews.show', ['token' => $token])->with('feedback_result', $result);
    }
}
