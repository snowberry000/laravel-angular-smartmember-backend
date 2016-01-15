<?php namespace App\Models\BridgePage;

use App\Models\Root;

class BridgeTemplate extends Root
{
    protected $table = 'bridge_templates';

    public function type()
    {
        return $this->belongsTo("App\Models\BridgePage\BridgeType", "type_id");
    }
}