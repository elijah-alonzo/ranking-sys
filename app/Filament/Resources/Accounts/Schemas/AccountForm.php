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
                // Account Information Section
                Section::make('Account Information')
                    ->description('Update your account details and profile information')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Profile Picture - Left Column
                                FileUpload::make('pfp')
                                    ->label('Profile Picture')
                                    ->image()
                                    ->directory('profile-pictures')
                                    ->maxSize(2048)
                                    ->imagePreviewHeight('200')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('1:1')
                                    ->imageResizeTargetWidth('300')
                                    ->imageResizeTargetHeight('300')
                                    ->loadingIndicatorPosition('center')
                                    ->panelLayout('integrated')
                                    ->removeUploadedFileButtonPosition('top-right')
                                    ->uploadButtonPosition('center')
                                    ->uploadProgressIndicatorPosition('center')
                                    ->columnSpan(1),

                                // Basic Info - Right Column
                                Grid::make(1)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Full Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->prefixIcon('heroicon-m-user')
                                            ->placeholder('Enter your full name')
                                            ->extraAttributes([
                                                'style' => 'font-size: 18px;'
                                            ]),

                                        TextInput::make('email')
                                            ->label('Email Address')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->prefixIcon('heroicon-m-envelope')
                                            ->placeholder('Enter your email address')
                                            ->extraAttributes([
                                                'style' => 'font-size: 16px;'
                                            ]),

                                        TextInput::make('contact_number')
                                            ->label('Contact Number')
                                            ->tel()
                                            ->maxLength(255)
                                            ->prefixIcon('heroicon-m-phone')
                                            ->placeholder('Enter your contact number'),
                                    ])
                                    ->columnSpan(1),
                            ]),

                        // Bio - Full Width Below
                        Grid::make(1)
                            ->schema([
                                Textarea::make('bio')
                                    ->label('Bio')
                                    ->rows(4)
                                    ->maxLength(500)
                                    ->placeholder('Tell us about yourself...')
                                    ->extraAttributes([
                                        'style' => 'resize: vertical;'
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->extraAttributes([
                        'class' => 'pb-6'
                    ]),
            ]);
    }
}
