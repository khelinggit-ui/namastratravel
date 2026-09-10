<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'customer_name', 'tour_name', 'whatsapp', 'email', 'destination', 'pax', 'planned_date', 'status',
        'payment_status', 'payment_provider', 'payment_order_id', 'payment_invoice_number',
        'payment_amount', 'payment_url', 'paid_at',
    ];

    protected $casts = [
        'payment_amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
