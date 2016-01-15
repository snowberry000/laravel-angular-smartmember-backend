<?php namespace App\Http\Controllers\Api;

use App\Models\AccessLevel\Pass;
use App\Models\User;
use App\Models\AccessLevel;


class AccessLedgerController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new Pass();   

    }

    public function index()
    {
        $passes = parent::paginateIndex();
        foreach ($passes['items'] as $pass) {
            $pass->user = User::find($pass->user_id);
            $pass->access_level = AccessLevel::find($pass->access_level_id);
        }
        return $passes;
    }

    public function destroy($model){
        $rsp = $model->revokePass();
        return parent::destroy($model);
    }
}