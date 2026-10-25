<?php

namespace App\Filament\Resources\VisaCaseResource\Pages;

use App\Filament\Resources\VisaCaseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVisaCase extends EditRecord
{
    protected static string $resource = VisaCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
