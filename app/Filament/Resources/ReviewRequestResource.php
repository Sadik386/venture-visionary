<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewRequestResource\Pages;
use App\Models\Lead;
use App\Models\ReviewRequest;
use App\Services\ReputationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;

class ReviewRequestResource extends Resource
{
    protected static ?string $model = ReviewRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'CRM & Sales';

    protected static ?string $navigationLabel = 'Reputation & Reviews';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Review Invitation Profile')
                    ->columns(3)
                    ->schema([
                        TextInput::make('recipient_name')
                            ->required()
                            ->label('Client / Lead Name'),

                        TextInput::make('recipient_phone')
                            ->tel()
                            ->label('Recipient Phone / WhatsApp'),

                        TextInput::make('recipient_email')
                            ->email(),

                        Select::make('channel')
                            ->options([
                                'whatsapp' => 'WhatsApp Messenger',
                                'sms' => 'Direct SMS',
                                'email' => 'Email',
                            ])
                            ->default('whatsapp')
                            ->required(),

                        Select::make('status')
                            ->options([
                                'sent' => 'Sent',
                                'opened' => 'Opened',
                                'completed' => 'Completed',
                                'declined' => 'Declined',
                            ])
                            ->default('sent'),

                        Select::make('rating')
                            ->options([
                                1 => '★ (1 Star - Critical)',
                                2 => '★★ (2 Stars - Poor)',
                                3 => '★★★ (3 Stars - Average)',
                                4 => '★★★★ (4 Stars - Good)',
                                5 => '★★★★★ (5 Stars - Excellent)',
                            ])
                            ->label('Customer Rating'),
                    ]),

                Section::make('Customer Testimonial & Review Link')
                    ->schema([
                        Textarea::make('feedback')
                            ->rows(3)
                            ->label('Client Feedback / Remarks'),

                        TextInput::make('google_review_url')
                            ->url()
                            ->label('Destination Google Maps / Business Profile Link')
                            ->default('https://search.google.com/local/writereview?placeid=ChIJQV_VentureVisionaryQatar'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sent_at')
                    ->label('Dispatched')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),

                TextColumn::make('recipient_name')
                    ->label('Client')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('channel')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'whatsapp' => 'success',
                        'sms' => 'info',
                        'email' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'sent' => 'warning',
                        'opened' => 'info',
                        default => 'danger',
                    }),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(function ($state) {
                        if (! $state) return 'Pending';
                        return str_repeat('★', (int) $state) . " ({$state}/5)";
                    })
                    ->badge()
                    ->color(fn ($state): string => match ((int) $state) {
                        5, 4 => 'success',
                        3 => 'warning',
                        1, 2 => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                IconColumn::make('public_review_redirected')
                    ->label('Google Diverted')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                TextColumn::make('feedback')
                    ->label('Testimonial')
                    ->limit(40)
                    ->tooltip(fn (ReviewRequest $record): ?string => $record->feedback),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'sent' => 'Pending Review',
                        'completed' => 'Completed Feedback',
                    ]),
                SelectFilter::make('rating')
                    ->options([
                        5 => '5 Stars',
                        4 => '4 Stars',
                        3 => '3 Stars',
                        2 => '2 Stars',
                        1 => '1 Star',
                    ]),
            ])
            ->actions([
                Action::make('open_link')
                    ->label('Copy Review Link')
                    ->icon('heroicon-m-link')
                    ->color('gray')
                    ->url(fn (ReviewRequest $record): string => url("/review/{$record->token}"))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviewRequests::route('/'),
            'create' => Pages\CreateReviewRequest::route('/create'),
        ];
    }
}
