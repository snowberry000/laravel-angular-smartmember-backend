<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Email;
use App\Models\EmailQueue;
use App\Models\EmailHistory;
use App\Models\AppConfiguration;
use App\Models\Click;
use App\Models\Open;
use App\Models\Link;
use App\Models\EmailSetting;
use App\Models\Unsubscriber;
use App\Models\EmailJob;
use Input;
use Auth;


class EmailJobController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new EmailJob();
    }

    public function index()
    {
        $page_size = config("vars.default_page_size");
        $query = $this->model;
        $query = $query->with('email');
        $query = $query->orderBy('id' , 'DESC');
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

        $email_jobs = $query->get();
		$email_integration = AppConfiguration::whereType('sendgrid')->whereSiteId( $this->site->id )->first();

		if( count( $email_jobs ) > 0 )
		{
			foreach ($email_jobs as $email_job)
			{
				$email_job->sent_count         = EmailHistory::whereJobId( $email_job->id )->count();
				$email_job->open_count         = Open::whereJobId( $email_job->id )->count();
				$email_job->click_count        = Click::leftJoin( 'links', 'clicks.link_id', '=', 'links.id' )
					->where( 'links.job_id', '=', $email_job->id )->count();
				$email_job->unsubscriber_count = Unsubscriber::whereJobId( $email_job->id )->count();
				$queue_items                   = EmailQueue::whereJobId( $email_job->id )->whereNull( 'deleted_at' )
					->count();
				if( $queue_items == 0 )
				{
					$email_job->status = 'Sent Successfully';
				}
				else
				{
					$send_at = strtotime( $email_job->send_at );
					if( $send_at > time() )
					{
						$email_job->status       = 'Send to ' . $queue_items . ' subscribers on: ';//. date( 'M j, Y', $send_at ) . ' at ' . date( 'g:i:s A' , $send_at);
						$email_job->send_date_at = $send_at;
						$email_job->admin_tools  = true;
					}
					else
						$email_job->status = 'Sent ' . $email_job->sent_count . '/' . $queue_items . ' emails';
				}

				if( empty( $email_integration ) || empty( $email_integration->password ) || empty( $email_integration->username ) && $email_job->status != "Sent Successfully" )
				{
					$email_job->sendgrid_account_check = true;
				}
			}
		}

		$return['items'] = $email_jobs;

		return $return;
    }

    public function sendNow()
    {
        $id = \Input::get('id');

        if( !$id )
            App::abort(408, "Id is required.");

        $job = $this->model->find($id);

        $queued_emails = EmailQueue::whereJobId( $id )->get();

        $time = time();
        $send_at = date( 'Y-m-d H:i:s', $time );

        foreach( $queued_emails as $queued_email )
        {
            $queued_email->send_at = $send_at;
            $queued_email->save();
        }

        $job->send_at = $send_at;
        $job->save();

        return array('success'=>1);
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

	public function show( $model )
	{
		$model = $this->model->whereId( $model->id )->with(['email','email.recipients'])->first();

		$model->sent_count         = EmailHistory::whereJobId( $model->id )->count();
		$model->open_count         = Open::whereJobId( $model->id )->count();
		$model->unique_open_count  = Open::whereJobId( $model->id )->select('identifier')->groupby('identifier')->distinct()->get()->count();

		$model->click_count        = Click::leftJoin( 'links', 'clicks.link_id', '=', 'links.id' )
			->where( 'links.job_id', '=', $model->id )->count();

		$model->unique_click_count        = Click::leftJoin( 'links', 'clicks.link_id', '=', 'links.id' )
			->where( 'links.job_id', '=', $model->id )->select('clicks.identifier')->groupby('clicks.identifier')->distinct()->get()->count();

		$model->unsubscriber_count = Unsubscriber::whereJobId( $model->id )->count();

		foreach( $model->email->recipients as $recipient )
			$recipient->fillInData( $model->id );

		return $model;
	}
}
