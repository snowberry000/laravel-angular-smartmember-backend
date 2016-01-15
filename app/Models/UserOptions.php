<?php

namespace App\Models;

class UserOptions extends Root
{
    protected $table = "user_options";

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public static function saveUserOption($user_options, $user_id)
    {
        if ($user_options) {
            foreach ($user_options as $key => $value) {
                $existing_option = Useroptions::whereUserId($user_id)->whereMetaKey($key)->first();


                if (!$existing_option) {
                    $existing_option = new UserOptions();
                }
                $existing_option->user_id = $user_id;
                $existing_option->meta_key = $key;
                $existing_option->meta_value = $value;
                $existing_option->save();
            }
        }
    }
}

UserOptions::saving(function($options){

    $routes[] = 'user_';
    
    \SMCache::reset($routes);
    return $options;
});

UserOptions::deleting(function($options){

    $routes[] = 'user_';
    
    \SMCache::reset($routes);
    return $options;
});
