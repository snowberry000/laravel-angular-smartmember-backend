<?php

namespace App\Models;

class CustomAttribute extends Root
{
    protected $table = "custom_attributes";

	public function event()
	{
		return $this->belongsTo('App\Models\Event');
	}

	public function UpdateType( $type )
	{
		if( $this->type != 'bool' && $this->type != $type )
		{
			$this->type = $type;
			$this->save();
		}
	}
}