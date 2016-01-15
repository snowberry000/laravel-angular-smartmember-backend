<?php namespace App\Models;


class PermalinkStats extends Root
{
    protected $table = 'permalink_stats';

    public function permalink()
    {
        return $this->hasOne('App\Models\Permalink', 'id', 'permalink_id');
    }
}