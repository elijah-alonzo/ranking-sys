<?php

namespace App\Filament\Resources\Councils\Schemas;

use Filament\Schemas\Schema;

class CouncilForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')->required(),
                \Filament\Forms\Components\TextInput::make('code')->required(),
                \Filament\Forms\Components\Toggle::make('is_active')->label('Is Active'),
                \Filament\Forms\Components\Textarea::make('description'),
                \Filament\Forms\Components\TextInput::make('logo'),
            ]);
    }
}
