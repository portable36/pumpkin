<?php

namespace App\Filament\Resources;

use App\Models\Payment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Payments';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;
        
    protected static ?string $navigationLabel = 'Payments';
    protected static ?string $modelLabel = 'Payment';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Payment Information')
                    ->schema([
                        TextInput::make('order_id')
                            ->label('Order ID')
                            ->disabled(),
                        TextInput::make('user.email')
                            ->label('Customer')
                            ->disabled(),
                        TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->disabled(),
                        TextInput::make('gateway')
                            ->label('Payment Gateway')
                            ->disabled(),
                    ]),

                Section::make('Payment Details')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'success' => 'Successful',
                                'failed' => 'Failed',
                                'cancelled' => 'Cancelled',
                                'refunded' => 'Refunded',
                            ])
                            ->disabled(),
                        TextInput::make('transaction_id')
                            ->label('Transaction ID')
                            ->disabled(),
                        TextInput::make('external_id')
                            ->label('Gateway Transaction ID')
                            ->disabled(),
                        TextInput::make('paid_at')
                            ->label('Paid At')
                            ->disabled(),
                    ]),

                Section::make('Refund Information')
                    ->schema([
                        TextInput::make('refunded_amount')
                            ->label('Refunded Amount')
                            ->numeric()
                            ->disabled(),
                        TextInput::make('refunded_at')
                            ->label('Refunded At')
                            ->disabled(),
                    ])->visible(fn($record) => $record?->refunded_amount),

                Section::make('Gateway Response')
                    ->schema([
                        Textarea::make('gateway_response_json')
                            ->label('Raw Response')
                            ->default(fn($record) => optional($record)->gateway_response ? json_encode($record->gateway_response, JSON_PRETTY_PRINT) : '')
                            ->disabled(),
                    ])->visible(fn($record) => $record),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_id')
                    ->label('Order')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn($state, $record) => $state . ' ' . ($record->currency ?? 'BDT'))
                    ->sortable(),
                TextColumn::make('gateway')
                    ->label('Gateway')
                    ->sortable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'info' => 'pending',
                        'warning' => 'processing',
                        'success' => 'success',
                        'danger' => 'failed',
                        'gray' => 'cancelled',
                        'secondary' => 'refunded',
                    ])
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'success' => 'Successful',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ]),
                SelectFilter::make('gateway')
                    ->label('Payment Gateway')
                    ->options([
                        'sslcommerz' => 'SSLCommerz',
                        'stripe' => 'Stripe',
                        'paypal' => 'PayPal',
                        'bkash' => 'bKash',
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PaymentResource\Pages\ListPayments::route('/'),
        ];
    }
}
