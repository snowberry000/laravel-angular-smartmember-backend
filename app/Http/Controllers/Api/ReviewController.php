<?php namespace App\Http\Controllers\Api;

use App\Models\Review;

class ReviewController extends SMController {
	
	public function __construct()
    {
        parent::__construct();
        $this->model = new Review();
    }
}