<?php

namespace App\Models;

class UnsubscriberSegment extends Root
{
    protected $table = "unsubscribers_segment";

    public function email() {
        return $this->belongsTo('App\Models\Email');
    }

}