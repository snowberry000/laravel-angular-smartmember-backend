<?php

namespace App\Http\Middleware;
use App\Models\AccessLevel\Pass;
use App\Models\AccessLevel\Grant;
use App\Models\Company;
use App\Models\TeamRole;
use App\Models\Site\Role;

use Closure;
use Auth;

class SiteCreate
{
    /**
     * Create a new filter instance.
     *
     * @return void
     */
    public function __construct(){

    }

    public function handle($request, Closure $next)
    {
        $user = Auth::user();

		$access_levels = Pass::granted_by_levels(env("MEMBER_ACCESS_LEVEL_ID"));

        $role = Role::whereUserId($user->id)->whereIn('access_level_id', $access_levels )->first();

        if ($role){
            return $next($request);
        }
        
        /*
        $users_current_company_id = Company::getOrSetCurrentCompany();
        if( $users_current_company_id )
            $role = TeamRole::whereCompanyId( $users_current_company_id )->whereUserId( Auth::user()->id )->where('role','<',4)->first();

        if (!empty($role))
        {
            
        }
        */
        
        \App::abort(403, 'You must be a Smart Member customer to create new sites');
    }
}
