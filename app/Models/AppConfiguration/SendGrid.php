<?php namespace App\Models\AppConfiguration;

use Exception;

class SendGrid extends \SendGrid {

	public function send(\SendGrid\Email $email)
    {
    	if (\Config::get('vars.email_lock'))
    	{
    		try 
    		{
    			\Log::info($email->toWebFormat());
    		}
    		catch (Exception $e)
    		{
    			\Log::info($email->getText());
    		}
    		
    	}
    	else
    		return parent::send($email);
    }
}