<?php namespace App\Models\Forum;

use App\Models\Root;

class Topic extends Root
{
    protected $table = 'forum_topics';
   	
   	public function category(){
   		return $this->belongsTo('App\Models\Forum\Category');
   	}

   	public function replies(){
   		return $this->hasMany('App\Models\Forum\Reply','topic_id');
   	}

   	public function user(){
   		return $this->belongsTo('App\Models\User','user_id');
   	}
}

Topic::creating(function($topic){
   $topic->user_id = \Auth::user()->id;
});

Topic::created(function($topic){
   $topic->permalink = \App\Models\Permalink::set($topic);
   $topic->save();

   $topic->category->total_topics++;
   $topic->category->save();
});