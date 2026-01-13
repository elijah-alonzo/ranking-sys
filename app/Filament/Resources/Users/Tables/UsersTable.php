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
                \Filament\Tables\Columns\ImageColumn::make('pfp')
                    ->label(' ')
                    ->circular()
                    ->size(40)
                    ->grow(false)
                    ->alignCenter()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF')
                    ->extraAttributes(['class' => 'ring-1 ring-gray-100 dark:ring-gray-800']),

                \Filament\Tables\Columns\TextColumn::make('name')
                    ->weight('medium')
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope')
                    ->label('Email'),

                \Filament\Tables\Columns\TextColumn::make('contact_number')
                    ->icon('heroicon-o-phone')
                    ->label('Contact')
                    ->placeholder('No contact')
                    ->copyable(),

                \Filament\Tables\Columns\TextColumn::make('council.name')
                    ->label('Council')
                    ->icon('heroicon-o-users')
                    ->badge()
                    ->color('primary'),

                \Filament\Tables\Columns\IconColumn::make('admin')
                    ->boolean()
                    ->label('Admin')
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-user')
                    ->trueColor('success')
                    ->falseColor('gray'),

                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Registered'),
            ])
            ->emptyStateHeading('No users yet')
            ->emptyStateDescription('Users will appear here once they are registered.')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
