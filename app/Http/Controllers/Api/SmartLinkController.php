<?php namespace App\Http\Controllers\Api;

use App\Models\Site;
use App\Models\SmartLink;
use App\Models\SmartLinkUrl;
use App\Models\AccessLevel\Pass;

class SmartLinkController extends SMController
{
    public function __construct()
	{
        parent::__construct();
        $this->model = new SmartLink();
        $this->middleware('auth',['except'=>array('index','show')]); 
        $this->middleware('admin',['except'=>array('index','show')]);    
    }

	public function index()
	{
		return parent::paginateIndex();
	}

	public function store()
	{
		\Input::merge(['site_id' => $this->site->id ]);

		$model = $this->model->create(\Input::except(['urls']));

		if (!$model->id){
			App::abort(401, "The operation requested couldn't be completed");
		}

		if( \Input::has('urls') && !empty( \Input::get('urls') ) )
		{
			$url_ids = [];

			foreach( \Input::get('urls') as $key=>$val )
			{
				if( empty( $val['url'] ) )
					break;

				if( isset( $val->id ) )
				{
					$url = SmartLinkUrl::find( $val->id );
					$url->url = $val['url'];
					$url->weight = $val['weight'];
					$url->order = $val['order'];
					$url->enabled = $val['enabled'];
					$url->save();
				}
				else
				{
					$val['smart_link_id'] = $model->id;
					$url = SmartLinkUrl::create( $val );
				}

				$url_ids[] = $url->id;
			}

			if( !empty( $url_ids ) )
				$extra_urls = SmartLinkUrl::whereSmartLinkId( $model->id )->whereNotIn('id', $url_ids )->get();

			if( !empty( $extra_urls ) )
				foreach( $extra_urls as $extra_url )
					$extra_url->delete();
		}
		else
		{
			$extra_urls = SmartLinkUrl::whereSmartLinkId( $model->id )->get();

			if( !empty( $extra_urls ) )
				foreach( $extra_urls as $extra_url )
					$extra_url->delete();
		}

		return $model;
	}

	public function update($model){
		$model->fill(\Input::except('_method' , 'urls'));
		$model->save();

		if( \Input::has('urls') && !empty( \Input::get('urls') ) )
		{
			$url_ids = [];

			foreach( \Input::get('urls') as $key=>$val )
			{
				if( empty( $val['url'] ) )
					continue;

				if( !empty( intval( $val['enabled'] ) ) )
					$val['enabled'] = 1;
				else
					$val['enabled'] = 0;

				if( !empty( $val['id'] ) )
				{
					$url = SmartLinkUrl::find( $val['id'] );

					$url->url = $val['url'];
					$url->weight = $val['weight'];
					$url->order = $val['order'];
					$url->enabled = $val['enabled'];
					$url->save();
				}
				else
				{
					$val['smart_link_id'] = $model->id;
					$url = SmartLinkUrl::create( $val );
				}

				$url_ids[] = $url->id;
			}

			if( !empty( $url_ids ) )
				$extra_urls = SmartLinkUrl::whereSmartLinkId( $model->id )->whereNotIn('id', $url_ids )->get();

			if( !empty( $extra_urls ) )
				foreach( $extra_urls as $extra_url )
					$extra_url->delete();
		}
		else
		{
			$extra_urls = SmartLinkUrl::whereSmartLinkId( $model->id )->get();

			if( !empty( $extra_urls ) )
				foreach( $extra_urls as $extra_url )
					$extra_url->delete();
		}

		return $model;
	}
}