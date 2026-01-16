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
        
        // Students can access evaluations
        if ($user && $record && $user->role === 'student') {
            // Check if user is participating in this evaluation
            $isParticipating = $record->users()->where('user_id', $user->id)->exists();
            
            if ($isParticipating) {
                // Self evaluation
                $actions[] = Action::make('self_evaluation')
                    ->label('Self Evaluation')
                    ->icon('heroicon-o-user-circle')
                    ->color('success')
                    ->url(fn() => $record->getEvaluationUrl($user->id, 'self'))
                    ->tooltip('Complete your self evaluation');
                    
                // Peer evaluations
                $peerEvaluatees = EvaluationPeerEvaluator::getEvaluatableUsers($record->id, $user->id);
                
                if (!empty($peerEvaluatees)) {
                    $actions[] = Action::make('peer_evaluations')
                        ->label('Peer Evaluations')
                        ->icon('heroicon-o-user-group')
                        ->color('warning')
                        ->actions($this->getPeerEvaluationActions($record, $peerEvaluatees))
                        ->tooltip('Evaluate your assigned peers');
                }
            }
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
                ->url(fn() => $record->getEvaluationUrl($evaluatee->id, 'peer'))
                ->tooltip("Complete peer evaluation for {$evaluatee->name}");
        }
        
        return $actions;
    }
}
