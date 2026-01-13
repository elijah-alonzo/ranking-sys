<?php

namespace App\Filament\Resources\Accounts\Pages;

use App\Filament\Resources\Accounts\AccountResource;
use Filament\Resources\Pages\EditRecord;

class EditAccount extends EditRecord
{
    protected static string $resource = AccountResource::class;

    public function mount(int|string $record = null): void
    {
        $this->record = auth()->user();
    }

    protected function getRedirectUrl(): string
    {
        return AccountResource::getUrl('view', ['record' => $this->record]);
    }
}
