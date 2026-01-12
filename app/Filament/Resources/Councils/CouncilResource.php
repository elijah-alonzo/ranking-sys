<?php

namespace App\Filament\Resources\Councils;

use App\Filament\Resources\Councils\Pages\CreateCouncil;
use App\Filament\Resources\Councils\Pages\EditCouncil;
use App\Filament\Resources\Councils\Pages\ListCouncils;
use App\Filament\Resources\Councils\Schemas\CouncilForm;
use App\Filament\Resources\Councils\Tables\CouncilsTable;
use App\Models\Council;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CouncilResource extends Resource
{
    protected static ?string $model = Council::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CouncilForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CouncilsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCouncils::route('/'),
            'create' => CreateCouncil::route('/create'),
            'edit' => EditCouncil::route('/{record}/edit'),
        ];
    }
}
