<?php

namespace App\Models;

class Draft extends Root
{
    protected $table = "drafts";

    public function site(){
        return $this->belongsTo('App\Models\Site');
    }

    public static function create(array $data = array()){

        $draft = self::firstOrNew(['site_id' => $data['site_id'] , 'user_id' => isset($data['user_id']) ? $data['user_id'] : '' , 'key' => $data['key'] ]);
        $draft->fill(['value' => $data['value']])->save();
        return $draft;
    }
}
