<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')->required(),
                \Filament\Forms\Components\TextInput::make('id_num')->required(),
                \Filament\Forms\Components\TextInput::make('email')->email()->required(),
                \Filament\Forms\Components\TextInput::make('contact_number'),
                \Filament\Forms\Components\Toggle::make('admin'),
                \Filament\Forms\Components\Select::make('council_id')->relationship('council', 'name')->required(),
                \Filament\Forms\Components\TextInput::make('password')->password()->dehydrateStateUsing(fn($state) => !empty($state) ? \Hash::make($state) : null)->required(fn($context) => $context === 'create'),
                \Filament\Forms\Components\TextInput::make('pfp'),
            ]);
    }
}
