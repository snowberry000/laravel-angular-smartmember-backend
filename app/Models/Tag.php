<?php namespace App\Models;


class Tag extends Root
{
    protected $table = 'tags';
    protected $hidden = array('pivot');

    public function site(){
    	return $this->belongsTo('App\\Models\\Site');
    }

    public function post(){
    	return $this->belongsTo('App\\Models\\Post');
    }
}
