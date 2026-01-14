<?php

namespace App\Filament\Resources\Accounts\Pages;

use App\Filament\Resources\Accounts\AccountResource;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Contracts\Support\Htmlable;

class ViewAccount extends ViewRecord
{
    protected static string $resource = AccountResource::class;

    protected static ?string $title = 'My Account';

    public function getTitle(): string | Htmlable
    {
        return 'My Account';
    }

    public function mount(int|string $record = null): void
    {
        $this->record = auth()->user();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('logo')
                ->label('')
                ->icon('')
                ->extraAttributes([
                    'class' => 'pointer-events-none',
                ])
                ->modalHeading('')
                ->modalDescription('')
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
                ->modalContent(
                    fn () => '<img src="/logo.png" alt="Logo" style="height:48px;max-width:160px;object-fit:contain;" />'
                )
                ->visible(true),
            Action::make('edit')
                ->label('Edit Account')
                ->url(fn (): string => AccountResource::getUrl('edit', ['record' => $this->record]))
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            // Main Header Section
            Grid::make([
                'default' => 1,
                'lg' => 3
            ])
                ->schema([
                    // Left side - Name and contact info
                    Grid::make(1)
                        ->schema([
                            TextEntry::make('name')
                                ->label('')
                                ->hiddenLabel()
                                ->size(TextSize::Large)
                                ->weight(FontWeight::Bold)
                                ->extraAttributes([
                                    'style' => 'font-size: 48px; font-weight: 800;',
                                    'class' => 'text-gray-900 dark:text-white'
                                ]),

                            // Email and Contact side by side
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('email')
                                        ->label('')
                                        ->hiddenLabel()
                                        ->icon('heroicon-s-envelope')
                                        ->iconColor('gray')
                                        ->extraAttributes([
                                            'style' => 'font-size: 16px; line-height: 1.5;',
                                            'class' => 'text-gray-700 dark:text-gray-200 flex items-center'
                                        ]),

                                    TextEntry::make('contact_number')
                                        ->label('')
                                        ->hiddenLabel()
                                        ->icon('heroicon-s-phone')
                                        ->iconColor('gray')
                                        ->extraAttributes([
                                            'style' => 'font-size: 16px; line-height: 1.5;',
                                            'class' => 'text-gray-700 dark:text-gray-200 flex items-center'
                                        ])
                                        ->placeholder('No contact number'),
                                ]),

                            // Bio
                            TextEntry::make('bio')
                                ->label('')
                                ->hiddenLabel()
                                ->extraAttributes([
                                    'style' => 'font-size: 16px; line-height: 1.6; max-width: 500px;',
                                    'class' => 'text-gray-600 dark:text-gray-300'
                                ])
                                ->placeholder('No bio provided'),
                        ])
                        ->columnSpan([
                            'default' => 'full',
                            'lg' => 2
                        ]),

                    // Right side - Profile Picture
                    ImageEntry::make('pfp')
                        ->label('')
                        ->hiddenLabel()
                        ->height(250)
                        ->width(250)
                        ->circular()
                        ->alignCenter()
                        ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name ?? 'User'))
                        ->columnSpan([
                            'default' => 'full',
                            'lg' => 1
                        ]),
                ])
                ->extraAttributes([
                    'style' => 'padding: 48px 0; align-items: start;',
                ])
                ->columnSpanFull(),
        ]);
    }
}