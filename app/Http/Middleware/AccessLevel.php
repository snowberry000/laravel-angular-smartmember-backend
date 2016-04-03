<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\AccessLevel as AL;
use App\Helpers\SMAuthenticate;


use Closure;
use Auth;

class AccessLevel
{
    /**
     * Create a new filter instance.
     *
     * @return void
     */
    public function __construct(){

    }

    public function handle($request, Closure $next)
    {
        $model = $next($request);

	    if( isset($model->original) )
            $model = $model->original;

        $model->access = SMAuthenticate::checkAccessLevel($model);
        if($model->access){
            return $model;
        }
        else {
			$hide_attr = array();

			if( $model->access_level_type == 2 && !empty( $model->access_level_id ) )
			{
				$access_level = AL::find( $model->access_level_id );

				if( $access_level && $access_level->hide_unowned_content )
					$hide_attr[] = 'title';
			}

            if (!$model->transcript_content_public) {
                $hide_attr[] = 'transcript_content';
            }

            if (!$model->show_content_publicly) {
                $hide_attr[] = 'content';
            }
            $hide_attr = array_merge($hide_attr, ['embed_content' , 'featured_image' , 'audio_file']);

            $model->setHidden($hide_attr);
            $model->timeLeft = SMAuthenticate::determineTimeLeft($model);
            return $model;
        }
    }
}
