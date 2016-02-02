<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Link;
use App\Models\Click;
use App\Helpers\DomainHelper;

use App\Helpers\SMAuthenticate;
use Illuminate\Support\Facades\Input;
use Auth;

class ClickController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware("auth", ['except' => array('trackClick')]);
        $this->middleware('admin', ['except' => array('trackClick')]);
        $this->model = new Click();
    }

    public function trackClick()
    {
        $hash = \Input::get('id');
        $url = \Input::get('refLink');
        $list_type = \Input::get('list_type', 'subscriber');
        $subscriber_id = \Input::get('subscriber_id', null);
		$segment_id = (\Input::has('segment_id')) ? \Input::get('segment_id') : null;

        if( isset( $hash ))
        {
            $link_data = Link::whereHash($hash)->first();

            if( isset($link_data ) )
            {
                $url = $link_data->url;
            }
        }

        $fields = array();
        $fields[ 'ip' ] = DomainHelper::getRealIP();
        $fields[ 'link_id' ] = isset($link_data) ? $link_data->id : '';
		$fields['identifier'] = $list_type . '_' . $subscriber_id; //sole purpose of this is so we can count unique clicks by doing a "distinct" query
		$fields['segment_id'] = $segment_id;

        Click::insert($fields);

        header( "Location: ".$url );
        exit;
    }
}