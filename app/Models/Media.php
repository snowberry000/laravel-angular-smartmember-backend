<?php namespace App\Models;


class Media extends Root
{
    protected $table = 'media_files';

    public function site(){
    	return $this->belongsTo('App\Models\Site');
    }
    
}