<?php

namespace App\Filament\Resources\Evaluations\RelationManagers;

use App\Models\EvaluationPeerEvaluator;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ColumnGroup;

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
        // This is now read-only for admin monitoring purposes
        // Student management should be done through MyEvaluations resource
        return [];
    }

    protected function getTableActions(): array
    {
        // This is now read-only for admin monitoring purposes
        // Student management should be done through MyEvaluations resource
        return [];
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
}