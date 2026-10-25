<?php

namespace App\Filament\Resources\VisaCaseResource\Pages;

use App\Filament\Resources\VisaCaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVisaCases extends ListRecords
{
    protected static string $resource = VisaCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
