<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Settings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Profile Settings';

    protected static ?int $navigationSort = 900;

    public static function getNavigationUrl(): string
    {
        return url('/admin/settings');
    }
}
