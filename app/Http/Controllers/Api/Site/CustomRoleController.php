<?php namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Api\SMController;
use App\Models\Site\CustomRole;

class CustomRoleController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new CustomRole();
    }
    
}