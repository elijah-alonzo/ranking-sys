<?php

namespace App\Filament\Resources\Evaluations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class EvaluationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => \App\Filament\Resources\Evaluations\EvaluationResource::getUrl('view', ['record' => $record]))
            ->modifyQueryUsing(function ($query) {
                $user = auth()->user();
                if ($user && $user->role === 'adviser') {
                    $query->where('council_adviser_id', $user->id);
                }
                return $query;
            })
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('council.logo')
                    ->label(' ')
                    ->circular()
                    ->size(40)
                    ->grow(false)
                    ->alignCenter()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->council->name ?? 'Council') . '&color=7F9CF5&background=EBF4FF')
                    ->extraAttributes(['class' => 'ring-1 ring-gray-100 dark:ring-gray-800']),
                
                \Filament\Tables\Columns\TextColumn::make('council.name')
                    ->label('Council'),
                    
                \Filament\Tables\Columns\TextColumn::make('adviser.name')
                    ->label('Adviser'),
                    
                \Filament\Tables\Columns\ImageColumn::make('students_images')
                    ->label('Students')
                    ->stacked()
                    ->limit(4)
                    ->limitedRemainingText()
                    ->circular()
                    ->getStateUsing(function ($record) {
                        return $record->users->map(function ($user) {
                            return $user->pfp ?: 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF';
                        })->toArray();
                    })
                    ->tooltip(function ($record) {
                        $userNames = $record->users->pluck('name')->toArray();
                        if (empty($userNames)) {
                            return 'No students assigned';
                        }
                        return 'Students: ' . implode(', ', $userNames);
                    }),
                    
                \Filament\Tables\Columns\TextColumn::make('academic_year')
                    ->label('Academic Year'),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('academic_year')
                    ->label('Academic Year')
                    ->options(function () {
                        return \App\Models\Evaluation::distinct()
                            ->pluck('academic_year', 'academic_year')
                            ->sort()
                            ->toArray();
                    })
                    ->placeholder('All Years')
                    ->searchable(),
                    
                \Filament\Tables\Filters\SelectFilter::make('adviser')
                    ->label('Adviser')
                    ->relationship('adviser', 'name')
                    ->placeholder('All Advisers')
                    ->searchable()
                    ->preload(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
