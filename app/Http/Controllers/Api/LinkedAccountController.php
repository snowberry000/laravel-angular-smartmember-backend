<?php namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\LinkedAccount;
use Auth;

class LinkedAccountController extends SMController
{
    public function __construct(){
        parent::__construct();
        $this->model = new LinkedAccount();   

    }

    public function togglePrimary(){
        $id = \Input::get('id');
        $user_id = isset(\Auth::user()->id) ? \Auth::user()->id : null;
        if(!isset($id) || !isset($user_id)){
            return array('success' => false);
        }
        return $this->model->togglePrimary($user_id , $id);
    }

    public function link(){
        $email = \Input::get('email');
        $user_id = isset(\Auth::user()->id) ? \Auth::user()->id : null;
        if(!isset($email) || !isset($user_id)){
            return array('success' => false);
        }
        return $this->model->link($user_id , $email);
    }

    public function claim(){
        $id = \Input::get('id');
        $user_id = isset(\Auth::user()->id) ? \Auth::user()->id : null;
        if(!isset($id) || !isset($user_id)){
            return array('success' => false);
        }
        return $this->model->claim($user_id , $id);
    }

    public function merge(){
        if (Auth::user())
        {
            return $this->model->merge(\Input::get('verification_hash', FALSE) , Auth::user()->id);
        }
    }
}