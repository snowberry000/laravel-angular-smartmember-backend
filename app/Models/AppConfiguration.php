<?php namespace App\Models;

use App\Models\AppConfiguration\ConstantContact;
use App\Models\Site;
use App\Models\AppConfiguration\GetResponse;
use App\Http\Controllers\Api\SiteController;
use SMCache;

class AppConfiguration extends Root
{
    protected $table = 'app_configurations';
    protected $type;
    protected $auth_type = 'oauth';
	protected $with = ['meta_data'];

	public function site(){
		return $this->belongsTo('App\Models\Site');
	}

	public function account(){
		return $this->belongsTo('App\Models\ConnectedAccount','connected_account_id','id');
	}

	public function meta_data()
	{
		return $this->hasMany('App\Models\IntegrationMeta','integration_id');
	}

	public static function AddMemberToEmailListIntegrationForSite($site, $member)
	{
		$site_id = $site->id;
		$app_configuration_instance_types = array('getresponse','aweber','constantcontact');
		$all_integrations = AppConfiguration::where(function($query) use ( $site_id ){
			$query->where('site_id', $site_id );
		})->with(['site','account','meta_data'])->whereIn('type', $app_configuration_instance_types)->whereDisabled(0)->get();

		foreach ($all_integrations as $app_configuration_instance)
		{
			switch ($app_configuration_instance->type)
			{
				/*case 'aweber':
					AweberAppConfiguration::addMemberToList($app_configuration_instance->meta_data->optin_member_list_id, $subscriber, $app_configuration_instance->remote_id, $app_configuration_instance->access_token);
					break;*/
				case 'getresponse':
					foreach ($app_configuration_instance->meta_data as $meta_data)
					{
						if ($meta_data->key == 'optin_member_campaign_id')
						{
							$campaign_id = $meta_data->value;
						}
					}
					if (isset($campaign_id))
					{
						GetResponse::addMemberToList($campaign_id, $member, $app_configuration_instance->remote_id);
					}

					break;
				case 'constantcontact':
					foreach ($app_configuration_instance->meta_data as $meta_data)
					{
						if ($meta_data->key == 'optin_member_list_id')
						{
							$list_id = $meta_data->value;
						}
					}
					if (isset($list_id))
					{
						ConstantContact::addMemberToList($list_id, $member, $app_configuration_instance->account->access_token);
					}
					break;
			}
		}
	}

	public static function create(array $app_configuration_instanceData = array())
	{
		if (isset($app_configuration_instanceData["additional_options"])){
			$additional_data = $app_configuration_instanceData["additional_options"];
			unset($app_configuration_instanceData['additional_options']);
		}
		$app_configuration_instance = parent::create($app_configuration_instanceData);
		$app_configuration_instance->save();

		if (isset($additional_data))
		{
			IntegrationMeta::set($app_configuration_instance, $additional_data);
		}

		return $app_configuration_instance;
	}

	public function update(array $app_configuration_instanceData = array())
	{
		if (isset($app_configuration_instanceData['additional_options']) && !empty($app_configuration_instanceData['additional_options']))
		{
			$additional_data = $app_configuration_instanceData['additional_options'];
			IntegrationMeta::set($this, $additional_data);
		}
		unset($app_configuration_instanceData['additional_options']);

		$this->fill($app_configuration_instanceData);
		$this->save();

		return $this;
	}
}

AppConfiguration::deleted(function($app_configuration_instance){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';
    
    SMCache::reset($routes);
    return $app_configuration_instance;
});

AppConfiguration::saving(function($app_configuration_instance){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';
    
    SMCache::reset($routes);
    return $app_configuration_instance;
});