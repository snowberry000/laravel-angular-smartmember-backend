<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Affiliate;
use App\Models\Site;
use App;
use Input;
use Auth;
use App\Helpers\SimpleHtmlDom;

class AffiliateController extends SMController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new Affiliate();
        $this->middleware('admin', ['except'=>array('processJVZooData')]);
        $this->middleware('auth', ['except'=>array('processJVZooData')]);
    }

    public function summary() {
        
        //$current_company_id = Company::getOrSetCurrentCompany();
        //if (!$current_company_id) return array('success' => false);

        //$company = Company::find($current_company_id);

        $summary['total_affiliates'] = Affiliate::where('site_id', $this->site->id)->count();
        $summary['affiliates_per_day'] = $this->model->getOne(
            "SELECT AVG(affiliates_per_day) as affiliates_per_day 
                FROM (SELECT count(*) as affiliates_per_day FROM affiliates where site_id = " .
                $this->site->id . "  group by DATE(created_at)) x",
            "affiliates_per_day");

        $summary["last_affiliate_joined"] = Affiliate::where('site_id', $this->site->id)
                                              ->orderBy('created_at', 'DESC')
                                              ->select(['created_at'])->first();

        $summary["affiliates_today"] = $this->model->getOne(
            "SELECT count(*) as affiliates from affiliates where site_id = " . $this->site->id .
            " and DATE(created_at) = CURDATE()", "affiliates");

         $summary["affiliates_yesterday"] = $this->model->getOne(
            "SELECT count(*) as affiliates from affiliates where site_id = " . $this->site->id .
            " and DATE(created_at) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))", "affiliates");

         $summary["affiliates_this_week"] = $this->model->getOne(
                "SELECT count(*) as affiliates from affiliates where site_id = " .  $this->site->id .
                " and DATE(created_at) > DATE(DATE_SUB(NOW(), INTERVAL 7 DAY))",
                "affiliates"
            );
         $summary["affiliates_last_week"] = $this->model->getOne(
                "SELECT count(*) as affiliates from affiliates where site_id = " .  $this->site->id .
                " and DATE(created_at) > DATE(DATE_SUB(NOW(), INTERVAL 14 DAY)) ".
                " and DATE(created_at) < DATE(DATE_SUB(NOW(), INTERVAL 7 DAY)) ",
                "affiliates"
            );
         $summary["affiliates_this_month"] = $this->model->getOne(
                "SELECT count(*) as affiliates  from affiliates where site_id = " .  $this->site->id .
                 " and MONTH(created_at) = MONTH(NOW())",
                 "affiliates");
         $summary["affiliates_last_month"] = $this->model->getOne(
                "SELECT count(*) as affiliates  from affiliates where site_id = " .   $this->site->id .
                " and MONTH(created_at) = MONTH(NOW() - INTERVAL 1 MONTH)",
                "affiliates"
            );

         $summary["affiliates_overtime"] = Affiliate::where('site_id',  $this->site->id)
              ->groupBy(\DB::raw("year, month"))
              ->select(\DB::raw("count(*) as affiliates, YEAR(`created_at`) as year, MONTH(`created_at`) as month"))
              ->orderBy(\DB::raw("year, month"))
              ->get();
          
        return $summary;
    }

    public function index() 
    {
        //$current_company_id = Company::getOrSetCurrentCompany();
        
        //if ($current_company_id)
        //{
			$page_size = config("vars.default_page_size");
			$query = $this->model->whereSiteId( $this->site->id );

			$query = $query->whereNull('deleted_at');
			foreach (Input::all() as $key => $value){
				switch($key){
					case 'q':
						$query = $this->model->applySearchQuery($query,$value);
						break;
                    case 'order_by':
                        $query = $query->orderBy($value , 'DESC');
                        break;
					case 'view':
					case 'p':
					case 'bypass_paging':
						break;
					default:
						$query->where($key,'=',$value);
				}
			}

			$return = [];

			$return['total_count'] = $query->count();

			if( !Input::has('bypass_paging') || !Input::get('bypass_paging') )
				$query = $query->take($page_size);

			if( Input::has('p') )
				$query->skip((Input::get('p')-1)*$page_size);

			$return['items'] = $query->get();

			return $return;
        /*}
        else
        {
            App::abort(408, "You must be signed in to a team to see the list of affiliates.");
        }*/
       
    }

    public function processJVZooData( $hash )
    {
        $content = $_POST['content'];

		$site = Site::whereHash( $hash )->first();

        if (strpos($_POST['url'], 'affiliaterequests') !== FALSE) {
            $data = $this->ParseAffiliateRequestTable($content);
        } else if (strpos($_POST['url'], 'youraffiliates') !== FALSE) {
            $data = $this->ParseMyAffiliatesTable($content);
        }

        if( !empty( $data ) ) {
            foreach ($data as $key => $value)
            {
                $affiliate = $this->model->whereAffiliateRequestId( $value['affiliate_request_id'])->whereSiteId( $site->id )->first();

                if( !empty( $affiliate ) )
                    continue;

                $fields = array();
                $fields['affiliate_request_id'] = $value['affiliate_request_id'];
                $fields['user_id'] = $value['jvzoo_user_id'];
                $fields['user_name'] = $this->TrimNameJunk($value['jvzoo_user_name']);
                $fields['user_email'] = !empty( $value['jvzoo_user_email'] ) ? $value['jvzoo_user_email'] : '';
                $fields['user_country'] = !empty( $value['jvzoo_user_country'] ) ? $value['jvzoo_user_country'] : '';
                $fields['user_note'] = !empty( $value['jvzoo_user_note'] ) ? $value['jvzoo_user_note'] : '';
                $fields['past_sales'] = !empty( $value['past_sales'] ) ? $value['past_sales'] : '';
                $fields['product_name'] = !empty( $value['product_name'] ) ? $value['product_name'] : '';
                $fields['site_id'] = $site->id;
                $result = Affiliate::firstOrCreate( $fields );
            }
        }
    }

    function ParseMyAffiliatesTable($html)
    {
        $dom = new SimpleHtmlDom();

        $dom->load($html);
        $rows = $dom->find('tr');

        $data = array();
        $iter = 0;

        foreach ($rows as $key => $value)
        {
            $iter++;

            if ($iter <= 1) {
                continue;
            }

            $fields = array();

            // affiliate request id
            $fields['affiliate_request_id'] = $value->find('td', 0)->find('input', 0)->value;

            // ******************************* user stuff
            // user id
            $bits = $value->find('td', 1)->find('a', 0)->href;
            $bits = explode('/', $bits);

            $fields['jvzoo_user_id'] = trim($bits[count($bits) - 1]);

            // user name
            $bits = explode('<a', $value->find('td', 1)->innertext);

            $fields['jvzoo_user_name'] = trim($bits[0]);

            // user note
            $fields['jvzoo_user_note'] = trim($value->find('td', 5)->innertext);
            // *********************************
            // product they're an affiliate for
            $bits = explode('<b>', $value->find('td', 2)->innertext);
            $fields['product_name'] = trim($bits[0]);

            // commission type
            $bits = explode('</span>', $value->find('td', 3)->innertext);
            $bits = explode('">', $bits[0]);

            $fields['commission_type'] = trim($bits[1]);

            // commission amount
            $bits = explode('</span>', $value->find('td', 4)->innertext);
            $bits = explode('">', $bits[0]);

            $fields['commission_amount'] = trim($bits[1]);

            foreach ($value->find('td') as $key2 => $value2) {
                $fields['original'][] = $value2->outertext;
                //$this->ItemDebug( $value2->plaintext );
            }

            $data[] = $fields;
        }
        return $data;
    }

    function TrimNameJunk($name) {
        $bits = explode('<', $name);

        return $bits[0];
    }

    public function ParseAffiliateRequestTable($html) {

        $dom = new SimpleHtmlDom();
        $dom->load($html);
        $rows = $dom->find('tr');

        $data = array();
        $iter = 0;

        foreach ($rows as $key => $value) {
            $iter++;

            if ($iter <= 1) {
                continue;
            }

            $fields = array();

            // affiliate request id
            $fields['affiliate_request_id'] = trim($value->find('td', 0)->find('input', 0)->value); //;//.type;
            // ******************************* user stuff
            // user id
            $bits = $value->find('td', 1)->find('a', 0)->href;
            $bits = explode('/', $bits);

            $fields['jvzoo_user_id'] = trim($bits[count($bits) - 1]);

            // user name
            $bits = explode('<a', $value->find('td', 1)->innertext);

            $fields['jvzoo_user_name'] = trim($bits[0]);

            // user email
            $bits = explode('<br>', $value->find('td', 1)->innertext);

            $fields['jvzoo_user_email'] = trim($bits[count($bits) - 1]);

            // user country
            $bits = explode('</a> (', $value->find('td', 1)->innertext);
            $bits = explode(')', $bits[1]);

            $fields['jvzoo_user_country'] = trim($bits[0]);

            // reason for application
            $fields['jvzoo_user_note'] = $value->find('td', 5)->innertext;
            // *********************************
            // total sales in the past
            $fields['past_sales'] = $this->GetNumberFromString(trim($value->find('td', 2)->innertext));

            // action on request
            $fields['actions'] = ''; //$value->find('td', 3)->outertext;
            // product applying for
            $fields['product_name'] = trim($value->find('td', 4)->innertext);

            foreach ($value->find('td') as $key2 => $value2) {
                $fields['original'][] = $value2->outertext;
                //$this->ItemDebug( $value2->plaintext );
            }

            $data[] = $fields;
        }
        return $data;
    }

    public function store()
    {
        //$current_company_id = Company::getOrSetCurrentCompany();

        /*if( empty( $current_company_id ) )
            App::abort(408, "You must be signed in to a team to add a subscriber");*/

        \Input::merge(array('site_id'=>$this->site->id));
        return parent::store();
    }


    public function GetNumberFromString($text) {
        $int = preg_replace("/[^0-9]/", "", $text);

        if (strpos($text, '<') !== FALSE) {
            $int -= 1;
        }

        return (int) $int;
    }
}