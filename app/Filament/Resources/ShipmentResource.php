<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShipmentResource\Pages;
use App\Models\Shipment;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;
     protected static string | \UnitEnum | null $navigationGroup = 'Shipment';
    protected static ?string $navigationLabel = 'Shipments';
    protected static ?string $modelLabel = 'Shipment';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('order_id')
                    ->relationship('order', 'order_number')
                    ->searchable(),

                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                Select::make('gateway')
                    ->options([
                        Shipment::GATEWAY_STEADFAST => 'Steadfast',
                        Shipment::GATEWAY_PATHAO => 'Pathao',
                    ])
                    ->required(),

                TextInput::make('tracking_number')
                    ->nullable()
                    ->unique(ignoreRecord: true),

                Select::make('status')
                    ->options([
                        Shipment::STATUS_PENDING => 'Pending',
                        Shipment::STATUS_PICKUP_SCHEDULED => 'Pickup Scheduled',
                        Shipment::STATUS_PICKED_UP => 'Picked Up',
                        Shipment::STATUS_IN_TRANSIT => 'In Transit',
                        Shipment::STATUS_OUT_FOR_DELIVERY => 'Out for Delivery',
                        Shipment::STATUS_DELIVERED => 'Delivered',
                        Shipment::STATUS_FAILED => 'Failed',
                        Shipment::STATUS_CANCELLED => 'Cancelled',
                        Shipment::STATUS_RETURNED => 'Returned',
                    ])
                    ->required(),

                TextInput::make('weight')
                    ->nullable()
                    ->numeric()
                    ->suffix('kg'),

                TextInput::make('cost')
                    ->nullable()
                    ->numeric()
                    ->suffix('BDT'),

                Textarea::make('notes')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('order.order_number')->sortable(),
                TextColumn::make('tracking_number')->sortable()->searchable(),
                TextColumn::make('gateway')->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => Shipment::STATUS_PENDING,
                        'info' => Shipment::STATUS_PICKUP_SCHEDULED,
                        'secondary' => Shipment::STATUS_PICKED_UP,
                        'primary' => Shipment::STATUS_IN_TRANSIT,
                        'warning' => Shipment::STATUS_OUT_FOR_DELIVERY,
                        'success' => Shipment::STATUS_DELIVERED,
                        'danger' => Shipment::STATUS_FAILED,
                        'gray' => Shipment::STATUS_CANCELLED,
                    ]),
                TextColumn::make('cost')->formatStateUsing(fn($state) => 'BDT ' . $state),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('gateway')
                    ->options([
                        Shipment::GATEWAY_STEADFAST => 'Steadfast',
                        Shipment::GATEWAY_PATHAO => 'Pathao',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        Shipment::STATUS_PENDING => 'Pending',
                        Shipment::STATUS_DELIVERED => 'Delivered',
                        Shipment::STATUS_FAILED => 'Failed',
                        Shipment::STATUS_CANCELLED => 'Cancelled',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShipments::route('/'),
            'create' => Pages\CreateShipment::route('/create'),
            'view' => Pages\ViewShipment::route('/{record}'),
            'edit' => Pages\EditShipment::route('/{record}/edit'),
        ];
    }
}
