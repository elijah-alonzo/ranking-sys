<?php

namespace App\Filament\Resources\Accounts;

use App\Filament\Resources\Accounts\Pages\EditAccount;
use App\Filament\Resources\Accounts\Pages\IndexAccounts;
use App\Filament\Resources\Accounts\Pages\ViewAccount;
use App\Filament\Resources\Accounts\Schemas\AccountForm;
use App\Models\Account;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    public static function form(Schema $schema): Schema
    {
        return AccountForm::configure($schema);
    }

    protected static UnitEnum|string|null $navigationGroup = 'Personal Management';

    protected static ?int $navigationSort = 2;

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => IndexAccounts::route('/'),
            'view' => ViewAccount::route('/{record}'),
            'edit' => EditAccount::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    protected static ?string $navigationLabel = 'Account';

    public static function canView($record): bool
    {
        return auth()->check() && auth()->id() === $record->id;
    }

    public static function canEdit($record): bool
    {
        return auth()->check() && auth()->id() === $record->id;
    }

    public static function canCreate(): bool
    {
        return false; // Users can't create accounts
    }

    public static function canDelete($record): bool
    {
        return false; // Users can't delete their accounts
    }
}
