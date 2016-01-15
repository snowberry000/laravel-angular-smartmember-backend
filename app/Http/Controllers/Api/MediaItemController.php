<?php namespace App\Http\Controllers\Api;

use App\Models\AccessLevel\Pass;
use App\Models\User;
use App\Models\MediaItem;


class MediaItemController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new MediaItem();   
    }
}