<?php

/**
 * Written by Netsense s.r.l. 2016
 */

namespace WimTvPublic;

require_once 'wimtv_api.php';

class WimTvPublic {

    private static $instance;

    function __construct() {
        
    }

    static function getApiWTPublic() {
        return WimTvPublic::$instance;
    }

    static function initApiWTPublic() {

        WimTvPublic::$instance = new WimTvPublic();
    }

    function apiRegistration($params) {

        $public_token = $this->getToken(array(), "client_credentials");

        $apiAccessor = getApi();

        $request = $apiAccessor->postRequest('api/public/user/register?access_token=' . get_option('wp_public_access_token'));

        $request->body(json_encode($params));

        $request->authenticateWithBasic(get_option('wp_client_id'), get_option('wp_secret_key'));

        $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language);

        return $result;
    }

    function getToken($parameters = array(), $grant_type) {
        $apiAccessor = getApiOAuth2();

        return $apiAccessor->getAccessToken(__("API_URL", "wimtvpro") . "oauth/token", $grant_type, $parameters);
    }



    function apiPreviewWimVodItem($vodid) {
        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/public/vod/' . $vodid . '/preview?access_token='. get_option('wp_public_access_token'));

        $request->body("{}");
        $request->authenticateWithBasic(get_option('wp_client_id'),get_option('wp_secret_key'));
        $response = $apiAccessor->executeRequest($request, null, null, null, true);

        $arrayjsonst = json_decode($response, true);


//        $apiOAuth2 = getApiOAuth2();
//        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
//            $apiAccessor->getRequest('api/user/me');
//            $apiAccessor->executeRequest($request);
//        }

        return $response;
    }

    function apiPlayWimVodItemPublic($vodid, $params) {
        $apiAccessor = getApi();
        $public_token = $this->getToken(array(), "client_credentials");
        $request = $apiAccessor->postRequest('api/public/vod/' . $vodid . '/play?access_token=' . get_option('wp_public_access_token'));

        if (isset($params)) {
            $request->body(json_encode($params));
        } else {
            $request->body("{}");
        }

//        $request->body("{}");
        $request->authenticateWithBasic(get_option('wp_client_id'), get_option('wp_secret_key'));
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);


        return $response;
    }

    function apiPayToPlayWimVodItemPublic($params, $vodid) {
// {
//  "embedded" : false,
//  "mobile" : false,
//  "returnUrl" : "http://www.wim.tv/live/event/play",
//  "cancelUrl" : "http://www.wim.tv/live/event/rejected"
//}
        

        $public_token = $this->getToken(array(), "client_credentials");
        $apiAccessor = getApi();

        $request = $apiAccessor->postRequest('api/public/vod/' . $vodid . '/pay?access_token=' . get_option('wp_public_access_token'));
        $request->body(json_encode($params));


        $request->authenticateWithBasic(get_option('wp_client_id'), get_option('wp_secret_key'));
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);

        return $response;
    }

    function apiPayForPlayLiveEventPublic($eventId, $params) {

        $public_token = $this->getToken(array(), "client_credentials");
        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/public/live/event/' . $eventId . '/pay?access_token=' . get_option('wp_public_access_token'));
        $request->body(json_encode($params));

        $request->authenticateWithBasic(get_option('wp_client_id'), get_option('wp_secret_key'));
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);


        return $response;
    }

    function apiGetDetailsShowtimePublic($vodid) {
        $public_token = $this->getToken(array(), "client_credentials");
        $apiAccessor = getApi();
        $request = $apiAccessor->getRequest('api/public/vod/' . $vodid . '?access_token=' . get_option('wp_public_access_token'));
        $request->authenticateWithBasic(get_option('wp_client_id'), get_option('wp_secret_key'));
//        $request->body(json_encode($params));
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);



        return $response;
    }

    function apiPlayOnAirLiveEventInChannelsPublic($channelid, $params) {

        $public_token = $this->getToken(array(), "client_credentials");
  
        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/public/live/channel/' . $channelid . '/play?access_token=' . get_option('wp_public_access_token'));
       
        if (isset($params)) {
   
            $request->body(json_encode($params));
        } else {
            $request->body("{}");
        }


        $request->authenticateWithBasic(get_option('wp_client_id'), get_option('wp_secret_key'));

        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);



        return $response;
    }

    function apiSearchLiveEventsPublic($params, $timezone) {
        $public_token = $this->getToken(array(), "client_credentials");
        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/public/search/live/events?access_token=' . get_option('wp_public_access_token'));
        $request->body(json_encode($params));
        $request->authenticateWithBasic(get_option('wp_client_id'), get_option('wp_secret_key'));
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true, $timezone);


        return $response;
    }

    function apiCreateStreamUrl($params) {

        $this->getToken(array(), "client_credentials");
        $apiAccessor = getApi();
        $request = $apiAccessor->getRequest('api/public/live/streampath?base=' . $params['base'] . '&access_token=' . get_option('wp_public_access_token'));
//        $request->body(json_encode($params));

        $request->authenticateWithBasic(get_option('wp_client_id'), get_option('wp_secret_key'));
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);

        return $response;
    }



}
