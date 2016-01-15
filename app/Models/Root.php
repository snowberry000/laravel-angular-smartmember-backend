<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes as SoftDeletingTrait;

abstract class Root extends Model{
    
    use SoftDeletingTrait;
    
    //There attributes will not be mass assigned
    protected $guarded = array('id', 'deleted_at', 'updated_at', 'created_at');
    

    public function checkPermalink( $checked_once = false ){
    	//Make sure Permalink doesnt

	    if (!$this->permalink){
	        $this->permalink = str_replace(" ", "-", trim($this->title)); 
	    }

        $entry = isset( $this->id ) ? self::wherePermalink($this->permalink)->where('id','!=',$this->id)->whereSiteId($this->site_id)->first() : self::wherePermalink($this->permalink)->whereSiteId($this->site_id)->first();

	    if ($entry){

            if( $checked_once )
            {
                $last_hyphen = strrpos( $this->permalink, '-' );
                $count = intval( substr( $this->permalink, strrpos( $this->permalink, '-' ) + 1 ) ) + 1;
                $this->permalink = substr( $this->permalink, 0, $last_hyphen + 1 ) . $count;
            }
            else
            {
                $this->permalink .= "-" . 2;
            }
            return self::checkPermalink( true );
	    }

	    return $this;
    }

    public function getOne($query , $column){
    	$data = \DB::select($query);
    	if($data){
    		if(!$data[0]->$column)
                return 0;
            else
                return $data[0]->$column;
    	}
    	return 0;
    }

    public static function getMeta($permalink, $linkType, $site=false){
        if( $site )
            $model = self::wherePermalink($permalink)->whereSiteId($site->id)->first();
        else
            $model = self::wherePermalink($permalink)->first();

        if (isset($model->id))
        {
            $meta = array(
                "title" => $model->title,
                "image" => $model->featured_image
            );

            $seo_settings = \App\Models\SeoSetting::whereLinkType($linkType)->whereTargetId($model->id)->get();

            if ($seo_settings){
                foreach ($seo_settings as $setting){
                    if ($setting["meta_key"] == "fb_share_title"){
                        $meta["title"] = $setting["meta_value"];
                    }else if ($setting["meta_key"] == "fb_share_description"){
                        $meta["description"] = $setting["meta_value"];
                    }else if ($setting["meta_key"] == "fb_share_image"){
                        $meta["image"] = $setting["meta_value"];
                    }else if ($setting["meta_key"] == "fb_retargeting_pixel_id"){
                        $meta["fb_retargeting_pixel_id"] = $setting["meta_value"];
                    }else if ($setting["meta_key"] == "fb_conversion_tracking_pixel_id"){
                        $meta["fb_conversion_tracking_pixel_id"] = $setting["meta_value"];
                    }
                }
            }
            return $meta;
        }

    }
}

/*

\DB::enableQueryLog();
\DB::listen(
    function ($sql, $bindings, $time) {
        \Log::info("TIME : $time " . $sql);
    }
);  

*/

?>