<?php

namespace App\Filament\Resources\Councils\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CouncilForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Council Information')
                    ->description('Create or edit council details')
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Council Logo')
                            ->image()
                            ->directory('council-logos')
                            ->imageEditor()
                            ->imagePreviewHeight('100')
                            ->maxSize(2048)
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->label('Council Name')
                            ->prefixIcon('heroicon-o-building-office')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter council name')
                            ->columnSpan(1),

                        TextInput::make('code')
                            ->label('Council Code')
                            ->prefixIcon('heroicon-o-hashtag')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('Enter council code')
                            ->columnSpan(1),
                        
                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Enter council description')
                            ->rows(3)
                            ->columnSpan(1),
                            
                        Toggle::make('is_active')
                            ->label('Is Active')
                            ->inline(false)
                            ->helperText('Activate or deactivate this council')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->extraAttributes(['class' => 'mb-6']),
            ]);
    }
}
