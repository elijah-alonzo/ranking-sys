<?php

namespace App\Filament\Resources\Accounts\Pages;

use App\Filament\Resources\Accounts\AccountResource;
use Filament\Resources\Pages\ListRecords;

class IndexAccounts extends ListRecords
{
    protected static string $resource = AccountResource::class;

    public function mount(): void
    {
        // Redirect directly to view the user's account
        redirect(AccountResource::getUrl('view', ['record' => auth()->id()]));
    }
}
