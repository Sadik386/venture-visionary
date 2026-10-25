<?php

namespace App\Filament\Partner\Resources\VisaCaseResource\Pages;

use App\Filament\Partner\Resources\VisaCaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVisaCase extends ViewRecord
{
    protected static string $resource = VisaCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
