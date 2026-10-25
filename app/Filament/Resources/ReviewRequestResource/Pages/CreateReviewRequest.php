<?php

namespace App\Filament\Resources\ReviewRequestResource\Pages;

use App\Filament\Resources\ReviewRequestResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateReviewRequest extends CreateRecord
{
    protected static string $resource = ReviewRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['token'] = Str::random(32);
        $data['sent_at'] = now();
        return $data;
    }
}
