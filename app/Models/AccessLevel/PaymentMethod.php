<?php namespace App\Models\AccessLevel;

use App\Models\Root;

class PaymentMethod extends Root
{
    protected $table = 'access_payment_methods';

    public function type()
    {
        return $this->belongsTo('App\Models\PaymentMethod', "payment_method_id");
    }
}
