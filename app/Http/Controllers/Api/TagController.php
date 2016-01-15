<?php namespace App\Http\Controllers\Api;

use App\Models\Tag;
use App\Models\User;

class TagController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new Tag();       
    }

}