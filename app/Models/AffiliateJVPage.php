<?php

namespace App\Models;

use App\Models\EmailList;

class AffiliateJVPage extends Root
{
    protected $table = "affiliate_jvpage";

    public function emailList()
    {
    	return $this->hasOne('App\Models\EmailList', 'id', 'email_list_id');
    }
}

AffiliateJVPage::creating(function($page){
	if ( ! isset($page->email_list_id) || empty($page->email_list_id) ) 
		\App::abort(403," 'Email List' is required.");

});

AffiliateJVPage::updating(function($page){
	if ( ! isset($page->email_list_id) || empty($page->email_list_id) ) 
		\App::abort(403," 'Email List' is required.");
});
