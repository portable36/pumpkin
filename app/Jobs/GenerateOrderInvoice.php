<?php

namespace App\Jobs;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GenerateOrderInvoice implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function handle(): void
    {
        $pdf = Pdf::loadView('invoices.order', ['order' => $this->order])
            ->setPaper('a4')
            ->setOption('margin-top', 0)
            ->setOption('margin-bottom', 0);

        $filename = "invoices/order-{$this->order->order_number}.pdf";
        Storage::disk('private')->put($filename, $pdf->output());
    }
}
