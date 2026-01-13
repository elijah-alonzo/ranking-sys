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
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('council.logo')
                    ->label('Logo')
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
                    
                \Filament\Tables\Columns\TextColumn::make('academic_year')
                    ->label('Academic Year'),
            ])
            ->filters([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
