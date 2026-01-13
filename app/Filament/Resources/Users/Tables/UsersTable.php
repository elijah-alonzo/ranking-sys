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
            ->recordUrl(fn ($record) => \App\Filament\Resources\Users\UserResource::getUrl('edit', ['record' => $record]))
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

                \Filament\Tables\Columns\IconColumn::make('role')
                    ->label('Role')
                    ->icon(fn (string $state): string => match ($state) {
                        'admin' => 'heroicon-o-shield-check',
                        'adviser' => 'heroicon-o-academic-cap',
                        'student' => 'heroicon-o-user',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'gray',
                        'adviser' => 'gray',
                        'student' => 'gray',
                        default => 'gray',
                    })
                    ->tooltip(fn (string $state): string => ucfirst($state)),

                \Filament\Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Registered'),
            ])
            ->emptyStateHeading('No users yet')
            ->emptyStateDescription('Users will appear here once they are registered.')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'adviser' => 'Adviser',
                        'student' => 'Student',
                    ]),
                \Filament\Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
