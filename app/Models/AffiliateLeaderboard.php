<?php

namespace App\Models;
use Carbon\Carbon;

class AffiliateLeaderboard extends Root
{
    protected $table = "affleaderboards";

    public static function getTotal($contest_id){
        $totals = \DB::select('select sum(rank) as total from affleaderboards where contest_id = ' . $contest_id);
        if ($totals){
           return $totals[0]->total;
        }
        return 0;
    }


    public static function generateLeaderboardLeads($contest)
    {
        if (!static::needsUpdating($contest)) return;


        // First of all delete leaderboard for this contest
        static::deleteLeaderboardForContest($contest);

        $sql = "SELECT um.id, um.value as affiliate_id, a.user_name as affiliate_name, a.user_email as email, count( DISTINCT um.id ) as rank
                FROM user_meta um
                INNER JOIN affiliates a ON um.value = a.user_id AND um.key = 'aid'
                WHERE um.site_id = ". $contest->site_id . "
                                                  AND um.created_at >= DATE('" . $contest->start_date . "')
                                                 AND um.created_at <= DATE('" . $contest->end_date . "')
                                                 AND um.deleted_at is null
                                                 AND a.deleted_at is null
                                                 AND a.site_id = " . $contest->site_id . "
                GROUP BY a.user_id
                ORDER BY rank DESC";

        $result = \DB::select( \DB::raw($sql) );

        if (!empty($result) && count($result) > 0)
        {
            foreach($result as $key=>$value)
            {
				if( $value->affiliate_id == '159327' )
					continue;

                $fields = array();
                $fields['contest_id'] = $contest->id;
                $fields['affiliate_id'] = $value->affiliate_id;
                $fields['affiliate_name'] = $value->affiliate_name;
                $fields['rank'] = $value->rank;

                AffiliateLeaderboard::create($fields);
            }
        }       
    }

    public static function generateLeaderboardSales($contest)
    {
        if (!static::needsUpdating($contest)) return;

        $sites = $contest->sites()->lists('site_id')->toArray();
        $sites = implode(',', $sites);
        
        // First of all delete leaderboard for this contest
        static::deleteLeaderboardForContest($contest);


        $sql = "SELECT t.affiliate_id, a.user_name as affiliate_name, count(*) as rank from transactions t 
                INNER JOIN  affiliates a 
                ON a.id = t.affiliate_id  
                WHERE t.site_id IN (" . $sites . ") AND t.created_at >= DATE('" . $contest->start_date . "')
                                                   AND t.created_at <= DATE('" . $contest->end_date . "')
                GROUP BY t.affiliate_id 
                ORDER BY rank DESC, user_name
                LIMIT 10";

        $result = \DB::select( \DB::raw($sql) ); 
        if (!empty($result) && count($result) > 0)
        {
            foreach($result as $key=>$value)
            {
                $fields = array();
                $fields['contest_id'] = $contest->id;
                $fields['affiliate_id'] = $value->affiliate_id;
                $fields['affiliate_name'] = $value->affiliate_name;
                $fields['rank'] = $value->rank;

                AffiliateLeaderboard::create($fields);
            }
        }

    }
    // IN: A contest model. 
    public static function generateLeaderboard($contest)
    {
        if (!$contest) return;

        if ($contest->type == 'sales')
        {
            static::generateLeaderboardSales($contest);
        }
        else if ($contest->type == 'leads')
        {
            static::generateLeaderboardLeads($contest);
        }
    }

    private static function deleteLeaderboardForContest($contest)
    {
        $leaderboard = AffiliateLeaderboard::where('contest_id', $contest->id)->get();
        foreach ($leaderboard as $el)
        {
            $el->forceDelete();
        }
    }


    private static function needsUpdating($contest)
    {
        if (!$contest->start_date || !$contest->end_date) return FALSE;

        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endofDay();

        // Contest finish on some date/time prior to end of today. 
        if ($contest->end_date < $end) return FALSE;

        // Contest starts on some date/time further than today. 
        if ($contest->start_date > $start) return FALSE;

        return TRUE;

    }
}
