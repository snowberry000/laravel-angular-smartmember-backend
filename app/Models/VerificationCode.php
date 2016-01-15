<?php

namespace App\Models;

use App\Models\User;
use Carbon\Carbon;

class VerificationCode extends Root
{
    protected $table = "verification_codes";

	public static function VerifyCode( $user_id, $code )
	{
		$status = false;

		$existing_code = VerificationCode::whereUserId( $user_id )->whereCode( $code )->where('expired_at','>', Carbon::now()->timestamp )->first();

		if( $existing_code )
			$status = true;

		return $status;
	}
}

VerificationCode::creating(function($model){
	$existing_codes = VerificationCode::whereUserId( $model->user_id )->get();

	foreach( $existing_codes as $existing_code )
		$existing_code->delete();

	$model->code = User::randomPassword(8);
	$model->expired_at = Carbon::now()->addMinutes(15);
});