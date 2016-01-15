<?php

namespace App\Models;

use App\Models\AccessLevel\Pass;
use App\Models\Role;
use App\Models\EmailSubscriber;

class SegmentTool
{
	private $admin;
	private $query;
	private $sites;
	private $whereClause;

	public function __construct($query, $sites = null, $limit = 100)
	{
		$this->query = $query;
		$this->sites = $sites;
		$this->limit = $limit;

		$this->max_user_id = 5000;
		$this->paginate = FALSE;
		$this->isAdmin = FALSE;
		$this->model = null;
	}

	public function setPaginationEnabled($enabled)
	{
		$this->paginate = $enabled;
	}

	public function setAdmin($isAdmin)
	{
		$this->isAdmin = $isAdmin;
	}

	public function getModel()
	{
		if ($this->model != null) return $this->model;

		return new User;
	}

	public function validate()
	{
		$result = $this->build($this->query);
		if (array_key_exists('status', $result) && $result['status'] == 'OK')
			return true;

		return false;
	}

	public function getUsersCount()
	{
		return count($this->getUsers(1));
	}

	public function getUsers($last_index = 0)
	{
		$result = $this->build($this->query);
		if( array_key_exists( 'status', $result ) && $result[ 'status' ] != 'OK' )
			return $result;

			// Idenitfy site filter
		$site_filter = FALSE;
		
		foreach ($result['params'] as $param)
		{
			if ($param['name'] == 'site')
				$site_filter = $param;
		}

		$sites = [];
		if ($site_filter)
		{
			// filter sites 
			$sites = Site::whereIN('subdomain', $site_filter['value'])->whereIn('id', $this->sites)->select('id')->lists('id');
		}
		else
		{
			$sites = $this->sites;
		}		

		$this->max_user_id = User::orderBy('id', 'DESC')->select('id')->first()->id;

		// Since paging is disable, we will compute and send back all results,
		// all at once. 
		if (!$this->paginate)
			return $this->execute($result['params'], $sites, 1)
						->select('email')
						->distinct('email')
						->get()
						->toArray();
		$count = 0;
		$users = [];
		do 
		{	
			$model = $this->execute($result['params'], $sites, $last_index + 1);
			$resultset = $model->select('email')->get()->toArray();

			if ($last_index > $this->max_user_id && empty($resultset))
				break;

			if (!empty($resultset))
				$users = array_merge($users, $resultset);

			$count = $count + count($resultset);
			\Log::info($count);
			$last_index = $last_index + $this->limit;

		} while ($count < $this->limit);

		return ['users' => $users, 'last_index' => $last_index];
	}

	private function getAttributes() 
	{
		$attributes = [];

		$attributes['access_pass'] = [
						'model' => function($model, $sites, $params) {
										$access_levels = AccessLevel::whereIn('name', $params)->whereIn('site_id', $sites)->select('id')->lists('id');
										return $model->whereHas('access_passess', function($q) use ($access_levels) {
											$q->whereIn('access_level_id', $access_levels);
										});

									}
							];

        $attributes['email_list_id'] = [
           				'model' => function($lists) {
            							return EmailSubscriber::whereHas('emailLists', function($q) use ($lists) {
											$q->whereIn('email_lists.id', $lists);
										});
            						}
        			];

		$attributes['refund_pass'] = 					 [
						'model' => function($model, $sites, $params) {
										$access_levels = AccessLevel::whereIn('name', $params)->whereIn('site_id', $sites)->where('product_id', '!=', 0)->select('product_id')->lists('product_id');
										return $model->whereHas('refunds', function($q) use ($access_levels) {
											$q->whereIn('product_id', $access_levels);
										});
						},
					];

		$attributes['site'] = 					 [
						'model' => function($model, $sites, $params) {
										return $model->whereHas('role_type', function($q) use ($sites) {
												$q->where('user_roles.role_type', 6);
												$q->whereIn('site_id', $sites);
											});
									},
					];

		$attributes['role'] = 					 [
						'model' => function($model, $sites, $params) {
										return $model->whereHas('role_type', function($q) use ($params, $sites) {
												$q->whereIn('user_roles.role_type', $params);
												($this->isAdmin) ? '' : $q->whereIn('site_id', $sites);
											});
									},
						'value_map' => [
                                'Primary Owner' => 1,
								'Owner' => 2,
								'Manager' => 3,
								'Admin' => 4,
								'Agent' => 5,
                                'Member' => 6,
							]
					];

		return $attributes;

	}

	private function getOperators()
	{
		return ['=', '!=', 'IN'];
	}

	private function getConnectors()
	{
		return ['and', 'or'];
	}

	private function build($query)
	{

		$attributes = $this->getAttributes();
		$operators = $this->getOperators();
		$connectors = $this->getConnectors();

		$params = [];

		$matches = [];
		preg_match_all('/(?:[^\s"]+|"[^"]*")+/', $query, $matches);

		if (!$matches || count($matches) < 1 )				
			return $this->generateError("Invalid segment query");

		$matches = $matches[0];

		$i = 0;
		$attribute = null;
		$param = [];
		foreach ($matches as $token)
		{
			switch ($i) {
				case 0:
					$token = strtolower($token);
					if (array_key_exists($token, $attributes)) 
					{
						$param['name'] = $token;
						$param['model'] = $attributes[$token]['model'];
						$attribute = $token;
					}
					else 
					{
						return $this->generateError("Invalid attribute token "  . $token);
					}
					break;
				case 1:
					if (!in_array($token, $operators))
					{
						return $this->generateError("Invalid operator " . $token);
					}
					$param['operator'] = $token;
					break;
				case 2:
                    if( $param['operator'] == "IN" )
                    {
                        $token = str_replace( array('(',')'), '', $token );
                        $token = str_getcsv( $token );
                        foreach( $token as $key=>$val )
                            $token[ $key ] = str_replace('"', "", $val );
                    }
                    else
					    $token = str_replace('"', "", $token);

					if (array_key_exists('value_map', $attributes[$attribute]))
                    {
                        if( $param[ 'operator' ] == "IN" )
                        {
                            foreach( $token as $key=>$val )
                            {
                                if (array_key_exists($val, $attributes[$attribute]['value_map']))
                                    $token[ $key ] = $attributes[ $attribute ][ 'value_map' ][ $val ];
                            }
                        }
                        else
                        {
                            if (array_key_exists($token, $attributes[$attribute]['value_map']))
                                $token = $attributes[ $attribute ][ 'value_map' ][ $token ];
                        }

                        $param[ 'value' ] = $token;
					}
					else
						$param['value'] = $token;
					break;
				case 3:
					$token = strtolower($token);
					if (!in_array($token, $connectors))
					{
						return $this->generateError("Invalid connector " . $token);
					}
					$param['connector'] = $token;
					break;
				default:
						return $this->generateError("Invalid query ");	
					break;
			}
			$i = ($i + 1) % 4;

			// Reset vars for next expresion. 
			if ($i == 0) 
			{
				$params[] = $param;
				$param = [];
				$attribute = null;
			}
		}

		// End params
		if ($i != 0)
			$params[] = $param;

		return ['status' => 'OK', 'params' => $params];
	}

	private function execute($params, $sites, $last_index= 1)
	{
		$users = [];
		$theConnector = FALSE;
		$model = $this->getModel();

		if ($this->paginate)
			$model = $model->whereBetween('id', [$last_index, $last_index + $this->limit]);
		
		foreach ($params as $param)
		{
			if ($param['name'] == 'email_list_id')
			{

				if ($theConnector == "and")
				{
					$m = call_user_func($param['model'], $param['value']);
					$model = $m->whereIn('email', $model->lists('email'));
				}
				else if ($theConnector == "or" && 
					(!$this->paginate || $last_index >= $this->max_user_id))
				{

					$m = call_user_func($param['model'], $param['value']);
					$m = $m->select('email')->distinct('email');
					if ($this->paginate)
					{
						$skip = ($last_index - $this->max_user_id) / $this->limit;
						$m = $m->skip($skip)->limit($this->limit);
					}
					
					$model = $model->union($m->getQuery());
				}
				else if ($theConnector == FALSE)
				{
					$m = call_user_func($param['model'], $param['value']);
					$model = $m->select('email');
					if ($this->paginate)
					{
						$model = $model->skip($last_index - 1)->limit($this->limit);
					}
				}
			}
			else
			{
				$model = call_user_func($param['model'], 
									 $model, 
									 $sites, 
									 $param['value']
									);	
			}
		
			if (array_key_exists("connector", $param))
			{
				$theConnector = $param['connector']	;
			}
		}
		
		return $model;
	}

	public static function hasUser($email, $query, $sites)
	{
		if ($sites == null || !is_array($sites) || empty($sites)) return;

		$segment = SegmentTool($query, $sites);
		$segment->setPaginationEnabled(false);
		$model = User::where('email', $email);
		$segment->setModel($model);

		return count($segment);
	}

	private function generateError($message)
	{
		return array('status' => 'error', 'message' => $message);
	}
}