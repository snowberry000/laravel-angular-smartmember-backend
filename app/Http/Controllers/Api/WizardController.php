<?php namespace App\Http\Controllers\Api;

use App\Models\Wizard;

class WizardController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new Wizard();   
    }

    public function store(){
    	return Wizard::customCreate(\Input::all());
    }

    public function index(){
    	return parent::index();
    }

}