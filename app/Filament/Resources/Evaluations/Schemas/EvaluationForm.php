<?php

namespace App\Filament\Resources\Evaluations\Schemas;

use Filament\Schemas\Schema;

class EvaluationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Evaluation Information')
                    ->description('Create or edit evaluation details')
                    ->columnSpanFull()
                    ->schema([
                        \Filament\Forms\Components\Select::make('council_id')
                            ->label('Council')
                            ->relationship('council', 'name')
                            ->required(),
                        \Filament\Forms\Components\Select::make('council_adviser_id')
                            ->label('Council Adviser')
                            ->relationship('adviser', 'name', fn ($query) => $query->whereIn('role', ['admin', 'adviser']))
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('academic_year')
                            ->label('Academic Year')
                            ->required(),
                    ])
                    ->columns(2)
                    ->extraAttributes(['class' => 'mb-6']),
            ]);
    }
}
