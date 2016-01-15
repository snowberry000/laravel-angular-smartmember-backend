<?php namespace App\Models;

use App\Models\Email;

class EmailJob extends Root
{
    protected $table = 'email_jobs';

	public function site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }

    public function email()
    {
        return $this->belongsTo('App\Models\Email', 'email_id');
    }

    public function email_histories()
    {
        return $this->hasMany('App\Models\EmailHistory', 'job_id');
    }

	public function applySearchQuery($query,$value)
	{
		$emails = Email::where('subject','like','%' . $value . '%')->select('id')->lists('id');
		return $query->whereIn('email_id',$emails);
	}
}


