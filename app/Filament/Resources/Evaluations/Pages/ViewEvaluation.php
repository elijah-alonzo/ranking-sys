<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Filament\Resources\Evaluations\RelationManagers\StudentsRelationManager;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEvaluation extends ViewRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit Evaluation')
        ];
    }

    // Render the InfoList and the relation managers together
    protected function getViewContent(): array
    {
        return array_merge(
            [$this->getInfolist()],
            $this->getRelationManagerComponents()
        );
    }

    protected function getInfolist()
    {
        return $this->getResource()::infolist(app(\Filament\Schemas\Schema::class))->render($this->record);
    }

    // Override to include students relation manager
    protected function getRelationManagerComponents(): array
    {
        $studentsManager = app(StudentsRelationManager::class, [
            'ownerRecord' => $this->record,
            'pageClass' => static::class,
        ]);

        return [
            $studentsManager->render(),
        ];
    }
}
