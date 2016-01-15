<?php namespace App\Models;


class Permalink extends Root
{
    protected $table = 'permalinks';

    public static function set($model){

    	$permalink = self::whereSiteId($model->site_id)->whereTargetId($model->id)->whereType($model->getTable())->first();
    	if ($permalink && !\Input::has('permalink')){
    		return $permalink->permalink;
    	}

        $text = str_replace(" ", "-", trim($model->title));
        if (\Input::get('permalink')){
            $text = str_replace(" ", "-", trim(\Input::get('permalink')));
        }
    	$text = self::handleDuplicate($text,$model);

		if( $permalink )
		{
			$permalink->permalink = $text;
			$permalink->save();

			//we were creating lots of duplicate permalinks, this gets rid of any remaining ones
			$extra_permalinks = self::whereSiteId($model->site_id)->whereTargetId($model->id)->whereType($model->getTable())->where( 'id','!=',$permalink->id)->get();
			foreach( $extra_permalinks as $extra_permalink )
				$extra_permalink->delete();
		}
		else
		{
			self::create( array(
				"permalink" => $text,
				"site_id" => $model->site_id,
				"target_id" => $model->id,
				"type" => $model->getTable()
			) );
		}

    	return $text;
    }

    public static function handleDuplicate($text, $model){

		if (!empty($text))
		{
			$if_exists = self::whereSiteId($model->site_id)->wherePermalink($text);

			if( $model->id )
				$if_exists = $if_exists->where(function($q) use ($model){
					$q->where('target_id','!=',$model->id);
					$q->orwhere('type','!=', $model->getTable() );
				});

			$if_exists = $if_exists->first();

			if ($if_exists){
				$last_char = $text{strlen($text)-1};
				if ($text{strlen($text)-2} == '-' && is_numeric($last_char)){
					$last_char = intval($last_char) + 1;
					$text{strlen($text)-1} = $last_char;
				}else{
					$text .= "-1";
				}

				return self::handleDuplicate($text,$model);
			} else {
				return $text;
			}

		}
    }

    public static function handleReservedWords($model){
        $reserved_words = array(    'admin' , 'my' , 'checkout' , 'service' , 'download-center' , 'blog' , 'page' ,
                                    'lesson' , 'lessons' , 'download' , 'sign' , 'support' , 'support-tickets' , 'support-ticket',
									'thankyou', 'thank-you'
                                );
        $text = str_replace(" ", "-", trim($model->title));
        if (\Input::get('permalink')){
            $text = str_replace(" ", "-", trim(\Input::get('permalink')));
        }
        \Log::info($text);
        if (in_array($text, $reserved_words)) {
            \App::abort('403','A reserved keyword cannot be used as permalink');
        }
    }


}
