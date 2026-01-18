<?php
namespace App\Filament\Resources\Accounts\Pages;

use App\Filament\Resources\Accounts\AccountResource;
use Filament\Resources\Pages\Page;

class ViewAccount extends Page
{
    protected static string $resource = AccountResource::class;
    protected static bool $shouldRegisterNavigation = false;
    protected string $view = 'ViewAccount';

    public $user;

    public function mount(int|string $record = null): void
    {
        $this->user = auth()->user();
    }
}