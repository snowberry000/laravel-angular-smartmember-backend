<?php namespace App\Models;


class ImportJob extends Root
{
    protected $table = 'import_jobs';

	public function site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }

}


