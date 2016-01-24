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
        $this->middleware('admin',['except'=>array('index','show','store','update','save')]);
        $this->middleware('auth',['except'=>array('show','store','update')]);
    }

	public function save()
	{
		foreach( \Input::except(['sm_customer_id']) as $key => $val )
		{
			$data = [
				'key' => $key,
				'value' => $val
			];

			$sm_customer_id = \Input::get('sm_customer_id', 10);

			$this->model->create( $data, $sm_customer_id );
		}

		return [ 'success' => true ];
	}
}