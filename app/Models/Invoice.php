<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = "invoices";

    const INVOICE_STATUS_PAID   = 'paid';
    const INVOICE_STATUS_UNPAID = 'unpaid';

    protected $fillable = [
        'transaction_id',
        'user_id',
        'book_id',
        'amount',
        'status',
        'description'
    ];

    public function getStatusAttribute($status)
    {
        switch ($status) {
            case 'paid':
                $status = "پرداخت شده";
                break;
            case 'unpaid':
                $status = " منتظر پرداخت";
                break;
        }
        return $status;
    }
}
