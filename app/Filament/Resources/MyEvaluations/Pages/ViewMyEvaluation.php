<?php

namespace App\Filament\Resources\MyEvaluations\Pages;

use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use App\Models\EvaluationPeerEvaluator;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewMyEvaluation extends ViewRecord
{
    protected static string $resource = MyEvaluationResource::class;

    protected static ?string $title = 'My Evaluations';
    
    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $record = $this->getRecord();
        
        $actions = [];
        
        // Council adviser can edit (add students)
        if ($user && $record && $record->council_adviser_id === $user->id) {
            $actions[] = \Filament\Actions\EditAction::make()
                ->label('Add Student Officers');
        }
        
        // Students: No header actions for self/peer evaluation (handled in table row)
        if ($user && $record && $user->role === 'student') {
            // No header actions for students
        }
        
        return $actions;
    }
    
    protected function getPeerEvaluationActions($record, array $peerEvaluateeIds): array
    {
        $actions = [];
        
        // Get user details for each peer evaluatee
        $evaluatees = $record->users()
            ->whereIn('user_id', $peerEvaluateeIds)
            ->get();
            
        foreach ($evaluatees as $evaluatee) {
            $actions[] = Action::make("evaluate_peer_{$evaluatee->id}")
                ->label("Evaluate {$evaluatee->name}")
                ->icon('heroicon-o-clipboard-document-check')
                ->url(fn() => MyEvaluationResource::getUrl('evaluate-student', ['evaluation' => $record->id, 'user' => $evaluatee->id, 'type' => 'peer']))
                ->tooltip("Complete peer evaluation for {$evaluatee->name}");
        }
        
        return $actions;
    }
}
