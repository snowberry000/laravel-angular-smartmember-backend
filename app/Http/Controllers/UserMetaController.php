<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\UserMeta;
use App\Models\User;
use Input;

class UserMetaController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new UserMeta();
    }

}