<?php

namespace App\Http\Middleware;

use App\Models\Site\Role;
use App\Models\TeamRole;
use App\Models\Company;

use Closure;
use Auth;
use App\Helpers\CompanyHelper;

class SiteAdmin
{
    /**
     * Create a new filter instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function handle( $request, Closure $next )
    {
        $site = \Domain::getSite();

		if( !Auth::user() )
			\App::abort( 403, "You must be logged in as an admin user to access that." );

        if( $site )
        {

            $role = Role::whereSiteId($site->id)->whereUserId(\Auth::id())->whereIn('type',['admin','owner'])->first();
            if( $role )
            {
                return $next( $request );
            }
        }

        $current_company_id = Company::getOrSetCurrentCompany();

        $role = TeamRole::whereCompanyId( $current_company_id )
            ->where('role','<',5)
            ->whereUserId( Auth::user()->id )
            ->first();

        if( $role )
        {
            return $next( $request );
        }

        \App::abort( 403, "You are you not an admin of this site." );
    }
}
