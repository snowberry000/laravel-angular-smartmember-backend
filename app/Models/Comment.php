<?php namespace App\Models;


class Comment extends Root
{
    protected $table = 'comments';

    public function user(){
    	return $this->belongsTo('App\\Models\\User');
    }

    public function reply(){
    	return $this->hasMany('App\\Models\\Comment' , 'parent_id');
    }
}

Comment::creating(function($comment){
	$comment->user_id = \Auth::user()->id;
});
