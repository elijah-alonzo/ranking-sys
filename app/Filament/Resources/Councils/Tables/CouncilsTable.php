<?php

namespace App\Filament\Resources\Councils\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class CouncilsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => \App\Filament\Resources\Councils\CouncilResource::getUrl('edit', ['record' => $record]))
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('logo')
                    ->label(' ')
                    ->circular()
                    ->size(40)
                    ->grow(false)
                    ->alignCenter()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF')
                    ->extraAttributes(['class' => 'ring-1 ring-gray-100 dark:ring-gray-800']),

                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('Council Name')
                    ->weight('medium')
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('code')
                    ->label('Council Code')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                \Filament\Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime()
            ])
            ->emptyStateHeading('No councils yet')
            ->emptyStateDescription('Councils will appear here once they are created.')
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
