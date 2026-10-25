<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('pipeline')
                ->label('Visual Pipeline (Kanban)')
                ->icon('heroicon-o-view-columns')
                ->color('amber')
                ->url('/staff/lead-pipeline-board'),
            Actions\CreateAction::make(),
        ];
    }
}
