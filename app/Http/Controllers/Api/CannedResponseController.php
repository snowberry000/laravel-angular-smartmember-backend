<?php namespace App\Http\Controllers\Api;

use App\Models\CannedResponse;

class CannedResponseController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new CannedResponse();       
    }

}