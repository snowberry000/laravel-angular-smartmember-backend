<?php namespace App\Models;

use SMCache;
class Module extends Root
{
    protected $table = 'modules';

    public function lessons(){
        return $this->hasMany('App\Models\Lesson', 'module_id')->orderBy("sort_order");
    }

    public static function create(array $data = array())
    {

        $module = parent::create($data);
        $module->save();

        return $module;
    }

    public function update(array $data = array()){

        $this->fill($data);
        $this->save();

        return $this;
    }

    public function applySearchQuery($query, $value)
    {
        return $query->where('title', 'like', '%' . $value . '%');
    }
}

/*Module::creating(function($module){
    
    $module_key ='modules' . ':' . $module->site_id . ':*';
    $keys = PRedis::keys($module_key);
    foreach ($keys as $key)
    {
        \Log::info("Deleting " . $key);
        PRedis::del($key);
    }
});*/

Module::deleting(function($module){
    $keys = array();
    $keys[] = 'modules' . ':' . $module->site_id . ':*';
    
    SMCache::clear($keys);

    $routes[] = 'module_home';
    SMCache::reset($routes);
});

/*Module::updating(function($module){
    
    $module_key ='modules' . ':' . $module->site_id . ':*';
    $keys = PRedis::keys($module_key);
    foreach ($keys as $key)
    {
        \Log::info("Deleting " . $key);
        PRedis::del($key);
    }
});*/

Module::saving(function($module){
    \Log::info('I am in saving');

    $keys = array();
    $keys[] ='modules' . ':' . $module->site_id . ':*';

    SMCache::clear($keys);

    $routes[] = 'module_home';
    SMCache::reset($routes);
});
