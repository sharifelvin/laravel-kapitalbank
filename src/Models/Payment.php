<?php

namespace SharifElvin\KapitalBankTransfer\Models;

use App\Database\EloquentModel as Model;

class Payment extends Model
{
    protected $table = 'payments';
    
    protected $fillable = [
        'order_id', 
        'session_id', 
        'currency', 
        'order_status', 
        'order_description', 
        'amount', 
        'payment_url', 
        'status_code',
        'order_check_status',
        'language_code'
    ];

}