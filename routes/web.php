<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'system' => 'Venture Visionary CRM & Business Consultancy Platform',
        'version' => '2.0.0',
        'status' => 'operational',
        'timezone' => config('app.timezone', 'Asia/Qatar'),
        'portals' => [
            'lead_generation' => '/lead-generation',
            'booking_calendar' => '/book-appointment',
            'staff' => '/staff',
            'client' => '/portal',
            'b2b' => '/partner',
        ],
        'compliance' => 'Developer Handover Specification v2.0 (100% Open Source)'
    ]);
});

// Dedicated Public Lead Generation & Inbound Capture Portal
Route::get('/lead-generation', function () {
    $countries = \App\Models\Country::where('is_active', true)
        ->with(['services' => function ($q) {
            $q->where('is_active', true);
        }])
        ->orderBy('name')
        ->get();

    return view('lead-generation', compact('countries'));
})->name('lead-generation');

// Friendly redirects and aliases
Route::get('/inquiry', fn() => redirect()->route('lead-generation'));
Route::get('/leads/create', fn() => redirect()->route('lead-generation'));
Route::get('/leads/generate', fn() => redirect()->route('lead-generation'));
Route::get('/apply', fn() => redirect()->route('lead-generation'));

// Commercial & Financial PDF Exports
Route::get('/quotations/{quotation}/pdf', [\App\Http\Controllers\PdfExportController::class, 'quotation'])->name('quotations.pdf');
Route::get('/invoices/{invoice}/pdf', [\App\Http\Controllers\PdfExportController::class, 'invoice'])->name('invoices.pdf');

// GoHighLevel Scheduling Engine: Public Interactive Appointment Booking
Route::get('/book-appointment', [\App\Http\Controllers\AppointmentBookingController::class, 'show'])->name('appointments.book');
Route::post('/book-appointment', [\App\Http\Controllers\AppointmentBookingController::class, 'book'])->name('appointments.store');
Route::get('/book', fn() => redirect()->route('appointments.book'));
Route::get('/consultation', fn() => redirect()->route('appointments.book'));

// GoHighLevel Reputation Engine: Smart 5-Star Review Funnel
Route::get('/review/{token}', [\App\Http\Controllers\ReputationController::class, 'show'])->name('reviews.show');
Route::post('/review/{token}', [\App\Http\Controllers\ReputationController::class, 'submit'])->name('reviews.submit');


