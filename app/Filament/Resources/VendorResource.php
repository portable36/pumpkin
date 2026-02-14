<?php

namespace App\Filament\Resources;

use App\Models\Vendor;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;
    
    protected static ?string $navigationLabel = 'Vendors';
    protected static ?string $modelLabel = 'Vendor';
    protected static string | \UnitEnum | null $navigationGroup = 'Vendor Management';
    
    
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Vendor Information')
                    ->schema([
                        TextInput::make('store_name')
                            ->label('Store Name')
                            ->required()
                            ->disabled(),
                        TextInput::make('user.email')
                            ->label('Email')
                            ->email()
                            ->disabled(),
                        TextInput::make('user.name')
                            ->label('Owner Name')
                            ->disabled(),
                        Textarea::make('description')
                            ->label('Store Description')
                            ->disabled(),
                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->disabled(),
                    ]),

                Section::make('KYC & Verification')
                    ->schema([
                        Select::make('kyc_status')
                            ->label('KYC Status')
                            ->options([
                                'pending' => 'Pending Review',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),
                        Textarea::make('kyc_rejection_reason')
                            ->label('Rejection Reason')
                            ->visible(fn($record) => optional($record)->kyc_status === 'rejected'),
                        FileUpload::make('kyc_document_path')
                            ->label('KYC Document')
                            ->disabled(),
                    ]),

                Section::make('Vendor Status & Settings')
                    ->schema([
                        Select::make('status')
                            ->label('Vendor Status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'suspended' => 'Suspended',
                            ])
                            ->required(),
                        TextInput::make('commission_rate')
                            ->label('Commission Rate (%)')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(10),
                    ]),

                Section::make('Banking Information')
                    ->schema([
                        TextInput::make('bank_account_holder')
                            ->label('Account Holder Name')
                            ->disabled(),
                        TextInput::make('bank_account_number')
                            ->label('Account Number')
                            ->disabled(),
                        TextInput::make('bank_name')
                            ->label('Bank Name')
                            ->disabled(),
                        TextInput::make('bank_routing_number')
                            ->label('Routing/Branch Code')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('store_name')
                    ->label('Store Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'info' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'warning' => 'suspended',
                    ])
                    ->sortable(),
                BadgeColumn::make('kyc_status')
                    ->label('KYC Status')
                    ->colors([
                        'info' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->sortable(),
                TextColumn::make('commission_rate')
                    ->label('Commission %')
                    ->formatStateUsing(fn($state) => $state . '%')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Vendor Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'suspended' => 'Suspended',
                    ]),
                SelectFilter::make('kyc_status')
                    ->label('KYC Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\VendorResource\Pages\ListVendors::route('/'),
        ];
    }
}
