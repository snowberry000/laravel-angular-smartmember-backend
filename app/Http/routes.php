<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/',function(){
 echo "Yes, I am working";		
});

Route::controller("utility","UtilityController");
Route::controller("stripe","AppConfiguration\StripeController");
Route::controller("wordpress","AppConfiguration\WordpressController");
Route::controller("vimeo","AppConfiguration\VimeoController");
Route::controller("aweber","AppConfiguration\AweberController");
Route::controller("getresponse","AppConfiguration\GetResponseController");
Route::controller("constantcontact","AppConfiguration\ConstantContactController");
Route::controller("facebook","AppConfiguration\FacebookController");
Route::get("/paypal/access_token","AppConfiguration\PaypalController@getAccessToken");
Route::controller("paypal","AppConfiguration\PaypalController");

include "Routes/smartmember.php";

