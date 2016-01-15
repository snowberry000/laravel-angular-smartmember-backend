<?php

namespace App\Models;
use Carbon\Carbon;

class Click extends Root
{
    protected $table = "clicks";

    public function site() {
        return $this->belongsTo('App\Models\Site');
    }

    public function link() {
        return $this->belongsTo('App\Models\Link');
    }

}