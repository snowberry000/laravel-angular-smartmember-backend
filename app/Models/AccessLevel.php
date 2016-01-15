<?php namespace App\Models;

use App\Models\AccessLevel\Grant;
use App\Models\AccessLevel\Pass;
use App\Models\AccessLevel\SharedGrant;
use App\Models\AccessLevel\PaymentMethod;
use SMCache;
class AccessLevel extends Root
{
    protected $table = 'access_levels';
	protected $with = ['grants'];

    public function grants(){
    	return $this->hasMany("App\\Models\\AccessLevel\\Grant","access_level_id");
    }

    public function shared_grants(){
        return $this->hasMany("App\\Models\\AccessLevel\\SharedGrant","access_level_id");
    }

    public function paymentMethods(){
        return $this->hasMany("App\\Models\\AccessLevel\\PaymentMethod","access_level_id");
    }

    public function passes(){
        return $this->hasMany("App\\Models\\Site\\Role","access_level_id");
    }

    public function applySearchQuery($query , $value)
    {
        return $query->where('name','like','%' . $value . "%");
    }
    
    public static function create(array $data = array())
    {
        $data["hash"] = md5(microtime());
        $shared_grants = [];
        unset($data['shared_grants']);
    	if (isset($data["grants"]))
        {
            $grants = $data[ "grants" ];
            unset( $data[ "grants" ] );
        }
        if (isset($data["payment_methods"]))
        {
            $payment_methods = $data[ "payment_methods" ];
            unset( $data[ "payment_methods" ] );
        }

        $access = parent::create($data);

        if( !empty( $grants ) && is_array( $grants ) )
        {
            foreach( $grants as $key => $grant )
            {
                $site_grant = AccessLevel::whereId($grant)->whereSiteId($data['site_id'])->first();
                if (!isset($site_grant))
                {
                    $shared_grants[] = $site_grant->id;
                    unset($grants[$key]);
                } else {
                    $access->grants()->save( new Grant( [ 'grant_id' => $grant ] ) );
                }
            }
        }
        if( !empty( $shared_grants ) && is_array( $shared_grants ) )
        {
            foreach( $shared_grants as $grant )
                $access->$shared_grants()->save( new SharedGrant( [ 'grant_id' => $grant ] ) );
        }
        if( !empty( $payment_methods ) && is_array( $payment_methods ) )
        {
            foreach( $payment_methods as $payment_method )
                $access->paymentMethods()->save( new paymentMethod( [ 'payment_method_id' => $payment_method ] ) );
        }

        return $access;
    }

    public function update(array $data = array()){
        if (isset($data["grants"]))
        {
            $grants = $data[ "grants" ];
            unset( $data[ "grants" ] );
        }
        unset($data['shared_grants']);
        $shared_grants = [];
        if (isset($data["payment_methods"]))
        {
            $payment_methods = $data[ "payment_methods" ];
            unset( $data[ "payment_methods" ] );
        }
        if($this->grants)
            $this->grants()->delete();
        if($this->shared_grants)
            $this->shared_grants()->delete();
        if($this->paymentMethods)
            $this->paymentMethods()->delete();

        if( !empty( $grants ) && is_array( $grants ) )
        {
            foreach( $grants as $key => $grant )
            {
                if( isset( $grant[ 'id' ] ) )
                    $grant = $grant[ 'grant_id' ];
                $site_grant = AccessLevel::whereId($grant)->whereSiteId($data['site_id'])->first();
                if (!isset($site_grant))
                {
                    $shared_grants[] = $site_grant->id;
                    unset($grants[$key]);
                } else {
                    $this->grants()->save( new Grant( [ 'grant_id' => $grant ] ) );
                }

            }
        }
        if( !empty( $shared_grants ) && is_array( $shared_grants ) )
        {
            foreach( $shared_grants as $grant )
            {
                if( isset( $grant[ 'id' ] ) )
                    $grant = $grant[ 'grant_id' ];
                $this->shared_grants()->save( new SharedGrant( [ 'grant_id' => $grant ] ) );
            }
        }
        if( !empty( $payment_methods ) && is_array( $payment_methods ) )
        {
            foreach( $payment_methods as $payment_method )
            {
                if( isset( $payment_method[ 'id' ] ) )
                    $payment_method = $payment_method[ 'payment_method_id' ];
                $this->paymentMethods()->save( new paymentMethod( [ 'payment_method_id' => $payment_method ] ) );
            }
        }

        parent::update($data);

        return $this;
    }
}

AccessLevel::saving(function($access_level){
    \Log::info('I am in saving');

    $keys = array();
    $keys[] ='modules' . ':' . $access_level->site_id . ':*';
    $keys[] = 'facebook_group_id:'.$access_level->site_id . ':*';

    SMCache::clear($keys);

    $routes[] = 'site_details';
    $routes[] = 'module_home';
    SMCache::reset($routes);
});

AccessLevel::deleting(function($access_level){
    $access_level->passes()->delete();
    $access_level->grants()->delete();
    $access_level->paymentMethods()->delete();

    $keys = array();
    $keys[] ='modules' . ':' . $access_level->site_id . ':*';
    $keys[] = 'facebook_group_id:'.$access_level->site_id . ':*';

    SMCache::clear($keys);

    $routes[] = 'site_details';
    $routes[] = 'module_home';
    SMCache::reset($routes);
});

/*AccessLevel::creating(function($access_level){
    
    $keys = array();
    $keys[] ='modules' . ':' . $access_level->site_id . ':*';
    $keys[] = 'facebook_group_id:'.$access_level->site_id . ':*';

    SMCache::clear($keys);
});

AccessLevel::updating(function($access_level){
    
    $keys = array();
    $keys[] ='modules' . ':' . $access_level->site_id . ':*';
    $keys[] = 'facebook_group_id:'.$access_level->site_id . ':*';

    SMCache::clear($keys);
});*/