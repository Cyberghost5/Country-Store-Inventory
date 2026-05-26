<?php

namespace App\Notifications;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SaleNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Sale $sale,
        public readonly User $recorder,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $product  = $this->sale->product?->name ?? 'Unknown product';
        $amount   = '₦' . number_format($this->sale->total_amount, 2);
        $recorder = $this->recorder->name;

        return [
            'type'        => 'sale',
            'sale_id'     => $this->sale->id,
            'product'     => $product,
            'quantity'    => $this->sale->quantity,
            'amount'      => (string) $this->sale->total_amount,
            'recorded_by' => $recorder,
            'message'     => "{$recorder} recorded a sale: {$product} × {$this->sale->quantity} — {$amount}.",
            'url'         => route('sales.index', ['date' => $this->sale->sale_date->toDateString()]),
        ];
    }
}
