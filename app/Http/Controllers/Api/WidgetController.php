<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Widget;
use App\Models\WidgetMeta;
use App\Models\WidgetLocation;

class WidgetController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new Widget();
    }

	public function index()
	{
		$query = $this->model;

		$query = $query->orderBy('sort_order' , 'ASC');
		foreach (\Input::all() as $key => $value){
			switch($key){
				case 'q':
					//$query = $this->model->applySearchQuery($query,$value);
					break;
				default:
					$query->where($key,'=',$value);
			}
		}
		return $query->get();
	}

	public function store()
	{
		$data = [
			'site_id' => $this->site->id
		];

		$defaults = [
			'sidebar_id' => 1,
			'target_id' => null,
			'sort_order' => 0,
			'type' => ''
		];

		foreach( $defaults as $key=>$val )
		{
			if( \Input::has( $key ) )
				$data[ $key ] = \Input::get( $key );
			else
				$data[ $key ] = $val;
		}

		$stored = $this->model->create( $data );

		if( \Input::has('meta') )
			WidgetMeta::set( $stored, \Input::get('meta') );

		if( \Input::has('location_data') )
			WidgetLocation::set( $stored, \Input::get('location_data') );

		return $this->model->with(['meta_data','banner'])->find( $stored->id );
	}

	public function update($model)
	{
		if( \Input::has('target_id') )
			$model->target_id = \Input::get('target_id');

		if( \Input::has('sidebar_id') )
			$model->sidebar_id = \Input::get('sidebar_id');

		if( \Input::has('sort_order') )
			$model->sort_order = \Input::get('sort_order');

		if( \Input::has('type') )
			$model->type = \Input::get('type');

		$model->save();

		if( \Input::has('meta') )
			WidgetMeta::set( $model, \Input::get('meta') );

		if( \Input::has('location_data') )
			WidgetLocation::set( $model, \Input::get('location_data') );

		return $this->model->with(['meta_data','banner'])->find( $model->id );
	}

	public function updateOrder()
	{
		if( \Input::has('order') )
		{
			foreach( \Input::get('order') as $widget_id => $order )
			{
				$widget = $this->model->find( $widget_id );

				if( $widget )
				{
					$widget->sort_order = $order;
					$widget->save();
				}
			}
		}

		return ['success'];
	}
}