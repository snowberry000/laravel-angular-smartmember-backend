<?php

namespace App\Models;
use App\Models\AppConfiguration\SendGridEmail;

class LinkedAccount extends Root
{
    protected $table = "linked_accounts";
    protected $guarded = ['verified', 'user_id', 'linked_email', 'verification_hash'];
    protected $hidden = ['verification_hash', 'linked_user_id', 'email_only_link', 'user_id'];

    public function togglePrimary($user_id , $linked_account_id){

    	$linked_account = LinkedAccount::find($linked_account_id);
    	if(!isset($linked_account)){
    		return array('success' => false);
    	}

    	$user = User::find($user_id);
    	if(!isset($user)){
    		return array('success' => false);
    	}

		$already_exists = User::where('email', $linked_account->linked_email)->first();
	    if ($already_exists)
	    {
	        $response['message'] = 'The given email is already linked';
	        return $response;
	    }
    	$new_account = $user->linkAccount($user->email , 1 , 1);

    	$user->email = $linked_account->linked_email;
    	$user->save();

    	$linked_account->forceDelete();

    	return $new_account;
    }

    public function link($user_id , $email){
    	$user = User::find($user_id);
    	return $user->linkAccount($email); 
    }

    public function claim($user_id , $id){
    	$user = User::find($user_id);
    	$linked_account = LinkedAccount::find($id);
    	SendGridEmail::sendAccountLinkEmail($linked_account);
    	$linked_account->claimed = true;
    	$linked_account->save();

    	return array('success' => true);
    }

    public function merge($hash = FALSE , $user_id)
    {
        if (!$hash) return;

        $account = LinkedAccount::where('verification_hash', $hash)->first();

        if (!$account) 
        {
            return ['status' => 'NOOK', 'message' => 'Invalid verification code'];
        }

        if ($account->verified == 1) 
        {
            return ['status' => 'NOOK',
                     'message' => 'Account already associated with your primary account'];
        }

        if ($account->linked_user_id && is_numeric($account->linked_user_id) && $account->linked_user_id > 0)
        {
            $tables = ['sites', 'affcontests', 'site_notices_seen', 'access_passes', 'bridge_bpages', 'comments',
                        'companies', 'downloads_history', 'drafts', 'linked_accounts', 'roles', 'sites', 'support_tickets', 'support_ticket_actions',
                        'support_tickets', 'team_roles', 'transactions', 'user_notes'];

            foreach ($tables as $table)
            {
                 \DB::table($table)
                     ->where('user_id', $account->linked_user_id)
                    ->update(array('user_id' => $user_id));   
            }
        }

        $user = User::find($account->linked_user_id);
        if ($user)
            $user->delete();

        $account->verified = 1;
        $account->save();


        return ['status' => 'OK'];
    }
}

LinkedAccount::saving(function($account){

    $routes[] = 'user_';
    
    \SMCache::reset($routes);
    return $account;
});

LinkedAccount::deleting(function($account){

    $routes[] = 'user_';
    
    \SMCache::reset($routes);
    return $account;
});
