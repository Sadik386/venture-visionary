<?php

namespace App\Filament\Partner\Resources\VisaCaseResource\Pages;

use App\Filament\Partner\Resources\VisaCaseResource;
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
