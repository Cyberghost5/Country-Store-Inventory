<?php

namespace App\Notifications;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PurchaseNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Purchase $purchase,
        public readonly User     $recorder,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $product  = $this->purchase->product?->name ?? 'Unknown product';
        $supplier = $this->purchase->supplier?->name ?? 'Unknown supplier';
        $amount   = '₦' . number_format($this->purchase->total_cost, 2);
        $recorder = $this->recorder->name;

        return [
            'type'        => 'purchase',
            'purchase_id' => $this->purchase->id,
            'product'     => $product,
            'quantity'    => $this->purchase->quantity,
            'amount'      => (string) $this->purchase->total_cost,
            'supplier'    => $supplier,
            'recorded_by' => $recorder,
            'message'     => "{$recorder} recorded a purchase: {$product} × {$this->purchase->quantity} from {$supplier} — {$amount}.",
            'url'         => route('purchases.index', ['date' => $this->purchase->purchase_date->toDateString()]),
        ];
    }
}
