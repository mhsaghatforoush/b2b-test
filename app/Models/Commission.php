<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $table = 'commissions';

    const STATUS_PENDING   = 'pending';
    const STATUS_PAID = 'paid';

    protected $fillable = [
        'invoice_id',
        'user_id',
        'status',
        'commission_amount',
        'description'
    ];

    // calculate & insert user (book store) commission
    public static function make_commission(Invoice $invoice) {
        self::create([
            'invoice_id' => $invoice->id,
            'user_id'    => $invoice->user_id,
            'pending'    => self::STATUS_PENDING,
            'commission_amount' => ($invoice->amount * 20) / 100    // 20 percent commision for user (book store)
        ]);
    }


    public function getStatusAttribute($status)
    {
        switch ($status) {
            case 'paid':
                $status = "پرداخت شده";
                break;
            case 'pending':
                $status = " منتظر پرداخت";
                break;
        }
        return $status;
    }
}
