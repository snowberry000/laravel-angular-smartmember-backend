<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Event;
use App\Models\EventMetaData;
use App\Models\User;
use App\Helpers\SMAuthenticate;
use Input;

class EventController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new Event();
        $this->middleware('admin',['except'=>array('index','show','store','update')]);
        $this->middleware('auth',['except'=>array('show','store','update')]);
    }

	public function index( $params = [] )
	{
		$site_ids = \Auth::user()->sitesWithCapability( 'manage_members', false );

		$page_size = config("vars.default_page_size");
		$query = $this->model->whereIn( 'site_id', $site_ids );

		$query = $query->orderBy( 'id' , 'DESC' );

		foreach (Input::all() as $key => $value){
			switch($key){
				case 'q':
					if (Input::get('q')){
						$query = $this->model->applySearchQuery( $query, $value );
					}
					break;
				case 'view':
				case 'p':
				case 'bypass_paging':
					break;
				default:
					$query->where($key,'=',$value);
			}
		}

		$return = [];

		if( isset( $params['distinct'] ) && $params['distinct'] )
			$return['total_count'] = $query->distinct()->count('user_id');
		else
			$return['total_count'] = $query->count();

		if( !Input::has('bypass_paging') || !Input::get('bypass_paging') )
			$query = $query->take($page_size);

		if( Input::has('p') )
			$query->skip((Input::get('p')-1)*$page_size);

		$return['items'] = $query->get();

		$users = [];

		foreach( $return['items'] as $item )
		{
			if( !empty( $users[ $item->user_id ] ) )
				$item->user = $users[ $item->user_id ];
			elseif( !empty( $users[ $item->email ] ) )
				$item->user = $users[ $item->email ];
			else
			{
				if( !empty( $item->user_id ) )
					$user = User::find( $item->user_id );
				elseif( !empty( $item->email ) )
					$user = User::whereEmail( $item->email )->first();

				if( !empty( $user ) )
				{
					$users[ $item->user_id ] = $user;
					$users[ $item->email ] = $user;

					$item->user = $user;
				}
			}
		}

		return $return;
	}

	public function store()
	{
		if( SMAuthenticate::set() && !\Input::has('user_id') )
			\Input::merge( ['user_id' => \Auth::user()->id ] );

		if( $this->site && $this->site->id && !\Input::has('site_id') )
			\Input::merge( ['site_id' => $this->site->id ] );
		else
			\Input::merge( ['site_id' => 0 ] );

		if( !\Input::has('event_name') )
			return [];

		if( \Input::has('user_id') )
			$user = User::find( \Input::get('user_id') );

		if( empty( $user ) && ( !\Input::has('email') || empty( \Input::get('email') ) ) )
			return [];

		$model = $this->model->whereEventName( \Input::get('event_name' ) );

		if( \Input::has('site_id') )
			$model = $model->whereSiteId( \Input::get('site_id') );
		else
			$model = $model->whereSiteId( 0 );

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
		if( SMAuthenticate::set() && !\Input::has('user_id') )
			\Input::merge( ['user_id' => \Auth::user()->id ] );

		if( $this->site && $this->site->id && !\Input::has('site_id') )
			\Input::merge( ['site_id' => $this->site->id ] );
		else
			\Input::merge( ['site_id' => 0 ] );

		if( !\Input::has('event_name') || empty( \Input::get('event_name') ) )
			return [];

		if( \Input::has('user_id') )
			$user = User::find( \Input::get('user_id') );

		if( empty( $user ) && ( !\Input::has('email') || empty( \Input::get('email') ) ) )
			return [];

		$model->update( \Input::all() );

		return $this->model->with(['meta'])->whereId( $model->id )->first();
	}

	public function show( $model )
	{
		return $this->model->with(['meta'])->whereId( $model->id )->first();
	}
}