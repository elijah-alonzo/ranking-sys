<?php

namespace App\Filament\Resources\MyEvaluations\Pages;

use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMyEvaluation extends EditRecord
{
    protected static string $resource = MyEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ViewAction::make(),
        ];
    }
}
