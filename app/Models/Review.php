<?php namespace App\Models;

class Review extends Root {

	 protected $table = "reviews";

	 public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}