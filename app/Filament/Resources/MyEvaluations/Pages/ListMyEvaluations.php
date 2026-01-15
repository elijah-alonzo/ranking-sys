<?php

namespace App\Filament\Resources\MyEvaluations\Pages;

use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMyEvaluations extends ListRecords
{
    protected static string $resource = MyEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
