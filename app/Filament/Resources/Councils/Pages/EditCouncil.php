<?php

namespace App\Filament\Resources\Councils\Pages;

use App\Filament\Resources\Councils\CouncilResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCouncil extends EditRecord
{
    protected static string $resource = CouncilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
