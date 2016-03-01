<?php namespace App\Helpers;

use App\Models\User;
use App\Models\Site\Role;
use App\Models\TeamRole;
use App\Models\UserRole;
use App\Models\Site;
use App\Models\DripFeed;
use App\Models\AccessLevel\Pass;
use App\Models\AccessLevel\Grant;
use App\Models\AccessLevel;
use App\Helpers\CompanyHelper;

use Carbon\Carbon;
use Auth;
use SMRole;

class SMAuthenticate
{

    public static function set()
    {
        $authorization = explode(" ",\Input::header("Authorization"));

        if($authorization && count($authorization) == 2){
            list($type,$access_token) = $authorization;
            switch($type){
                case "Basic":
                        $user = \App\Models\User::whereAccessToken($access_token)->first();
                        if($user){
                            \Auth::loginUsingId($user->id);
                            return true;
                        }
                        break;
            }
        }
        return false;
    }

    public static function getSiteRole($site_id){
        if (!Auth::user()){
            if(!self::set()){
                return []; // User is not logged in
            }
        }
        $roles = Role::whereUserId(Auth::user()->id)->whereSiteId($site_id)->get();
        $data = [];
        if ($roles){
            foreach ($roles as $role){
                $data[] = $role->type;
            }   
        }
        return $data;
    }

    public static function isMember($site_id){
        $logged_in = SMAuthenticate::set();

        if(!$logged_in){
            return false;
        }
        $role = Role::whereSiteId($site_id)->whereUserId(\Auth::user()->id)->first();

        if($role){
            return true;
        } 
        return false;
    }


    public static function checkIfUnowned($model)
    {
        $access_level = AccessLevel::find($model->access_level_id);
        if ($access_level && $access_level->hide_unowned_content) {
            if (self::checkAccessLevel($model)) {
                return true;
            } else {
                return false;
            }
        }
        return true;

    }

    public static function determineTimeLeft($model)
    {
        $logged_in = SMAuthenticate::set();
        $current_time_stamp = Carbon::now()->timestamp;
        if ($model->end_published_date != null)
        {
            if ($current_time_stamp >= strtotime($model->published_date) && $current_time_stamp <= strtotime($model->end_published_date))
            {
                return strtotime($model->end_published_date) - $current_time_stamp;
            }
        }

        if ($current_time_stamp < strtotime($model->published_date))
        {
            return strtotime($model->published_date) - $current_time_stamp;
        }

        if ($logged_in)
        {
            $dripfeed = DripFeed::whereSiteId($model->site_id)->whereTargetId($model->id)->whereType($model->getTable())->first();
            if ($dripfeed)
            {
                switch ( $dripfeed->interval )
                {
                    case 'hours':
                        $diff = $dripfeed->duration * 3600;
                        break;
                    case 'days':
                        $diff = $dripfeed->duration * 86400;
                        break;
                    case 'weeks':
                        $diff = $dripfeed->duration * 86400 * 7;
                        break;
                    case 'months':
                        $diff = $dripfeed->duration * 86400 * 30;
                        break;
                }

                switch ($model->access_level_type)
                {
                    case 2:

                        $passes = Role::whereSiteId($model->site_id)->whereUserId(\Auth::user()->id)->get();

                        foreach ($passes as $pass) {
                            if($pass && $pass->access_level_id == $model->access_level_id){
                                if ($current_time_stamp - strtotime($pass->created_at) < $diff)
                                    return $diff - ($current_time_stamp - strtotime($pass->created_at));
                            }
                        }

                        foreach ($passes as $pass) {
                            $grants = Grant::whereAccessLevelId($pass->access_level_id)->get();
                            foreach ($grants as $grant) {
                                if($grant->grant_id == $model->access_level_id) {
                                    if ($current_time_stamp - strtotime($grant->created_at) < $diff)
                                        return $diff - ($current_time_stamp - strtotime($grant->created_at));
                                }
                            }
                        }

                        break;

                    case 3:

                        $roles = Role::whereUserId(\Auth::user()->id)->whereSiteId($model->site->id)->first();

                        if ($roles) {
                            if ($current_time_stamp - strtotime($roles->created_at) < $diff)
                                return $diff - ($current_time_stamp - strtotime($roles->created_at));
                        }
                        break;
                }
            }
        }
        return 0;
    }

    public static function validateDate($date)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') == $date;
    }

    public static function checkScheduleAvailability($model)
    {
        $availability = true;
        $logged_in = SMAuthenticate::set();
        if (!$model->published_date || $model->published_date == null || $model->published_date == '0000-00-00 00:00:00' || !self::validateDate($model->published_date))
            return true;

        if ($logged_in)
        {
            if (SMRole::userHasAccess($model->site->id, 'manage_content', \Auth::user()->id))
                return true;
        }

        if ($model->end_published_date != null && $model->end_published_date != '0000-00-00 00:00:00')
        {
            if (!(Carbon::now()->timestamp >= strtotime($model->published_date) && Carbon::now()->timestamp <= strtotime($model->end_published_date)))
            {
                $availability = false;
            }
        } else {
            if (!(Carbon::now()->timestamp >= strtotime($model->published_date)))
            {
                $availability = false;
            }
        }
        return $availability;
    }

    public static function checkDripAvailability($model)
    {
        $logged_in = SMAuthenticate::set();
        $dripfeed = DripFeed::whereSiteId($model->site->id)->whereTargetId($model->id)->whereType($model->getTable())->first();

        if (!$dripfeed)
        {
            return true;
        }


        switch ( $dripfeed->interval )
        {
            case 'hours':
                $diff = $dripfeed->duration * 3600;
                break;
            case 'days':
                $diff = $dripfeed->duration * 86400;
                break;
            case 'weeks':
                $diff = $dripfeed->duration * 86400 * 7;
                break;
            case 'months':
                $diff = $dripfeed->duration * 86400 * 30;
                break;
        }

        switch ($model->access_level_type)
        {

            case 2:
                if (!$logged_in)
                    return false;

                if (SMRole::userHasAccess($model->site->id, 'manage_content', \Auth::user()->id))
                    return true;

                $passes = Role::whereSiteId($model->site->id)->whereUserId(\Auth::user()->id)->get();

                foreach ($passes as $pass) {
					$access_levels = Pass::access_levels( $pass->access_level_id );

                    if( $pass && in_array( $model->access_level_id, $access_levels ) ){
                        if (Carbon::now()->timestamp - strtotime($pass->created_at) >= $diff)
                            return true;
                    }
                }

                return false;
                break;


            case 3:

                if (!$logged_in)
                    return false;

                if (SMRole::userHasAccess($model->site->id, 'manage_content', \Auth::user()->id))
                    return true;

                $roles = Role::whereUserId(\Auth::user()->id)->whereSiteId($model->site->id)->first();

                if ($roles)
                {
                    \Log::info('checking roles for drip ' . $roles);
                    if (Carbon::now()->timestamp - strtotime($roles->created_at) >= $diff)
                        return true;
                    else
                        return false;
                } else {
                    \Log::info('No role found for drip');
                    return false;
                }
                break;
        }
    }

    public static function checkAccessLevel($model){
        $logged_in = SMAuthenticate::set();

        if($model->access_level_type <= 1)
            return SMAuthenticate::checkScheduleAvailability($model);

        // if(isset($model->show_content_publicly) && $model->show_content_publicly){
        //     return SMAuthenticate::checkDripAvailability($model) && SMAuthenticate::checkScheduleAvailability($model);
        // }

        if(!$logged_in && $model->access_level_type > 1){
            return false;
        }

        //if user is logged in and access level is member, grant access
        if($model->access_level_type == 3){
            return (SMAuthenticate::isMember($model->site_id) && SMAuthenticate::checkDripAvailability($model) && SMAuthenticate::checkScheduleAvailability($model));
        }
        $user_id = \Auth::user()->id;

        //if user does not have proper access rights
        if($model->access_level_type == 2)
		{
			if( \SMRole::hasAccess($model->site_id, 'view_private_content') || \SMRole::hasAccess($model->site_id, 'manage_members'))
				return true;

            $passes = Role::whereSiteId($model->site_id)->whereUserId($user_id);

            $passes = $passes->where(function($query){
                $query->whereNull('expired_at')
                ->orWhere('expired_at' , '>' , Carbon::now())
                ->orWhere('expired_at','=','0000-00-00 00:00:00');
            })->get();
            foreach ($passes as $pass) {
                if (!empty($pass->access_level_id))
                {
                    $access_levels = Pass::access_levels( $pass->access_level_id );
                    if($pass && in_array( $model->access_level_id, $access_levels ) && SMAuthenticate::checkDripAvailability($model) && SMAuthenticate::checkScheduleAvailability($model))
                        return true;
                }

            }

            return false;
        }
        else if($model->access_level_type==4){
			if( \SMRole::hasAccess($model->site_id, 'view_private_content') )
				return true;

            return false;
        }

        return true;
    }
}