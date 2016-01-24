<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\CustomAttribute;
use App\Models\MemberMeta;
use App\Models\User;
use App\Helpers\SMAuthenticate;
use Input;

class MemberMetaController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new MemberMeta();
        $this->middleware('admin',['except'=>array('index','show','store','update')]);
        $this->middleware('auth',['except'=>array('show','store','update')]);
    }
}