<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\ImportQueue;

class ImportQueueController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new ImportQueue();
        $this->middleware('auth',['except'=>array('cronProcessQueue')]);
        // $q1 = $this->model->with('email')->with(array('user' => function($query) {
        // 	$query->where('email', '!=', '');
        // }))->whereNull('list_type');

        // if (\Input::has('site_id')) {
        // 	$q2 = $this->model->with(['email', 'subscriber'])->whereNotNull('list_type')->where('site_id', \Input::get('site_id'));
        // 	$this->model = $q1->unionAll($q2->getQuery());
        // }

    }

    public function cronProcessQueue()
    {
        $sites = ImportQueue::distinct()
            ->whereNotNull('site_id')
            ->where('site_id', '!=', 0)
            ->select('site_id')
            ->lists('site_id');

        // \Config::set('smartmail.debug', true);

        foreach ($sites as $site)
        {
            $queue = new ImportQueue;
            try
            {
                \Log::info("Processing queue for " . $site);
                $queue->processQueue($site, false);
            }
            catch (Exception $e)
            {
                \Log::info("Failed to import members for site " . $site . " " . $e->getMessage());
            }

            continue;

        }
    }

    public function processImportQueue()
    {
        $status = $this->model->processQueue($this->site->id);
        return $status;
    }

    public function index()
    {
        if( empty( $this->site->id ) )
            App::abort(408, "You must be signed in to view import queue");

        $this->model = $this->model->with(['subscriber', 'email', 'user']);

        return $this->model->whereSiteId( $this->site->id )->get();
    }

}
