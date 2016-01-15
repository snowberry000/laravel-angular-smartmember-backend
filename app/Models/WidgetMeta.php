<?php

namespace App\Models;

class WidgetMeta extends Root
{
    protected $table = "widget_meta";

    public function widget(){
        return $this->belongsTo('App\Models\Widget');
    }

    public static function set( $model, array $data = array() )
    {
        if ($data) {
            foreach ($data as $key => $value) {
                $meta = WidgetMeta::whereWidgetId($model->id)->whereKey($key)->first();
                if (!$meta) {
                    $meta = new WidgetMeta();
                }
                $meta->key = $key;
                $meta->value = $value;
                $meta->widget_id = $model->id;
                $meta->save();
            }
        }
    }
}
