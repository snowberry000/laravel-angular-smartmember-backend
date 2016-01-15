<?php namespace App\Models;


class DiscussionSettings extends Root
{
    protected $table = 'discussion_settings';

    public function site(){
        return $this->belongsTo('App\Models\Site');
    }
}
