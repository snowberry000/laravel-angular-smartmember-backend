<?php

namespace App\Models;

class SiteTemplateData extends Root
{
    protected $table = "sites_templates_data";

    public function site(){
        return $this->belongsTo('App\Models\Site');
    }
    public function template(){
        return $this->belongsTo('App\Models\Template');
    }
    public function element_type(){
        return $this->belongsTo('App\Models\ElementType', "element_type_id");
    }

}
