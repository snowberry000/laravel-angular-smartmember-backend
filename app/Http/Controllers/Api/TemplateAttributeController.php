<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\TemplateAttributes;

class TemplateAttributeController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new TemplateAttributes();
    }
}