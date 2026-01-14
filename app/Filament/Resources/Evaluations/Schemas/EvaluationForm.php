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
                            ->relationship('council', 'name', fn ($query) => $query->where('is_active', true))
                            ->required()
                            ->prefixIcon('heroicon-m-building-office')
                            ->placeholder('Select a council'),
                        \Filament\Forms\Components\Select::make('council_adviser_id')
                            ->label('Council Adviser')
                            ->relationship('adviser', 'name', fn ($query) => $query->whereIn('role', ['admin', 'adviser']))
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                            ->required()
                            ->prefixIcon('heroicon-m-user-circle'),
                        \Filament\Forms\Components\TextInput::make('academic_year')
                            ->label('Academic Year')
                            ->required()
                            ->placeholder('e.g., 2024-2025')
                            ->prefixIcon('heroicon-m-calendar-days'),
                    ])
                    ->columns(2)
                    ->extraAttributes(['class' => 'mb-6']),
            ]);
    }
}
