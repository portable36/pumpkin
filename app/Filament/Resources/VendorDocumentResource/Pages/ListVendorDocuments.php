<?php

namespace App\Filament\Resources\VendorDocumentResource\Pages;

use App\Filament\Resources\VendorDocumentResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;

class ListVendorDocuments extends ListRecords
{
    protected static string $resource = VendorDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Document uploads are handled by vendor, not admins
        ];
    }
}
