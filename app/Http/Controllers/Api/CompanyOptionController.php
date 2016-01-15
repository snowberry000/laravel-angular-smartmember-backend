<?php namespace App\Http\Controllers\Api;

use App\Models\CompanyOption;
use App\Models\User;
use App\Helpers\SMAuthenticate;
use Auth;

class CompanyOptionController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new CompanyOption();

    }

    public function index()
    {
        $current_company_id = \Auth::user()->options('current_company_id')->first()->meta_value;

        //return $this->model->with(['open'])->whereCompanyId($current_company_id)->get();
        return $this->model->whereCompanyId($current_company_id)->whereNull('deleted_at')->get();
    }
}
