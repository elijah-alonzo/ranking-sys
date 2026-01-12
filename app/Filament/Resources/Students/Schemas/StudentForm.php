<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Student Information')
                    ->description('Create or edit student account details')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->prefixIcon('heroicon-o-user')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter full name')
                            ->columnSpan(1),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->prefixIcon('heroicon-o-envelope')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter email address')
                            ->columnSpan(1),

                        TextInput::make('contact_number')
                            ->label('Contact Number')
                            ->prefixIcon('heroicon-o-phone')
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('Enter contact number')
                            ->columnSpan(1),

                        FileUpload::make('pfp')
                            ->label('Profile Picture')
                            ->image()
                            ->directory('profile-pictures')
                            ->imageEditor()
                            ->imagePreviewHeight('100')
                            ->maxSize(2048)
                            ->columnSpan(1),

                        Textarea::make('bio')
                            ->label('Biography')
                            ->placeholder('Enter student bio')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->extraAttributes(['class' => 'mb-6']),

                Section::make('Security & Privacy')
                    ->description('Set up password for the student account')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('password')
                                    ->label('Password')
                                    ->prefixIcon('heroicon-o-lock-closed')
                                    ->password()
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->minLength(8)
                                    ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->hiddenOn('view')
                                    ->revealable()
                                    ->placeholder('Minimum 8 characters'),
                            ]),
                    ]),
            ]);
    }
}
