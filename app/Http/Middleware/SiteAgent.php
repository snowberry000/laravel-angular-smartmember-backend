<?php

namespace App\Http\Middleware;
use App\Models\Role;
use App\Models\TeamRole;
use App\Models\Company;

use Closure;
use Auth;

class SiteAgent
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
        $site = \Domain::getSite();

		if( !$site ){
			$error = array("message" => 'You do not appear to be on a Smart Member site, please try again.', "code" => 500);
			return response()->json($error)->setStatusCode(500);
		}

        $role = Role::with('type')
                      ->whereHas('type' , function($query){
                         $query->where('role_type', "<" , 6);
                      })
                      ->whereSiteId($site->id)
                      ->whereUserId(Auth::user()->id)
                      ->first();
        if ($role){
            return $next($request); 
        }

		$current_company_id = Company::getOrSetCurrentCompany();

		$role = TeamRole::whereCompanyId( $current_company_id )
			->where('role','<',6)
			->whereUserId( Auth::user()->id )
			->first();

		if( $role )
		{
			return $next( $request );
		}

        \App::abort(403,"You are you not an admin or agent of this site.");
    }
}
