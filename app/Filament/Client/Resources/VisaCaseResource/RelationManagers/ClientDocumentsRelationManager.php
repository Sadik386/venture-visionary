<?php

namespace App\Filament\Client\Resources\VisaCaseResource\RelationManagers;

use App\Models\CaseDocument;
use App\Models\TimelineEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClientDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'My Uploaded Documents & Slots';

    protected static ?string $icon = 'heroicon-o-document-arrow-up';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('title')
                    ->label('Document Type / Slot')
                    ->options([
                        'Passport Biodata Page'               => 'Passport Biodata Page',
                        'Qatar ID (QID) - Front & Back'      => 'Qatar ID (QID) - Front & Back',
                        '6-Month Bank Statement'              => '6-Month Official Bank Statement',
                        'Salary Certificate / Employer NOC'   => 'Salary Certificate / Employer NOC',
                        'Passport Size Photograph'            => 'Recent Passport Size Photograph',
                        'Travel Itinerary / Flight Ticket'    => 'Travel Itinerary / Flight Reservation',
                        'Hotel Booking / Accommodation Proof' => 'Hotel Booking / Accommodation Proof',
                        'Supporting Document'                 => 'Other Supporting Document',
                    ])
                    ->required(),

                FileUpload::make('file_path')
                    ->label('Upload File (PDF / Images)')
                    ->disk('public')
                    ->directory('client-uploads')
                    ->visibility('public')
                    ->maxSize(15360) // 15MB
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                    ->required(),

                DatePicker::make('expiry_date')
                    ->label('Document Expiration Date (if applicable)'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label('Document')
                    ->weight('bold'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'verified'  => 'success',
                        'rejected'  => 'danger',
                        'uploaded'  => 'warning',
                        'requested' => 'gray',
                        default     => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'verified'  => 'Approved (QC Passed)',
                        'rejected'  => 'Correction Needed',
                        'uploaded'  => 'Under Verification',
                        'requested' => 'Upload Required',
                        default     => ucfirst($state),
                    }),

                TextColumn::make('rejection_reason')
                    ->label('Officer Feedback')
                    ->placeholder('None - Document looks good')
                    ->color('danger')
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Uploaded At')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Upload Document')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['uploaded_by_type'] = 'client';
                        $data['uploaded_by_id'] = (string) Auth::id();
                        $data['status'] = 'uploaded';
                        $data['version'] = 1;
                        if (!empty($data['file_path'])) {
                            $data['file_name'] = basename($data['file_path']);
                        }
                        return $data;
                    })
                    ->after(function (CaseDocument $record) {
                        TimelineEvent::create([
                            'case_id' => $record->case_id,
                            'actor_id' => Auth::id(),
                            'event_type' => 'document_uploaded',
                            'title' => "Applicant Uploaded: {$record->title}",
                            'description' => "Uploaded file awaiting staff QC verification.",
                            'is_client_visible' => true,
                        ]);

                        Notification::make()
                            ->title('Document Uploaded Successfully')
                            ->body('Our visa officer will review and verify your document.')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Action::make('view_document')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (CaseDocument $record): string => Storage::disk('public')->url($record->file_path))
                    ->openUrlInNewTab()
                    ->visible(fn (CaseDocument $record) => !empty($record->file_path)),
            ]);
    }
}
