<?php

namespace App\Filament\Client\Resources\VisaCaseResource\RelationManagers;

use App\Models\TimelineEvent;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class ClientTimelineRelationManager extends RelationManager
{
    protected static string $relationship = 'timelineEvents';

    protected static ?string $title = 'Application Progress Timeline';

    protected static ?string $icon = 'heroicon-o-clock';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('is_client_visible', true))
            ->columns([
                TextColumn::make('title')
                    ->label('Milestone / Update')
                    ->weight('bold'),

                TextColumn::make('description')
                    ->label('Details')
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
