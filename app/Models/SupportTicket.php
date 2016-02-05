<?php namespace App\Models;

use App\Models\SupportTicketAction;
use App\Models\AppConfiguration\SendGridEmail;
use App\Models\LinkedAccount;
use App\Models\Site;
use App\Models\User;
use SMCache;

class SupportTicket extends Root
{
    protected $table = 'support_tickets';

    public function user(){
    	return $this->belongsTo("App\\Models\\User" , 'user_id');
    }

    public function reply(){
    	return $this->hasMany('App\\Models\\SupportTicket' , 'parent_id');
    }

    public function lastReply(){
        return $this->hasOne('App\\Models\\SupportTicket' , 'parent_id')->orderBy('created_at' , 'DESC')->take(1);
    }

    public function actions(){
        return $this->hasMany('App\\Models\\SupportTicketAction' , 'ticket_id');
    }

    public function notes(){
    	return $this->hasMany('App\\Models\\TicketNote' , 'ticket_id');
    }

    public function agent(){
    	return $this->belongsTo("App\\Models\\User" , 'agent_id');
    }

    public static function getUnreadSupportTickets()
    {
		$site = \Domain::getSite();
        if ($site)
        {
            if( \Auth::user() && \SMRole::hasAccess($site->id,'manage_support_tickets') )
                if (!empty($site))
                {
                    return SupportTicket::whereSiteId( $site->id )->whereParentId(0)->whereStatus('open')->count();
                }
                else
                    return 0;
        }
    }

	public function applySearchQuery($query , $value)
	{
		return $query->where(function($q) use ($value){
			$q->whereId( $value );
			$q->orwhere( 'subject', 'like', '%' . $value . '%');
			$q->orwhere( 'message', 'like', '%' . $value . '%');
			$q->orwhere( 'user_email', 'like', '%' . $value . '%');
			$q->orwhere( 'user_name', 'like', '%' . $value . '%');
		});
	}
}

SupportTicket::created(function($ticket){
	if( !empty( $ticket->parent_id ) )
	{
		$full_ticket = SupportTicket::with(array(
			   'reply' => function ($query) {
				   $query->orderBy('created_at', 'desc');
			   } , 'reply.user' , 'user' , 'agent' ,
			   'actions'=> function ($query) {
				   $query->orderBy('created_at', 'desc');
			   } , 'actions.user'))->find( $ticket->parent_id );

		$full_ticket->data = [];
		//return $parent_ticket;
		$full_ticket->data = \App\Http\Controllers\Api\SupportTicketController::sortTickets($full_ticket);
		$site = Site::find($full_ticket->site_id);

		if( \Auth::user() && \Auth::user()->id != $full_ticket->user_id )
			SendGridEmail::sendReplySupportEmail( array( 'name' => $full_ticket->user_name, 'email' => $full_ticket->user_email ), $full_ticket, $site );
	}
});

SupportTicket::deleting(function($support_ticket){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';
    
    SMCache::reset($routes);
    return $support_ticket;
});

SupportTicket::saving(function($support_ticket){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';
    
    SMCache::reset($routes);
    return $support_ticket;
});

SupportTicket::saving(function($ticket){
    $updates = array(
        'status',
        'agent_id',
		'user_email'
    );

    foreach($ticket->getDirty() as $attribute => $value){
        if( in_array( $attribute, $updates ) )
        {
            $original = $ticket->getOriginal( $attribute );
            if( $original != $value )
            {
                //please don't comment out this line, it is required for SM-1693
                //This is throwing error about $ticket->id being null <-- gotchya, I wasn't seeing this error, comments as to why something was commented out would be useful
                if($ticket->id){
                    if($attribute=='agent_id')
                    {
                        if($original == 0)
                            $original = 'Unassigned';
                        else{
                            $original = User::find(\Auth::user()->id);
                            if($original){
                                $original = $original->first_name . ' ' . $original->last_name;
                            }
                        }
                        $value = User::find($value);
                        if($value){
                           $value = $value->first_name . ' ' . $value->last_name; 
                        }
                    }
					
					if( $attribute != 'user_email' )
                    	SupportTicketAction::addAction( $ticket->id, $attribute, $value, $original );
                }

                switch( $attribute )
                {
                    case 'status':
                        if( $value == 'solved' && \Auth::user() )
                        {
                            $full_ticket = SupportTicket::with(array(
                                'reply' => function ($query) {
                                    $query->orderBy('created_at', 'desc');
                                } , 'reply.user' , 'user' , 'agent' ,
                                'actions'=> function ($query) {
                                    $query->orderBy('created_at', 'desc');
                                } , 'actions.user'))->find( $ticket->id );

                            $full_ticket->data = [];
                            //return $parent_ticket;
                            $full_ticket->data = \App\Http\Controllers\Api\SupportTicketController::sortTickets($full_ticket);

                            SendGridEmail::sendResolvedSupportEmail( $full_ticket );
                        }
                        $send_email = \Input::get('send_email');
                        break;
                    case 'agent_id':
                        //this was moved here so it only sends the e-mail if the agent changed
                        //instead of every time there is an update and the agent_id != 0
                        if( $ticket->agent_id != 0 && ( !\Auth::user() || \Auth::user()->id != $ticket->agent_id ) )
                        {
                            $full_ticket = SupportTicket::with(array(
                               'reply' => function ($query) {
                                   $query->orderBy('created_at', 'desc');
                               } , 'reply.user' , 'user' , 'agent' ,
                               'actions'=> function ($query) {
                                   $query->orderBy('created_at', 'desc');
                               } , 'actions.user'))->find( $ticket->id );
                            if( $full_ticket )
                            {
                                $full_ticket->data = [ ];

                                $full_ticket->data = \App\Http\Controllers\Api\SupportTicketController::sortTickets( $full_ticket );

								$full_ticket->agent_id = $ticket->agent_id;

                                SendGridEmail::sendNewAgentEmail( $full_ticket );
                            }
                        }
                        break;
					case 'user_email':
						$user = User::whereEmail( $value )->first();

						if( !$user )
						{
							$primaryAccount = LinkedAccount::where('linked_email', \Input::get('user_email') )
								->where('verified', 1)
								->first();

							if ($primaryAccount)
							{
								$user = User::find($primaryAccount->user_id);
							}
						}

						if( !$user )
								$user = \App\Models\Transaction::createUserForTransaction( array( 'email' => $value, 'site_id' => $ticket->site_id, 'name' => $ticket->user_name ), true );

						if( $user )
						{
							$ticket->user_id = $user->id;
							$ticket->user_name = $user->first_name . ' ' . $user->last_name;
						}
						break;
                }
            }
        }
    }
});