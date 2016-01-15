<?php

namespace App\Models;

class TicketNote extends Root
{
    protected $table = "ticket_notes";

    public function user(){
        return $this->belongsTo("App\\Models\\User" , 'user_id');
    }
}

TicketNote::creating(function($note){
    if( \Auth::user() )
        $note->user_id = \Auth::user()->id;
});
