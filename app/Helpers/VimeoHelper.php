<?php

namespace App\Helpers;

use App\Models\VimeoAppConfiguration;
use Vimeo\Vimeo;

/*
   Reference to this code has been completed removed. Use VimeoController or Model/Vimeo for reference. 
*/

class VimeoHelper
{
    public static function GetVimeoRedirectUrl()
    {
        $url = \Config::get('app.url');
        return $url . "/vimeo/connect";
//        return Request::header();
//        $subdomain = self::ReplaceSubdomain($replace_string);
//        $subdomain = $subdomain."/vimeo-login.php";
//        return $subdomain;
    }


    public function GetUserVideos($token, $page = 1)
    {
        //TODO: Paging
        $lib = new Vimeo(\Config::get("integration.vimeo.client_id"), \Config::get("integration.vimeo.client_secret"));
        $lib->setToken($token);
        return $lib->request("/me/videos?page=" . $page . "&per_page=50", "", "GET");
    }

    public function HandleConnection()
    {
        $scope = 'public';
        $state = '';
        $redirect_url = self::GetVimeoRedirectUrl();

        $lib = new Vimeo(\Config::get("integration.vimeo.client_id"), \Config::get("integration.vimeo.client_secret"));

        if (isset($_GET['code'])) {
            $tokens = $lib->accessToken($_GET['code'], $redirect_url);

            dd($tokens);

            if ($tokens['status'] == 200) {
                $vimeo_access_token = $tokens['body']['access_token'];

                $url = \Session::get("redirect");
                dd($url);

                $vimeo_integration = new VimeoAppConfiguration();
                $vimeo_integration->site_id = DomainHelper::getPageIdByUrl($url);
                $vimeo_integration->access_token = $vimeo_access_token;
                $vimeo_integration->save();
                return redirect($url . "#/admin/vimeo-videos");
            }
        } else {
            \Session::put("redirect", DomainHelper::getOrigin(true));
            $url = $lib->buildAuthorizationEndpoint($redirect_url, $scope, $state);
            return redirect($url);
        }
    }

    public function GetVideosUploadedByUser( $token, $page = '' )
    {
        if( $page == '' )
        {
            $page = 1;
        }

        $response = $this->GetUserVideos( $token, $page );
        $tags = array();

        $real_data = array();
        $summary_data = array();
        $summary_data[ 'mode' ] = "loggedin";

        if( !isset($response[ 'error' ]) )
        {
            $data = $response[ 'body' ][ 'data' ];
//            $removed_videos = $this->Route( 'vimeo-video' )->GetPosts();
            $removed_videos_arr = array();

            if( !empty( $removed_videos ) && is_array( $removed_videos ) )
            {
                foreach( $removed_videos as $keyv => $valuev )
                {
                    $removed_videos_arr[ ] = $valuev[ 'video_id' ];
                }
            }

            $summary_data['tags'][ 'All videos' ] = 'vimeo-video-wrapper';

            if( $data )
            {
                foreach( $data as $key => $value )
                {
                    $video_id = str_replace( "/videos/", "", $value[ 'uri' ] );

                    if( in_array( $video_id, $removed_videos_arr ) )
                        continue;

                    $fields = array();
                    $fields[ 'title' ] = $value[ 'name' ];
                    $fields[ 'embed_content' ] = $value[ 'embed' ][ 'html' ];
                    $fields[ 'description' ] = $value[ 'description' ];
                    $fields[ 'featured_image' ] = $value[ 'pictures' ][ 'sizes' ][ 1 ][ 'link' ];
                    $fields[ 'video_id' ] = str_replace( "/videos/", "", $value[ 'uri' ] );
                    $fields[ 'video_length' ] = gmdate( "H:i:s", $value[ 'duration' ] );

                    if( count( $value[ 'tags' ] ) > 0 )
                    {
                        $tag_name = $value[ 'tags' ][ 0 ][ 'name' ];

                        $fields['tags'] = $tag_name;
                        $fields[ 'canon-tags' ] = strtolower( str_replace( " ", "-", $tag_name ) );

                        if( !in_array( $tag_name, array_keys( $summary_data['tags'] ) ) )
                        {
                            $summary_data['tags'][ $tag_name ] = strtolower( str_replace( " ", "-", $tag_name ) );
                        }
                    }

                    $real_data[ ] = $fields; //here we put all the video into an array
                }
            }

            $fields = array();
            $fields['videos'] = $real_data;
            $fields['summary_data'] = $summary_data;

            //$this->ItemDebug( $fields );exit;

            return $fields;
        }
    }
}