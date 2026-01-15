<?php

namespace App\Filament\Resources\MyEvaluations\Pages;

use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMyEvaluation extends ViewRecord
{
    protected static string $resource = MyEvaluationResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make(),
        ];
    }
}
