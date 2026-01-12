<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                \Filament\Tables\Columns\TextColumn::make('id_num')->searchable()->sortable(),
                \Filament\Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                \Filament\Tables\Columns\TextColumn::make('contact_number'),
                \Filament\Tables\Columns\IconColumn::make('admin')->boolean(),
                \Filament\Tables\Columns\TextColumn::make('council.name')->label('Council'),
                \Filament\Tables\Columns\TextColumn::make('pfp'),
                \Filament\Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
