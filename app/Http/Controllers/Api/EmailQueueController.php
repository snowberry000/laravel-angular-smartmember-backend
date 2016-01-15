<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\EmailQueue;

class EmailQueueController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new EmailQueue();
        // $q1 = $this->model->with('email')->with(array('user' => function($query) {
        // 	$query->where('email', '!=', '');
        // }))->whereNull('list_type');

        // if (\Input::has('site_id')) {
        // 	$q2 = $this->model->with(['email', 'subscriber'])->whereNotNull('list_type')->where('site_id', \Input::get('site_id'));
        // 	$this->model = $q1->unionAll($q2->getQuery());
        // }

    }

	public function processEmailRecipientsQueue()
	{
		$status = $this->model->processRecipientsQueue($this->site->id);
		return $status;
	}

    public function processEmailQueue()
    {
        $status = $this->model->processQueue($this->site->id);
        return $status;
    }

    public function index()
    {
        if( empty( $this->site->id ) )
            App::abort(408, "You must be signed in to a team to view the email queue");

        $this->model = $this->model->with(['subscriber', 'email', 'user']);

        return $this->model->whereSiteId( $this->site->id )->get();
    }

}
