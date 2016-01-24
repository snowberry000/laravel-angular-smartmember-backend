<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\CustomAttribute;
use App\Models\MemberMeta;
use App\Models\User;
use App\Helpers\SMAuthenticate;
use Input;

class CustomAttributeController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new CustomAttribute();
        $this->middleware('admin',['except'=>array('index','show','store','update')]);
        $this->middleware('auth',['except'=>array('show','store','update')]);
    }

	public function set()
	{
		$custom_attribute = $this->model->whereUserId( \Input::get( 'sm_customer_id', 10 ) )->whereName( \Input::get('name') )->first();

		if( $custom_attribute )
		{
			if( \Input::has('archived') )
				$custom_attribute->archived = \Input::get('archived');

			if( \Input::has('shown') )
				$custom_attribute->shown = \Input::get('shown');

			$custom_attribute->save();

			return $custom_attribute;
		}
	}
}