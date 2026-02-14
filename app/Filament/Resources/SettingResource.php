<?php

namespace App\Filament\Resources;

use App\Models\Setting;
use App\Filament\Resources\SettingResource\Pages;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static ?string $navigationLabel = 'Settings';
    protected static ?string $modelLabel = 'Setting';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('key')
                    ->label('Setting Key')
                    ->required()
                    ->placeholder('e.g., platform.name'),
                
                TextInput::make('value')
                    ->label('Value')
                    ->required(),
                
                Select::make('type')
                    ->label('Type')
                    ->options([
                        'string' => 'String',
                        'boolean' => 'Boolean',
                        'integer' => 'Integer',
                        'float' => 'Float',
                        'array' => 'Array',
                        'json' => 'JSON',
                    ])
                    ->default('string'),
                
                TextInput::make('category')
                    ->label('Category')
                    ->placeholder('e.g., platform, payment'),
                
                Textarea::make('description')
                    ->label('Description'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('value')
                    ->limit(50),
                
                BadgeColumn::make('type')
                    ->sortable(),
                
                TextColumn::make('category')
                    ->badge()
                    ->sortable(),
                
                TextColumn::make('updated_at')
                    ->dateTime('M d, Y H:i'),
            ])
            ->filters([
                SelectFilter::make('type'),
                SelectFilter::make('category'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
