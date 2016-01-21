<?php

namespace App\Http\Controllers;

use Input;
use App\Models\Permalink;
use App\Models\Media;
use Log;
use Domain;

class UtilityController extends Controller
{
    public function anyUpload(){

        $user = false;

        if (\Input::has('access_token')){
            $user = \App\Models\User::whereAccessToken(\Input::get('access_token'))->first();
        }
        

    	if (\Request::hasFile('file')){
            $site = Domain::getSite();
    	    $file = \Request::file('file');
            $private = Input::get('private');
            $private = empty( $private ) ? false : true;

            $originalName = \Request::file('file')->getClientOriginalName();
    	    $newFile =  'uploads/' . md5(time() + rand(0,100000)) . '/' . $originalName;

            $s3 = \Storage::disk('s3');

            $s3->getAdapter()->getClient()->putObject(
                array(
                    'Bucket' => \Config::get('filesystems.disks.s3.bucket'),
                    'Key' => $newFile,
                    'Body' => file_get_contents( \Request::file('file') ),
                    'ACL' => $private ? 'private' : 'public-read',
                    'ContentType' => \Request::file('file')->getMimeType(),
                    'Expires'       => 'Thu, 01 Dec 2030 16:00:00 GMT',
                    'CacheControl'  => 'max-age'
                )
            );

            $return = array();
            $return['file_name'] = $s3->getAdapter()->getClient()->getObjectUrl( \Config::get('filesystems.disks.s3.bucket'), $newFile );
            $return['link'] = $return['file_name'];
            if( $private )
                $return['aws_key'] = $newFile;

            //Store the resource in the media file
            if(!empty($site->id))
                Media::create([
                        "site_id" => $site->id,
                        "type" => "image",
                        "user_id" => $user ? $user->id : 0,
                        "source" => $return['link']
                    ]);

    	    return $return;
    	}
        else {
            //$max_upload = ini_get('upload_max_filesize') . ' ' . ini_get('post_max_size') . ini_get('memory_limit');
            \App::abort(403, "Something went wrong with the file upload.  Note that only files that are less than 50 MB are currently accepted. ");
        }
    }

    public function anyDownload(){
        $file_name = Input::get("file");
        $aws_key = Input::get("aws_key");

        if( strpos( $file_name, 's3.amazonaws.com/imbmediab/' ) !== false )
        {
            $file_name_bits = explode( 's3.amazonaws.com/imbmediab/', $file_name );

            $aws_key = $file_name_bits[1];
            unset( $file_name );
        }
        elseif( strpos( $file_name, 'imbmediab.s3.amazonaws.com/' ) !== false )
        {
            $file_name_bits = explode( 'imbmediab.s3.amazonaws.com/', $file_name );

            $aws_key = $file_name_bits[1];
            unset( $file_name );
        }

        if( !empty( $file_name ) ) {
            $file = file_get_contents( $file_name );
        }
        elseif( $aws_key ) {
            $s3 = \Storage::disk('s3');

            $command = $s3->getAdapter()->getClient()->getCommand('GetObject', [
                'Bucket'                     => \Config::get('filesystems.disks.s3.bucket'),
                'Key'                        => $aws_key,
                'ResponseContentDisposition' => 'attachment;'
            ]);

            $request = $s3->getAdapter()->getClient()->createPresignedRequest($command, '+10 minutes');
            $file = (string)$request->getUri();

            header( 'Location: ' . $file );
            exit;
        }

        if( !empty( $file ) ) {
            $quoted = sprintf('"%s"', addcslashes(basename( $file_name ), '"\\'));
            $size   = $this->RemoteFilesize( $file_name );

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $quoted);
            header('Content-Transfer-Encoding: binary');
            header('Connection: Keep-Alive');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . $size);

            echo $file;
            exit;
        }
    }

    function RemoteFilesize($url)
    {
        static $regex = '/^Content-Length: *+\K\d++$/im';
        if (!$fp = @fopen($url, 'rb')) {
            return false;
        }
        if (
            isset($http_response_header) &&
            preg_match($regex, implode("\n", $http_response_header), $matches)
        ) {
            return (int)$matches[0];
        }
        return strlen(stream_get_contents($fp));
    }

    public function getMeta(){
        $subdomain = Input::get("subdomain");
        $domain = Input::get("domain");
        if( $domain && strpos( $domain, '.smartmember.' ) === false )
			$site = \App\Models\Site::whereDomain( $domain )->first();

		if( empty( $site ) && $subdomain )
			$site = \App\Models\Site::whereSubdomain( $subdomain )->first();

		if( empty( $site ) )
			return array('success'=>'failure');

        $url = explode("/", Input::get('url'));
        $permalink = array_shift($url);
        $permalink = array_shift($url);

		if( !empty( $permalink ) )
        	$permalink = Permalink::whereSiteId($site->id)->wherePermalink($permalink)->first();

        if ($permalink){
            $type = $permalink->type;
            switch($type){
                case "lessons":
                    $meta = \App\Models\Lesson::getMeta($permalink->permalink,2,$site);
                    break;
                case "custom_pages":
                    $meta = \App\Models\CustomPage::getMeta($permalink->permalink,1,$site);
                    break;
                case "download_center":
                    $meta = \App\Models\Download::getMeta($permalink->permalink,3,$site);
                    break;
                case "livecasts":
                    $meta = \App\Models\Livecast::getMeta($permalink->permalink,5,$site);
                    break;
                case "bridge_bpages":
                    $meta = \App\Models\BridgePage::getMeta($permalink->permalink,6,$site);
                    break;
                case "blog":
                case "posts":
                    $meta = \App\Models\Post::getMeta($permalink->permalink,4,$site);
                    if( empty( $meta['description'] ) )
                    {
                        $post_controller = new \App\Http\Controllers\Api\PostController;
                        $post = $post_controller->getByPermalink( $permalink->permalink );
                        $meta['description'] = strip_tags( str_replace( array( '<p>','</p>' ),' ', $post_controller->truncateParagraph( strip_tags( $post->content, '<p>' ) ) ) );
                    }
                    break;
                default:
                    $meta = \App\Models\Site::getShareData($site->subdomain);
            }
        } else {
            $meta = \App\Models\Site::getShareData($site->subdomain);
            //dd($meta);
        }
        return $meta;
    }

}
