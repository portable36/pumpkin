<?php

namespace App\Filament\Resources\Vendors\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('store_name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Toggle::make('approved')
                    ->required(),
                DateTimePicker::make('approved_at'),
            ]);
    }
}
