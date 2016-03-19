<?php namespace App\Http\Controllers\Api;

class ReviewController extends SMController {
	
	public function __construct()
    {
        parent::__construct();
        $this->model = new Review();
    }
}