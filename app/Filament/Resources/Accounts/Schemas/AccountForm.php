<?php

namespace App\Filament\Resources\Accounts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Information')
                    ->description('Update your account details and profile information')
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('pfp')
                            ->label('Profile Picture')
                            ->image()
                            ->directory('profile-pictures')
                            ->imageEditor()
                            ->imagePreviewHeight('100')
                            ->maxSize(2048)
                            ->columnSpan(2),

                        TextInput::make('name')
                            ->label('Name')
                            ->prefixIcon('heroicon-o-user')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter full name')
                            ->columnSpan(1),

                        TextInput::make('email')
                            ->label('Email address')
                            ->prefixIcon('heroicon-o-envelope')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Enter email address')
                            ->columnSpan(1),

                        TextInput::make('contact_number')
                            ->label('Contact Number')
                            ->prefixIcon('heroicon-o-phone')
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('Enter contact number')
                            ->columnSpan(1),

                        Textarea::make('bio')
                            ->label('Biography')
                            ->placeholder('Enter your bio')
                            ->rows(3)
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->extraAttributes(['class' => 'mb-6']),
            ]);
    }
}
