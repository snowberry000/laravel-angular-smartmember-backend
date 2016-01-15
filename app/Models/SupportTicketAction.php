<?php

namespace App\Models;

class SupportTicketAction extends Root
{
    protected $table = "support_ticket_actions";
    protected $fillable = ["user_id", "ticket_id", "modified_attribute", "old_value", "new_value"];

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function ticket(){
        return $this->belongsTo('App\Models\SupportTicket','ticket_id');
    }
    
    public static function addAction( $ticket_id, $attribute, $new_value = '', $original_value = '' )
    {
        $user = \Auth::user();

        parent::create( array(
             'user_id' => ( $user ? $user->id : 0 ),
             'ticket_id' => $ticket_id,
             'modified_attribute' => $attribute,
             'old_value' => $original_value,
             'new_value' => $new_value
        ) );
    }
}
