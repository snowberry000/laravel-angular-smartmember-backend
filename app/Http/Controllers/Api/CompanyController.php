<?php namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\User;
use App\Models\UserOptions;
use App\Helpers\SMAuthenticate;
use App\Helpers\CompanyHelper;
use App\Models\Directory;
use App\Models\Site;
use App\Models\Download;
use Auth;
use PRedis;

class CompanyController extends SMController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new Company();
    }

    public function getUsersCompanies()
    {
    	$companies = Company::getUsersCompanies();
        return $companies;
    }

	public function getUserCompaniesAndSites()
	{
		return Company::getUserCompaniesAndSites();
	}

	public function getUsersSitesAndTeams()
	{
		return Company::getUsersSitesAndTeams();
	}

	public function getCurrentCompany()
	{
		$current_company_id = Company::getOrSetCurrentCompany();

		return Company::find( $current_company_id );
	}

    public function getCurrentCompanyHash()
    {
        if(!$this->site) {
            $company = CompanyHelper::getCurrentCompany();
            if($company) {
                $company_id = $company['id'];
                return $this->model->find($company_id)->hash;
            }else {
                return null;
            }
        }
        else {
            if (isset($this->site->company_id))
            {
                $company_id = $this->site->company_id;
                return $this->model->find($company_id)->hash;
            } else {
                return null;
            }
        }
    }

    public function byPermalink($permalink){
        $company = Company::wherePermalink($permalink)->first();
        if(!$company){
            \App::abort('404' , 'No such company found');
        }
        $company_stored = PRedis::get('company:'.$company->id);
        if($company_stored){
            \Log::info('getting company from PRedis');
            return $company_stored;
        }
        $sites = Site::whereCompanyId($company->id)->get(["id"]);
        $company->sites = Directory::with(array('site' => function($q) {
                                $q->select('id', 'user_id', 'subdomain', 'domain', 'total_members','total_lessons','total_revenue');
                            }, 'site.user' => function($q) {
                                $q->select('id','first_name', 'last_name');
                            }))->where('is_approved', '1')
                            ->whereIn('site_id' , $sites)
                            ->get();
        $approved_sites = $company->sites->lists('site_id');
        $total_members = $total_lessons = $total_downloads = $total_revenue = $total_sites = 0;
        $download = new Download();
        foreach ($sites as $site_info)
        {
            $site = Site::find($site_info->id);
            $total_members += $site->total_members;
            $total_lessons += $site->total_lessons;
            $total_downloads += $download->getOne("select count(id) as total_downloads FROM download_center WHERE site_id = " . $site_info->id . " and access_level_type = 4 and deleted_at = NULL", 'total_downloads');
            $total_revenue += $site->total_revenue;
            $total_sites++;
        }

        $company->stats = \DB::table('sites')->select(\DB::raw('sum(total_members) as total_members , sum(total_revenue) as total_revenue , sum(total_lessons) as total_lessons'))->whereIn('id' , $approved_sites)->first();
        $company->stats->total_members = $total_members;
        $company->stats->total_lessons = $total_lessons;
        $company->stats->total_downloads = $total_downloads;
        $company->stats->total_revenue = $total_revenue;
        $company->stats->total_sites_count = $total_sites;

        $company->hide_total_lessons = intval($company->hide_lessons);
        $company->hide_members = intval($company->hide_members);
        $company->hide_total_downloads = intval($company->hide_downloads);
        $company->hide_revenue = intval($company->hide_revenue);
        $company->hide_sites = intval($company->hide_sites);

        PRedis::setex('company:'.$company->id, 24 * 60 * 60, $company);

        return $company;
    }

    public function store()
    {
        \App::abort(403, "This method is not allowed");
    }

	public function update($model)
	{
		$model = parent::update( $model );

		return $model;
	}

    // public function update($model)
    // {
    //    $this->model->save($model);
    //    $keys = PRedis::keys('company:'.$model->id);
    //    foreach ($keys as $key)
    //    {
    //        PRedis::del($key);
    //    }
    //    return $model;
    // }
}
