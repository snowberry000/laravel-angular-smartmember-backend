<?php

namespace App\Models;

class Template extends Root
{
    protected $table = "templates";

    public function pages(){
        return $this->hasMany('App\Models\Site');
    }
    public function attributes(){
        return $this->hasMany('App\Models\TemplateAttributes');
    }


}
