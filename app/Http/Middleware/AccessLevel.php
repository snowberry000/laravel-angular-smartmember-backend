<?php

namespace App\Http\Middleware;

use App\Models\User;
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
        $model = $model->original;

        $model->access = SMAuthenticate::checkAccessLevel($model);
        if($model->access){
            return $model;
        }
        else {
            $hide_attr = array();
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
