<?php

namespace App\Filament\Client\Resources\VisaCaseResource\Pages;

use App\Filament\Client\Resources\VisaCaseResource;
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
