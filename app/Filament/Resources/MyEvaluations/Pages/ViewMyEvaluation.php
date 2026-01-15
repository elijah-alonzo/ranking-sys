<?php

namespace App\Filament\Resources\MyEvaluations\Pages;

use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMyEvaluation extends ViewRecord
{
    protected static string $resource = MyEvaluationResource::class;

    protected static ?string $title = 'My Evaluations';
    
    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $record = $this->getRecord();
        
        // Only show edit button if current user is the council adviser for this evaluation
        if ($user && $record && $record->council_adviser_id === $user->id) {
            return [
                \Filament\Actions\EditAction::make()
                ->label('Add Student Officers'),
            ];
        }
        
        return [];
    }
}
