<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;

use Auth;
use App\Models\Role;

class SMController extends ApiController
{
	protected $site;
	protected $is_admin = false;

	public function __construct(){
		parent::__construct();
		$this->site = \Domain::getSite();
		//$this->company_sites = $this->_getCompany();
		$this->__setUser();
	}

	private function __setUser(){
		//Set if user is an admin
		if (isset($this->site->id) && \SMRole::hasAccess($this->site->id,'manage_content') ){
        	$this->is_admin = true;
		}
	}

	private function __requireAdmin(){
		if (!$this->is_admin){
			\App::abort("403","You don't have access to this resource");
		}
	}

}
