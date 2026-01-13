<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Filament\Resources\Evaluations\EvaluationResource;
use Filament\Resources\Pages\Page;

class ViewEvaluation extends Page
{
    protected static string $resource = EvaluationResource::class;

    protected string $view = 'filament.resources.evaluations.pages.view-evaluation';
}
