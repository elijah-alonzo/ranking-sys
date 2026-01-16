<?php

namespace App\Filament\Resources\MyEvaluations\RelationManagers;

use App\Models\User;
use App\Models\EvaluationPeerEvaluator;
use App\Models\EvaluationForm as EvaluationFormModel;
use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
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
            ColumnGroup::make('Evaluation Scores', [
                TextColumn::make('self_score')
                    ->label('Self')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'self'))
                    ->tooltip('Self evaluation score'),
                TextColumn::make('peer_score')
                    ->label('Peer')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'peer'))
                    ->tooltip('Peer evaluation score'),
                TextColumn::make('adviser_score')
                    ->label('Adviser')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'adviser'))
                    ->tooltip('Adviser evaluation score'),
            ]),
        ];
    }

    protected function getHeaderActions(): array
    {
        if (!$this->isCouncilAdviser()) {
            return [];
        }

        return [
            AttachAction::make()
                ->label('Add Student')
                ->color('info')
                ->form($this->getAttachForm())
                ->preloadRecordSelect()
                ->modalHeading('Add Student to Evaluation')
                ->modalDescription('Add a new student and optionally assign peer evaluatees')
                ->modalWidth('lg')
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
                })
                ->after(function (AttachAction $action, array $data, $record) {
                    // Assign peer evaluatees if provided
                    if (isset($data['peer_evaluatees']) && !empty($data['peer_evaluatees'])) {
                        $this->assignPeerEvaluatees($data['recordId'], $data['peer_evaluatees']);
                    }
                }),
        ];
    }

    protected function getTableActions(): array
    {
        $user = auth()->user();
        $isAdviser = $this->isCouncilAdviser();
        $isStudent = $user && $user->role === 'student';

        $actions = [];

        // Adviser: can evaluate any student
        if ($isAdviser) {
            $actions[] = Action::make('evaluate')
                ->label('Evaluate')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('success')
                ->size('sm')
                ->url(fn ($record) => $this->ownerRecord->getEvaluationUrl($record->id, 'adviser'))
                ->tooltip('Complete adviser evaluation for this student');
        }

        // Student: can self-evaluate and peer-evaluate assigned students
        if ($isStudent) {
            $actions[] = Action::make('evaluate')
                ->label('Evaluate')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('success')
                ->size('sm')
                ->url(function ($record) use ($user) {
                    if ($user->id === $record->id) {
                        return $this->ownerRecord->getEvaluationUrl($record->id, 'self');
                    }
                    // Check if current user is assigned as peer evaluator for this student
                    $isPeerEvaluator = \App\Models\EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->where('evaluator_user_id', $user->id)
                        ->where('evaluatee_user_id', $record->id)
                        ->exists();
                    if ($isPeerEvaluator) {
                        return $this->ownerRecord->getEvaluationUrl($record->id, 'peer');
                    }
                    return null;
                })
                ->tooltip(function ($record) use ($user) {
                    if ($user->id === $record->id) {
                        return 'Complete your self evaluation';
                    }
                    $isPeerEvaluator = \App\Models\EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->where('evaluator_user_id', $user->id)
                        ->where('evaluatee_user_id', $record->id)
                        ->exists();
                    if ($isPeerEvaluator) {
                        return 'Complete peer evaluation for this student';
                    }
                    return 'You are not assigned to evaluate this student';
                })
                ->disabled(function ($record) use ($user) {
                    if ($user->id === $record->id) {
                        return false;
                    }
                    $isPeerEvaluator = \App\Models\EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->where('evaluator_user_id', $user->id)
                        ->where('evaluatee_user_id', $record->id)
                        ->exists();
                    return !$isPeerEvaluator;
                });
        }

        // Edit/Remove actions for adviser only
        if ($isAdviser) {
            $actions[] = EditAction::make()
                ->color('info')
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
                ->modalWidth('lg');

            $actions[] = DetachAction::make()
                ->label('Remove')
                ->color('danger')
                ->after(function ($record) {
                    // Remove all peer evaluator assignments for this student in this evaluation
                    EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->where(function ($query) use ($record) {
                            $query->where('evaluatee_user_id', $record->id)
                                  ->orWhere('evaluator_user_id', $record->id);
                        })
                        ->delete();
                });
        }

        return $actions;
    }

    protected function getAttachForm(): array
    {
        return [
            Select::make('recordId')
                ->label('Student')
                ->options(function () {
                    return User::where('role', 'student')
                        ->whereNotIn('id', $this->ownerRecord->users->pluck('id'))
                        ->pluck('name', 'id');
                })
                ->searchable()
                ->required()
                ->placeholder('Select a student')
                ->prefixIcon('heroicon-m-user'),
                
            TextInput::make('position')
                ->label('Position')
                ->required()
                ->maxLength(255)
                ->placeholder('e.g., President, Secretary, Member')
                ->prefixIcon('heroicon-m-identification'),

            CheckboxList::make('peer_evaluatees')
                ->label('Assign Peer Evaluatees')
                ->options(function () {
                    // Get all current students in the evaluation
                    return $this->ownerRecord->users()
                        ->pluck('name', 'users.id')
                        ->toArray();
                })
                ->columns(2)
                ->helperText('Optional: Select which students this peer evaluator will evaluate. You can also assign this later through the edit action'),
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

    /**
     * Get the evaluation score for a user and evaluator type
     */
    protected function getEvaluationScore(int $userId, string $evaluatorType): string
    {
        $score = EvaluationFormModel::where('evaluation_id', $this->ownerRecord->id)
            ->where('user_id', $userId)
            ->where('evaluator_type', $evaluatorType)
            ->first();

        if ($score && $score->evaluator_score !== null) {
            return number_format($score->evaluator_score, 2);
        }
        
        return '-';
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

    /**
     * Check if current user is the council adviser for this evaluation
     */
    protected function isCouncilAdviser(): bool
    {
        $user = auth()->user();
        return $user && $this->ownerRecord->council_adviser_id === $user->id;
    }
}