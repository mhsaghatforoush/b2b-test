<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'invoice_id',
        'transaction_id',
        'ip',
        'data',
        'description'
    ];

    public static function makeTransaction($transaction_id, $invoice_id, $data = [], $description = null) {
        $transaction = new self();
        $transaction->user_id = auth()->user()->id;
        $transaction->invoice_id = $invoice_id;
        $transaction->transaction_id = $transaction_id;
        $transaction->ip = request()->getClientIp();
        $transaction->data = json_encode($data);
        $transaction->description = $description;
        $transaction->save();
        return $transaction;
    }
}
