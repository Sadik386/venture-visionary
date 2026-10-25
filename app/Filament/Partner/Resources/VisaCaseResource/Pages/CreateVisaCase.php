<?php

namespace App\Filament\Partner\Resources\VisaCaseResource\Pages;

use App\Filament\Partner\Resources\VisaCaseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVisaCase extends CreateRecord
{
    protected static string $resource = VisaCaseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $partnerUser = \App\Models\PartnerUser::where('email', $user?->email)->first();
        if ($partnerUser) {
            $data['partner_id'] = $partnerUser->partner_id;
        }

        return $data;
    }
}
