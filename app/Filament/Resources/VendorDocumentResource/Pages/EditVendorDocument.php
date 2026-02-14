<?php

namespace App\Filament\Resources\VendorDocumentResource\Pages;

use App\Filament\Resources\VendorDocumentResource;
use App\Models\VendorDocument;
use App\Services\VendorOnboardingService;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use Filament\Notifications\Notification;

class EditVendorDocument extends EditRecord
{
    protected static string $resource = VendorDocumentResource::class;

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $this->record->status = $this->form->getState()['status'] ?? 'pending';
        
        if ($this->record->status === 'rejected') {
            $this->record->rejection_reason = $this->form->getState()['rejection_reason'] ?? null;
        }

        $this->record->save();

        Notification::make()
            ->success()
            ->title('Document status updated')
            ->send();

        parent::save($shouldRedirect, $shouldSendSavedNotification);
    }

    public function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
