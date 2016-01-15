<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Template;

class TemplateController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new Template();
    }
}