<?php namespace App\Models\Forum;

use App\Models\Root;

class Reply extends Root
{
    protected $table = 'forum_replies';
   	
   	public function topic(){
   		return $this->belongsTo('App\Models\Forum\Topic','topic_id');
   	}

   	public function user(){
   		return $this->belongsTo('App\Models\User','user_id');
   	}
}

Reply::creating(function($reply){
	$reply->user_id = \Auth::user()->id;
});

Reply::created(function($reply){
	$reply->topic->total_replies++;
	$reply->topic->save();

	$reply->topic->category->total_replies++;
	$reply->topic->category->save();
});