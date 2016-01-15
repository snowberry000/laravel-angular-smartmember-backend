<?php

namespace App\Models;

class UserMeta extends Root
{
    protected $table = "user_meta";

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public static function saveUserOption($user_options, $user_id, $site)
    {
        if ($user_options)
		{
            foreach ($user_options as $key => $value)
			{
                $existing_option = UserMeta::whereUserId($user_id)->whereKey($key)->whereSiteId( $site->id )->first();


                if (!$existing_option) {
                    $existing_option = new UserMeta();
                }

                $existing_option->user_id = $user_id;
                $existing_option->site_id = $site->id;
                $existing_option->key = $key;
                $existing_option->value = $value;
                $existing_option->save();
            }
        }
    }
}

UserOptions::saving(function($meta){

    $routes[] = 'user_';
    
    \SMCache::reset($routes);
    return $meta;
});

UserOptions::deleting(function($meta){

    $routes[] = 'user_';
    
    \SMCache::reset($routes);
    return $meta;
});
