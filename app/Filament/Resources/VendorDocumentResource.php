<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorDocumentResource\Pages;
use App\Models\VendorDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class VendorDocumentResource extends Resource
{
    protected static ?string $model = VendorDocument::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'KYC Documents';
    protected static ?int $navigationSort = 5;
    protected static string|\UnitEnum|null $navigationGroup = 'Vendor Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Section::make('Document Information')
                    ->schema([
                        Components\Select::make('vendor_id')
                            ->relationship('vendor', 'shop_name')
                            ->required()
                            ->searchable()
                            ->disabled(fn ($operation) => $operation === 'edit'),

                        Components\TextInput::make('document_type')
                            ->label('Document Type')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => VendorDocument::DOCUMENT_TYPES[$state] ?? $state),

                        Components\TextInput::make('file_name')
                            ->label('File Name')
                            ->disabled(),

                        Components\TextInput::make('file_size')
                            ->label('File Size (KB)')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => $state ? round($state / 1024, 2) : 'N/A'),

                        Components\TextInput::make('mime_type')
                            ->disabled(),
                    ])->columns(2),

                Components\Section::make('Review & Approval')
                    ->schema([
                        Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending Review',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            ->live(),

                        Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->hidden(fn (Components\Textarea $component) => $component->getLivewireValue() !== 'rejected')
                            ->required(fn (Components\Textarea $component) => $component->getLivewireValue() === 'rejected')
                            ->rows(3),

                        Components\Select::make('verified_by')
                            ->label('Verified By')
                            ->relationship('verifier', 'name')
                            ->disabled()
                            ->searchable(),

                        Components\Datetime::make('verified_at')
                            ->label('Verified At')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vendor.shop_name')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('document_type')
                    ->label('Document Type')
                    ->formatStateUsing(fn ($state) => VendorDocument::DOCUMENT_TYPES[$state] ?? $state)
                    ->colors([
                        'primary' => fn ($state) => true,
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => VendorDocument::STATUSES[$state] ?? $state),

                Tables\Columns\TextColumn::make('file_name')
                    ->label('File')
                    ->limit(30)
                    ->copyable(),

                Tables\Columns\TextColumn::make('verifier.name')
                    ->label('Verified By')
                    ->default('Pending'),

                Tables\Columns\TextColumn::make('verified_at')
                    ->label('Verified At')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),

                SelectFilter::make('document_type')
                    ->options(VendorDocument::DOCUMENT_TYPES),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendorDocuments::route('/'),
            'edit' => Pages\EditVendorDocument::route('/{record}/edit'),
        ];
    }
}
