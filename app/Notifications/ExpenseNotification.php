<?php

namespace App\Notifications;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExpenseNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Expense $expense,
        public readonly User    $recorder,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $amount   = '₦' . number_format($this->expense->amount, 2);
        $recorder = $this->recorder->name;
        $desc     = $this->expense->description;

        return [
            'type'        => 'expense',
            'expense_id'  => $this->expense->id,
            'description' => $desc,
            'amount'      => (string) $this->expense->amount,
            'recorded_by' => $recorder,
            'message'     => "{$recorder} recorded an expense: \"{$desc}\" — {$amount}.",
            'url'         => route('expenses.index', ['date' => $this->expense->expense_date->toDateString()]),
        ];
    }
}
