<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\AffiliateLeaderboard;
use App\Models\AffiliateContest;
use App\Models\AffiliateTeam;
use App\Models\AffiliateTeamLedger;
use App\Models\Affiliate;

class AffiliateLeaderboardController extends ApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new AffiliateLeaderboard();
        $this->middleware('admin' ,['except' => ['show']]); 
        $this->middleware('auth' ,['except' => ['show']]); 
    }

    public function index()
    {
        //$current_company_id = Company::getOrSetCurrentCompany();
        
        $page_size = config("vars.default_page_size");
        $query = $this->model;
        $query = $query->take($page_size);
        $query = $query->orderBy('id' , 'DESC');
        //$query = $query->whereCompanyId( $current_company_id );
        $query = $query->whereSiteId($this->site->id);
        $query = $query->whereNull('deleted_at');
        foreach (\Input::all() as $key => $value){
            switch($key){
                case 'q':
                    $query = $this->model->applySearchQuery($query,$value,$this->site);
                    break;
                case 'p':
                    $query->skip((\Input::get('p')-1)*$page_size);
                    break;
                default:
            }
        }

        return $query->get();
    }

    
    public function show($id)
    {
        $contest = AffiliateContest::find($id);
        if ( !$contest ) return array();

        $data = [];
        AffiliateLeaderboard::generateLeaderboard($contest);
        $leaderboard = AffiliateLeaderboard::where('contest_id', $id)->orderby('rank', 'desc')->get()->toArray();
        $data ['contest'] = $contest;
        $affiliate_teams = AffiliateTeam::with('members')->whereSiteId($contest->site_id)->get();
        $affid_arr = array();
        $already_replaced = array();

        for ($i = 0; $i < count($leaderboard); $i++)
        {
            $affid_arr[] = $leaderboard[$i]['affiliate_id'];
        }
        //dd($affiliate_teams);

        foreach ($affiliate_teams as $affiliate_team)
        {
            foreach ($affiliate_team->members as $member)
            {
                $key = array_search($member->affiliate->user_id, $affid_arr);
                if ($key !== FALSE)
                {
                    if (!array_key_exists($affiliate_team->name, $already_replaced))
                    {
                        $already_replaced[$affiliate_team->name] = $key;
                        $leaderboard[$key]['affiliate_name'] = $affiliate_team->name;
                    } else {

                        $team_pos = $already_replaced[$affiliate_team->name];
                        $leaderboard[$team_pos]['rank'] += $leaderboard[$key]['rank'];
                        unset($leaderboard[$key]);

                    }
                }
            }
        }
        $leaderboard = array_values($leaderboard);
        $sort = array();
        foreach ($leaderboard as $key => $value)
        {
            $sort[$key] = $value['rank'];
        }

        array_multisort($sort, SORT_DESC, $leaderboard);
        for ($i = 0; $i < count($leaderboard); $i++)
        {
            //check if the affiliate belongs to a team
            $v = $leaderboard[$i];
            $n = FALSE;
            if ($i < count($leaderboard) - 1)
                $n = $leaderboard[$i + 1];

            $ranking = [];
            $ranking['position'] = $i + 1;
            $ranking['rank'] = $v['rank'];
            $ranking['standing'] = 'Good';
            $ranking['affiliate_name'] = $v['affiliate_name'];

            $ranking['ahead'] = -1;
            if ($n)
            {
                if ( ($v['rank'] - $n['rank']) > 0 )
                {
                    $ranking['ahead'] = $v['rank'] - $n['rank'];
                    $ranking['ahead_of'] = $n['affiliate_name'];
                }
            }

            $data['updated_at'] = $v['updated_at'];
            $data['ranking'][] = $ranking;
        }

        $data["total_ranks"] = AffiliateLeaderboard::getTotal($id);

        return $data;
    }
}