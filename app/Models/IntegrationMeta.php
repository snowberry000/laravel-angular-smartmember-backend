<?php

namespace App\Models;

class IntegrationMeta extends Root
{
    protected $table = "integration_meta";

    public function connected_app(){
        return $this->belongsTo('App\Models\AppConfiguration');
    }

    public static function set($model, array $app_configuration_instanceData = array())
    {
        if ($app_configuration_instanceData) {
            foreach ($app_configuration_instanceData as $key => $value) {
                $meta = IntegrationMeta::whereIntegrationId($model->id)->whereKey($key)->first();
                if (!$meta) {
                    $meta = new IntegrationMeta();
                }
                $meta->key = $value['key'];
                $meta->value = $value['value'];
                $meta->integration_id = $model->id;
                $meta->save();
            }
        }

    }
}
