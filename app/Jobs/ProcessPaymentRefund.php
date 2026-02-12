<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Services\PaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPaymentRefund implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public float $amount,
        public string $reason
    ) {}

    public function handle(PaymentService $paymentService): void
    {
        $paymentService->processRefund($this->order, $this->amount, $this->reason);
    }
}
