<?php

namespace App\Filament\Resources\ReviewRequestResource\Pages;

use App\Filament\Resources\ReviewRequestResource;
use App\Services\ReputationService;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListReviewRequests extends ListRecords
{
    protected static string $resource = ReviewRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('dispatch_request')
                ->label('Dispatch Review Request')
                ->icon('heroicon-o-paper-airplane')
                ->color('amber')
                ->form([
                    TextInput::make('recipient_name')
                        ->required()
                        ->label('Client Name'),
                    TextInput::make('recipient_phone')
                        ->tel()
                        ->label('WhatsApp / Mobile (+974...)'),
                    Select::make('channel')
                        ->options([
                            'whatsapp' => 'WhatsApp Messenger',
                            'sms' => 'Direct SMS',
                            'email' => 'Email',
                        ])
                        ->default('whatsapp')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $token = \Illuminate\Support\Str::random(32);
                    $googleUrl = config('services.google.review_url') 
                        ?: 'https://search.google.com/local/writereview?placeid=ChIJQV_VentureVisionaryQatar';

                    $review = \App\Models\ReviewRequest::create([
                        'token' => $token,
                        'recipient_name' => $data['recipient_name'],
                        'recipient_phone' => $data['recipient_phone'] ?? null,
                        'channel' => $data['channel'],
                        'status' => 'sent',
                        'google_review_url' => $googleUrl,
                        'sent_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Review Invitation Dispatched')
                        ->body("Review link generated for {$review->recipient_name}: /review/{$review->token}")
                        ->success()
                        ->send();
                }),
        ];
    }
}
