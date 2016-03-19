<?php namespace App\Models;

class DripFeed extends Root
{
    protected $table = 'dripfeed';

    public static function set($model, array $data = array())
    {
        $dripfeed = self::whereSiteId($model->site_id)->whereTargetId($model->id)->whereType($model->getTable())->first();
        if ($dripfeed) {
            $dripfeed->duration = $data['duration'];
            $dripfeed->interval = $data['interval'];
            $dripfeed->save();
            return $dripfeed;
        }
        if (isset($data['interval']) && isset($data['duration']))
        {
            $new_dripfeed = self::create(array(
                "duration" => $data['duration'],
                "interval" => $data['interval'],
                "site_id" => $model->site_id,
                "target_id" => $model->id,
                "type" => $model->getTable(),
            ));
            return $new_dripfeed;
        }

    }

	public function getDurationAttribute( $value )
	{
		if( $value )
			return intval( $value );
		else
			return 0;
	}

	public static function remove($model)
	{
		$dripfeed = self::whereSiteId($model->site_id)->whereTargetId($model->id)->whereType($model->getTable())->first();

		if( $dripfeed )
			$dripfeed->delete();
	}
}
