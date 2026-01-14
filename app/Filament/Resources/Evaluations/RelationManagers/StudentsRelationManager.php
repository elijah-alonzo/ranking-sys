<?php

namespace App\Filament\Resources\Evaluations\RelationManagers;

use App\Models\User;
use App\Models\EvaluationPeerEvaluator;
use App\Filament\Resources\Evaluations\EvaluationResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Notifications\Notification;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'users';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $title = 'Students';

    public function table(Table $table): Table
    {
        return $table
            ->columns($this->getTableColumns())
            ->headerActions($this->getHeaderActions())
            ->actions($this->getTableActions())
            ->filters([])
            ->bulkActions([])
            ->striped();
    }

    protected function getTableColumns(): array
    {
        return [
            ColumnGroup::make('Student', [
                ImageColumn::make('pfp')
                    ->label('Profile')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF'),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pivot.position')
                    ->label('Position')
                    ->placeholder('No position assigned'),
            ]),
            ColumnGroup::make('Peer Assignments', [
                TextColumn::make('peer_evaluatees_count')
                    ->label('Evaluating')
                    ->getStateUsing(fn ($record) => $this->getPeerEvaluateesCount($record->id))
                    ->tooltip('Number of peers this student will evaluate'),
                TextColumn::make('peer_evaluators_count')
                    ->label('Evaluated By')
                    ->getStateUsing(fn ($record) => $this->getPeerEvaluatorsCount($record->id))
                    ->tooltip('Number of peers who will evaluate this student'),
            ]),
        ];
    }

    protected function getHeaderActions(): array
    {
        // Only show Add Student action in Edit page, not in View page
        if ($this->pageClass === \App\Filament\Resources\Evaluations\Pages\ViewEvaluation::class) {
            return [];
        }

        return [
            AttachAction::make()
                ->label('Add Student')
                ->form($this->getAttachForm())
                ->preloadRecordSelect()
                ->before(function (AttachAction $action, array $data) {
                    // Check if user is already assigned to this evaluation
                    $existingUser = $this->ownerRecord->users()
                        ->where('user_id', $data['recordId'])
                        ->exists();
                    
                    if ($existingUser) {
                        Notification::make()
                            ->title('User Already Added')
                            ->body('This user is already assigned to this evaluation.')
                            ->warning()
                            ->send();
                        
                        $action->halt();
                    }
                }),
        ];
    }

    protected function getTableActions(): array
    {
        // Only show management actions in Edit page, not in View page
        if ($this->pageClass === \App\Filament\Resources\Evaluations\Pages\ViewEvaluation::class) {
            return [];
        }

        return [
            EditAction::make()
                ->form($this->getEditForm())
                ->action(function ($record, $data) {
                    // Update student position
                    $record->pivot->update(['position' => $data['position']]);

                    // Update peer evaluator assignments
                    if (isset($data['peer_evaluatees'])) {
                        $this->assignPeerEvaluatees($record->id, $data['peer_evaluatees']);
                    }
                })
                ->modalHeading(fn ($record) => 'Edit ' . $record->name)
                ->modalDescription('Update student details and peer evaluation assignments')
                ->modalWidth('lg'),
            DetachAction::make()
                ->label('Remove')
                ->after(function ($record) {
                    // Remove all peer evaluator assignments for this student in this evaluation
                    EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->where(function ($query) use ($record) {
                            $query->where('evaluatee_user_id', $record->id)
                                  ->orWhere('evaluator_user_id', $record->id);
                        })
                        ->delete();
                }),
        ];
    }

    protected function getAttachForm(): array
    {
        return [
            Select::make('recordId')
                ->label('Student')
                ->options(User::all()->pluck('name', 'id'))
                ->searchable()
                ->required(),
            TextInput::make('position')
                ->label('Position')
                ->required()
                ->maxLength(255),
        ];
    }

    protected function getEditForm(): array
    {
        return [
            TextInput::make('position')
                ->label('Position')
                ->required()
                ->maxLength(255)
                ->prefixIcon('heroicon-m-identification'),

            CheckboxList::make('peer_evaluatees')
                ->label('Select Peer Evaluatees')
                ->options(function ($record) {
                    // Get all students in the evaluation except the current student (evaluator)
                    $allUserIds = $this->ownerRecord->users()
                        ->where('users.id', '!=', $record->id)
                        ->pluck('users.id')
                        ->toArray();

                    $alreadyAssignedIds = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->whereIn('evaluatee_user_id', $allUserIds)
                        ->where('evaluator_user_id', '!=', $record->id)
                        ->pluck('evaluatee_user_id')
                        ->toArray();

                    // Get students not already assigned as evaluatees to another evaluator
                    $eligibleIds = array_diff($allUserIds, $alreadyAssignedIds);

                    // Always include students already assigned to this evaluator (for editing)
                    $currentAssignedIds = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->where('evaluator_user_id', $record->id)
                        ->pluck('evaluatee_user_id')
                        ->toArray();

                    $finalIds = array_unique(array_merge($eligibleIds, $currentAssignedIds));

                    return $this->ownerRecord->users()
                        ->whereIn('users.id', $finalIds)
                        ->pluck('name', 'users.id')
                        ->toArray();
                })
                ->default(function ($record) {
                    return EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->where('evaluator_user_id', $record->id)
                        ->pluck('evaluatee_user_id')
                        ->toArray();
                })
                ->columns(2)
        ];
    }

    protected function getPeerEvaluateesCount(int $userId): string
    {
        $count = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
            ->where('evaluator_user_id', $userId)
            ->count();

        return $count > 0 ? $count : '-';
    }

    protected function getPeerEvaluatorsCount(int $userId): string
    {
        $count = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
            ->where('evaluatee_user_id', $userId)
            ->count();

        return $count > 0 ? $count : '-';
    }

    protected function assignPeerEvaluatees(int $evaluatorUserId, array $evaluateeIds): void
    {
        try {
            // Remove existing assignments for this evaluator
            EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                ->where('evaluator_user_id', $evaluatorUserId)
                ->delete();

            // Create new assignments
            foreach ($evaluateeIds as $evaluateeId) {
                EvaluationPeerEvaluator::create([
                    'evaluation_id' => $this->ownerRecord->id,
                    'evaluatee_user_id' => $evaluateeId,
                    'evaluator_user_id' => $evaluatorUserId,
                    'assigned_by_user_id' => auth()->id(),
                    'assigned_at' => now(),
                ]);
            }

            $evaluatorName = User::find($evaluatorUserId)->name;
            $evaluateeNames = User::whereIn('id', $evaluateeIds)->pluck('name')->join(', ');
            
            Notification::make()
                ->title('Peer Evaluatees Assigned Successfully')
                ->body("Assigned {$evaluatorName} to evaluate: {$evaluateeNames}")
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error Assigning Peer Evaluatees')
                ->body('There was an error assigning the peer evaluatees. Please try again.')
                ->danger()
                ->send();
        }
    }
}