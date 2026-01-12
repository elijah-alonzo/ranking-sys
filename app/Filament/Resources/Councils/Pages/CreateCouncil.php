<?php

namespace App\Filament\Resources\Councils\Pages;

use App\Filament\Resources\Councils\CouncilResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCouncil extends CreateRecord
{
    protected static string $resource = CouncilResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
