<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\ImportQueue;
use App\Models\ImportJob;
use Input;
use Auth;


class ImportJobController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new ImportJob();
    }

    public function index()
    {
        //return $this->model->with(['open'])->whereCompanyId($current_company_id)->get();
        $page_size = config("vars.default_page_size");
        $query = $this->model;
        $query = $query->whereNull('deleted_at');
        $query = $query->whereSiteId($this->site->id);
        foreach (Input::all() as $key => $value){
            switch($key){
                case 'q':
                    $query = $this->model->applySearchQuery($query,$value);
                    break;
				case 'view':
				case 'bypass_paging':
                case 'p':
                    break;
                default:
                    $query->where($key,'=',$value);
            }
        }

		$return = [];

		$return['total_count'] = $query->count();

		if( !\Input::has('bypass_paging') || !\Input::get('bypass_paging') )
			$query = $query->take($page_size);

		if( \Input::has('p') )
			$query = $query->skip((\Input::get('p')-1)*$page_size);

        $import_jobs = $query->get();

		if( count( $import_jobs ) > 0 )
		{
			foreach ($import_jobs as $import_job)
			{
				$done_count = ImportQueue::withTrashed()->whereJobId($import_job->id)->whereNotNull('deleted_at')->count();
                $total_count = ImportQueue::withTrashed()->whereJobId($import_job->id)->count();
				if ($done_count == $total_count)
				{
                    $import_job->is_active = 0;
				} 
				else 
				{
                    $import_job->is_active = 1;
				}
                $import_job->done_count = $done_count;
			}
		}

		$return['items'] = $import_jobs;

		return $return;
    }

    public function countActiveJob()
    {
        $active_count = 0;

        $all_jobs = ImportJob::whereSiteId($this->site->id)->get();
        if (count($all_jobs) > 0)
        {
            foreach ($all_jobs as $job)
            {
                $queue_items = ImportQueue::whereJobId($job->id)->whereNull('deleted_at')->count();
                if ($queue_items > 0)
                {
                    $active_count += 1;
                }
            }

            return $active_count;
        } else
            return 0;
    }

    public function deleteJob()
    {
        $id = \Input::get('id');

        if( !$id )
            App::abort(408, "Id is required.");

        $job = $this->model->find($id);

        $queued_emails = EmailQueue::whereJobId( $id )->get();

        $time = time();
        $send_at = date( 'Y-m-d H:i:s', $time );

        foreach( $queued_emails as $queued_email )
            $queued_email->delete();

        $job->delete();

        return array('success'=>1);
    }
}
