<?php namespace App\Models;


class SpecialPage extends Root
{
    protected $table = 'special_pages';

    public function site(){
        return $this->belongsTo('App\Models\Site');
    }
    public function access_level(){
        return $this->belongsTo('App\Models\AccessLevel', 'access_level');
    }
    
}
