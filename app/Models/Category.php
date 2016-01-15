<?php namespace App\Models;


class Category extends Root
{
    protected $table = 'categories';
    protected $hidden = array('pivot');

    public function site(){
    	return $this->belongsTo('App\\Models\\Site');
    }

    public function post(){
    	return $this->belongsTo('App\\Models\\Post');
    }
}

