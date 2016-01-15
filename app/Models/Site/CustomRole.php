<?php namespace App\Models\Site;

use App\Models\Root;

class CustomRole extends Root{
    protected $table = 'sites_custom_roles';

    public function capabilities(){
    	return $this->hasMany('App\Models\Site\Capability',"name","type");
    }

    public static function getCapabilities($site_id,$type){
        $data = [];
    	$capabilities = Capability::whereSiteId($site_id)->whereType($type)->get();
    	foreach ($capabilities as $cap){
    		$data[] = $cap->capability;
    	}
    	return $data;
    }
    
}