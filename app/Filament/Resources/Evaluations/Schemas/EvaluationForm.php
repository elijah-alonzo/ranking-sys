<?php

namespace App\Filament\Resources\Evaluations\Schemas;

use Filament\Schemas\Schema;

class EvaluationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('council_id')
                    ->label('Council')
                    ->relationship('council', 'name')
                    ->required(),
                \Filament\Forms\Components\Select::make('council_adviser_id')
                    ->label('Council Adviser')
                    ->relationship('adviser', 'name')
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name . ' (' . $record->role . ')')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('academic_year')
                    ->label('Academic Year')
                    ->required(),
            ]);
    }
}
