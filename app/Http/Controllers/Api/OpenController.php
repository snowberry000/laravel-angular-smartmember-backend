<?php namespace App\Http\Controllers\Api;

use App\Helpers\DomainHelper;
use App\Http\Controllers\ApiController;
use App\Models\Open;
use App\Models\Email;
use App\Models\EmailSubscriber;

use App\Helpers\SMAuthenticate;
use Illuminate\Support\Facades\Input;
use Auth;

class OpenController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware("auth", ['except' => array('trackOpen')]);
        $this->middleware('admin', ['except' => array('trackOpen')]);
        $this->model = new Open();
    }

    public function trackOpen()
    {
        $this->FakeImageOutput();
        $job_id = (\Input::has('job_id')) ? \Input::get('job_id') : '';
        $subscriber_id = (\Input::has('subscriber_id')) ? \Input::get('subscriber_id') : '';
        $site_id = (\Input::has('network_id')) ? \Input::get('network_id') : '';

        if (!$job_id || !$subscriber_id)
            \App::abort(403, "Email ID or Subscriber ID is empty. Please try again");

        $fields = array();
        $fields['ip'] = DomainHelper::getRealIP();
        $fields['subscriber_id'] = $subscriber_id;
        $fields['job_id'] = $job_id;
        $fields['site_id'] = $site_id;

        Open::insert($fields);
        //return $fields;
    }

    function FakeImageOutput()
    {
        ignore_user_abort(true);

        // turn off gzip compression
        if (function_exists('apache_setenv')) {
            apache_setenv('no-gzip', 1);
        }

        ini_set('zlib.output_compression', 0);

        // turn on output buffering if necessary
        if (ob_get_level() == 0) {
            ob_start();
        }

        // removing any content encoding like gzip etc.
        header('Content-encoding: none', true);

        //check to ses if request is a POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // the GIF should not be POSTed to, so do nothing...
            echo ' ';
        } else {
            // return 1x1 pixel transparent gif
            header("Content-type: image/gif");
            // needed to avoid cache time on browser side
            header("Content-Length: 42");
            header("Cache-Control: private, no-cache, no-cache=Set-Cookie, proxy-revalidate");
            header("Expires: Wed, 11 Jan 2000 12:59:00 GMT");
            header("Last-Modified: Wed, 11 Jan 2006 12:59:00 GMT");
            header("Pragma: no-cache");

            echo sprintf('%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%', 71, 73, 70, 56, 57, 97, 1, 0, 1, 0, 128, 255, 0, 192, 192, 192, 0, 0, 0, 33, 249, 4, 1, 0, 0, 0, 0, 44, 0, 0, 0, 0, 1, 0, 1, 0, 0, 2, 2, 68, 1, 0, 59);
        }

        // flush all output buffers. No reason to make the user wait for OWA.
        ob_flush();
        flush();
        ob_end_flush();
    }
}