<?php

namespace App\Models;

class Unsubscriber extends Root
{
    protected $table = "unsubscribers";

    public function email() {
        return $this->belongsTo('App\Models\Email');
    }

}