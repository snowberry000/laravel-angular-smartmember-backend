<?php

namespace App\Models;

class TemplateAttributes extends Root
{
	
    protected $table = "templates_attributes";

    public function pages(){
        return $this->belongsTo('App\Models\Template');
    }

    public function element_type(){
        return $this->belongsTo('App\Models\ElementType', "element_type_id");
    }

}
