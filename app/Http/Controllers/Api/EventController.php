<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Event;
use App\Models\EventMetaData;
use App\Models\User;

class EventController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new Event();
        $this->middleware('admin',['except'=>array('index','show','store','update')]);
    }

	public function store()
	{
		if( \Auth::set() && !\Input::has('user_id') )
			\Input::merge( ['user_id' => \Auth::user()->id ] );

		if( $this->site && $this->site->id && !\Input::has('site_id') )
			\Input::merge( ['site_id' => $this->site->id ] );

		if( !\Input::has('event_name') )
			\App::abort('422','You must provide an event name');

		if( \Input::has('user_id') )
			$user = User::find( \Input::get('user_id') );

		if( empty( $user ) )
			\App::abort('422','The user doesn\'t exist');

		$model = $this->model->whereEventName( \Input::get('event_name' ) );

		if( \Input::has('site_id') )
			$model = $model->whereSiteId( \Input::get('site_id') );

		$model = $model->whereUserId( \Input::get('user_id') )->first();

		if( !$model )
		{
			$model = $this->model->create( \Input::all() );
		}

		if( !$model->count )
			$model->count = 1;
		else
			$model->count = $model->count + 1;

		$model->save();

		return $this->model->with(['meta'])->whereId( $model->id )->first();
	}

	public function update( $model )
	{
		if( \Auth::set() && !\Input::has('user_id') )
			\Input::merge( ['user_id' => \Auth::user()->id ] );

		if( $this->site && $this->site->id && !\Input::has('site_id') )
			\Input::merge( ['site_id' => $this->site->id ] );

		if( !\Input::has('event_name') )
			\App::abort('422','You must provide an event name');

		if( \Input::has('user_id') )
			$user = User::find( \Input::get('user_id') );

		if( empty( $user ) )
			\App::abort('422','The user doesn\'t exist');

		$model->update( \Input::all() );

		return $this->model->with(['meta'])->whereId( $model->id )->first();
	}

	public function show( $model )
	{
		return $this->model->with(['meta'])->whereId( $model->id )->first();
	}
}