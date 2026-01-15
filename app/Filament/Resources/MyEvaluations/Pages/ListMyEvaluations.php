<?php

namespace App\Filament\Resources\MyEvaluations\Pages;

use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use Filament\Resources\Pages\ListRecords;

class ListMyEvaluations extends ListRecords
{
    protected static string $resource = MyEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        // Users cannot create evaluations through MyEvaluations
        return [];
    }
}
