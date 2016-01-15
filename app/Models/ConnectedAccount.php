<?php namespace App\Models;

class ConnectedAccount extends Root
{
    protected $table = 'connected_accounts';
    protected $type;
    protected $auth_type = 'oauth';

	public function app_configurations(){
		return $this->hasMany('App\Models\AppConfiguration');
	}

    public function connect($data){
    	$data["type"] =  $this->type;
        $exists = ConnectedAccount::whereType($data['type'])->whereSiteId($data['site_id'])->first();
        if($exists){
            $exists->update($data);
            return $exists;
        }else{
            $this->fill($data);
            $this->save();
        }

    	return $this;
    }

}
