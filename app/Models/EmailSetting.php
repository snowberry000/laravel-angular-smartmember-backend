<?php

namespace App\Models;
use SMCache;

class EmailSetting extends Root
{
    protected $table = "email_settings";

	public function setEmailSignatureAttribute( $value )
	{
		$this->attributes['email_signature'] = html_entity_decode( $value, ENT_QUOTES | ENT_HTML5 );
	}

	public function getEmailSignatureAttribute( $value )
	{
		return html_entity_decode( $value, ENT_QUOTES | ENT_HTML5 );
	}
}

EmailSetting::deleted(function($settings){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';
    
    SMCache::reset($routes);
    return $settings;
});

EmailSetting::saving(function($settings){

    //$company->permalink = Company::setPermalink($company);
    $routes[] = 'site_details';
    
    SMCache::reset($routes);
    return $settings;
});
