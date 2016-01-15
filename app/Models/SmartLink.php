<?php

namespace App\Models;

class SmartLink extends Root
{
    protected $table = "smart_links";
	protected $with = ['urls'];

	public function urls()
	{
		return $this->hasMany('App\Models\SmartLinkUrl')->orderBy( 'order' );
	}

	public static function setPermalink( $length = 6 )
	{
		$alphabet    = "abcdefghijklmnopqrstuwxyz0123456789";
		$pass        = array(); //remember to declare $pass as an array
		$alphaLength = strlen( $alphabet ) - 1; //put the length -1 in cache

		for( $i = 0; $i < $length; $i++ )
		{
			$n      = rand( 0, $alphaLength );
			$pass[] = $alphabet[ $n ];
		}
		return implode( $pass );
	}

	public function applySearchQuery($q, $value)
	{
		if(!empty($value))
			return $q->where( function($query) use ($value) {
				$query->where( 'title', 'like','%' . $value . "%");
				$query->orwhere( 'permalink', 'like','%' . $value . "%");
			});
		else
			return $q;
	}

	public function setLastUrl( $order )
	{
		$this->last_url_id = $order;
		$this->save();
	}

	public static function getNextUrl( $id )
	{
		$smart_link = self::find( $id );

		if( $smart_link )
		{
			if( isset( $smart_link->urls ) && !empty( $smart_link->urls ) && count( $smart_link->urls ) > 0 )
			{
				foreach( $smart_link->urls as $key=>$val )
					if( !$val->enabled )
						unset( $smart_link->urls[ $key ] );

				if( count( $smart_link->urls ) > 0)
				{
					switch( $smart_link->type )
					{
						case 'random':
							$random = rand( 0, count( $smart_link->urls ) - 1 );

							$url = $smart_link->urls[ $random ];
							break;
						case 'sequential':
							foreach( $smart_link->urls as $val )
								if( $val->order == $smart_link->last_url_id + 1 )
									$url = $val;

							if( empty( $url ) )
								$url = $smart_link->urls[ 0 ];
							break;
						case 'least_hit':
							foreach( $smart_link->urls as $val )
							{
								if( empty( $url ) || $val->visits < $url->visits )
								{
									$url = $val;
									if( !$url->visits )
										$url->visits = 0;
								}
							}
							break;
						case 'weighted':
							$links = [ ];

							foreach( $smart_link->urls as $key => $val )
							{
								if( $val->weight )
								{
									for( $x = 0; $x < $val->weight; $x++ )
										$links[] = $key;
								}
							}

							if( !empty( $links ) )
								$url = $smart_link->urls[ intval( $links[ array_rand( $links ) ] ) ];
							break;
					}
				}
			}

			if( !empty( $url ) )
			{
				if( $url->visits )
					$url->visits++;
				else
					$url->visits = 1;

				$url->save();

				$smart_link->setLastUrl( $url->order );

				return $url->url;
			}
		}

		return '/';
	}
}

SmartLink::creating(function($model){
	if( !isset( $model->permalink ) || empty( $model->permalink ) )
	{
		$model->permalink = SmartLink::setPermalink();
		$query = SmartLink::wherePermalink( $model->permalink );

		if( isset( $model->id ) )
			$query = $query->where( 'id', '!=', $model->id );

		if ( $query->first() )
			$model->permalink = SmartLink::setPermalink();

		\Input::merge( ['permalink' => $model->permalink ] );
	}
});

SmartLink::created(function($model){
	$model->permalink = \App\Models\Permalink::set($model);
	$model->save();
	return $model;
});