<?php

namespace App\Filament\Resources\VisaCaseResource\RelationManagers;

use App\Models\CaseDocument;
use App\Models\TimelineEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Case Document Vault (QC Lifecycle)';

    protected static ?string $icon = 'heroicon-o-document-duplicate';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Document Title / Slot')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Passport Bio Page, QID, Salary Certificate'),

                FileUpload::make('file_path')
                    ->label('Document File')
                    ->disk('public')
                    ->directory('case-documents')
                    ->visibility('public')
                    ->maxSize(15360) // 15MB
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                    ->required()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state instanceof \Illuminate\Http\UploadedFile) {
                            $set('file_name', $state->getClientOriginalName());
                            $set('mime_type', $state->getMimeType());
                            $set('file_size', $state->getSize());
                        }
                    }),

                DatePicker::make('expiry_date')
                    ->label('Document Expiration Date')
                    ->helperText('Track passport / QID / police clearance validity'),

                Select::make('status')
                    ->options([
                        'requested' => 'Requested from Client',
                        'uploaded'  => 'Uploaded (Pending Review)',
                        'verified'  => 'Verified (QC Approved)',
                        'rejected'  => 'Rejected (QC Failed)',
                    ])
                    ->default('uploaded')
                    ->required(),

                Textarea::make('rejection_reason')
                    ->label('Rejection Reason / Client Feedback')
                    ->rows(2)
                    ->columnSpanFull()
                    ->visible(fn (Forms\Get $get) => $get('status') === 'rejected'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label('Document')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'verified'  => 'success',
                        'rejected'  => 'danger',
                        'uploaded'  => 'warning',
                        'requested' => 'gray',
                        default     => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('expiry_date')
                    ->label('Expiry Date')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn (CaseDocument $record) => $record->expiry_date && $record->expiry_date->isPast() ? 'danger' : null),

                TextColumn::make('verifiedBy.name')
                    ->label('QC Officer')
                    ->placeholder('Unverified'),

                TextColumn::make('verified_at')
                    ->label('QC Timestamp')
                    ->dateTime('d M Y, H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('rejection_reason')
                    ->label('Feedback')
                    ->limit(30)
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Uploaded At')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['uploaded_by_type'] = 'staff';
                        $data['uploaded_by_id'] = (string) Auth::id();
                        $data['version'] = 1;
                        if (empty($data['file_name']) && !empty($data['file_path'])) {
                            $data['file_name'] = basename($data['file_path']);
                        }
                        return $data;
                    })
                    ->after(function (CaseDocument $record) {
                        TimelineEvent::create([
                            'case_id' => $record->case_id,
                            'actor_id' => Auth::id(),
                            'event_type' => 'document_uploaded',
                            'title' => "Document Added: {$record->title}",
                            'description' => "Uploaded file {$record->file_name}",
                            'is_client_visible' => true,
                        ]);
                    }),
            ])
            ->actions([
                Action::make('view_file')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (CaseDocument $record): string => Storage::disk('public')->url($record->file_path))
                    ->openUrlInNewTab()
                    ->visible(fn (CaseDocument $record) => !empty($record->file_path)),

                Action::make('qc_approve')
                    ->label('Approve QC')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Document (QC Pass)')
                    ->modalDescription('Confirm this document meets all embassy compliance and quality checks.')
                    ->visible(fn (CaseDocument $record) => $record->status !== 'verified')
                    ->action(function (CaseDocument $record) {
                        $record->update([
                            'status' => 'verified',
                            'verified_by_id' => Auth::id(),
                            'verified_at' => now(),
                            'rejection_reason' => null,
                        ]);

                        TimelineEvent::create([
                            'case_id' => $record->case_id,
                            'actor_id' => Auth::id(),
                            'event_type' => 'document_verified',
                            'title' => "QC Verified: {$record->title}",
                            'description' => "Document successfully checked and approved for submission.",
                            'is_client_visible' => true,
                        ]);

                        Notification::make()
                            ->title('Document QC Approved')
                            ->success()
                            ->send();
                    }),

                Action::make('qc_reject')
                    ->label('Reject QC')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Rejection Reason / Resubmission Requirement')
                            ->placeholder('e.g. Scanned copy is blurry, passport validity must exceed 6 months, bank statement must have official stamp.')
                            ->required(),
                    ])
                    ->visible(fn (CaseDocument $record) => $record->status !== 'rejected')
                    ->action(function (CaseDocument $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'verified_by_id' => Auth::id(),
                            'verified_at' => now(),
                        ]);

                        TimelineEvent::create([
                            'case_id' => $record->case_id,
                            'actor_id' => Auth::id(),
                            'event_type' => 'document_rejected',
                            'title' => "QC Rejected: {$record->title}",
                            'description' => $data['rejection_reason'],
                            'is_client_visible' => true,
                        ]);

                        Notification::make()
                            ->title('Document Rejected with Feedback')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
