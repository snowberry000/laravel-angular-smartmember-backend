<?php

namespace App\Models;

class EmailHistory extends Root
{
    protected $table = "email_history";

    public function site() {
        return $this->belongsTo('App\Models\Site');
    }

    public function email() {
        return $this->belongsTo('App\Models\Email');
    }
}