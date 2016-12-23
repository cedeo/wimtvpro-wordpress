<?php

/**
 * Written by Netsense s.r.l. 2016
 */

namespace WimTvPrivate;

require_once 'wimtv_api.php';
require_once 'wimtv_public_api.php';

class WimTvPrivate {

    private static $instance;

    function __construct() {
        
    }

    static function getApiWTPrivate() {
        return WimTvPrivate::$instance;
    }

    static function initApiWTPrivate() {

        WimTvPrivate::$instance = new WimTvPrivate();
    }

//    USER
    /**
     * 
     * @return type
     */
    function apiGetProfile() {
        $apiAccessor = getApi();

        $request = $apiAccessor->getRequest('api/user/me');



        $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);

        $arrayjsonst = json_decode($result, true);



        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {

            $request = $apiAccessor->getRequest('api/user/me');
            $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }


        return $result;
    }

    function apiGetPacketProfile() {
        $apiAccessor = getApi();

        $request = $apiAccessor->getRequest('api/user/me/overview');


        $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);

        $arrayjsonst = json_decode($result, true);



        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {

            $request = $apiAccessor->getRequest('api/user/me');
            $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $result;
    }

    function apiEditProfile($params) {


        $apiAccessor = getApi();

        $request = $apiAccessor->postRequest('api/user/me');

        $request->body(json_encode($params));


        $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);

        $arrayjsonst = json_decode($result, true);
        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $result;
    }

//    VIDEO - WIMBOX


    function apiUpload($parameters, $tags, $contentIdentifier) {

        $apiAccessor = getApi();

        $request = $apiAccessor->postRequest('api/box');
        $request->body($parameters);


        $request->attach(array('file' => $parameters['file']));
        $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, $contentIdentifier, true);

        $arrayjsonst = json_decode($result, true);

        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, $contentIdentifier, true);
            $arrayjsonst = json_decode($result, true);
        }
//     var_dump($result->code);
//        if ($result->code == 201) {
//            $params = array(
//                'title' => $arrayjsonst->title,
//                'description' => $arrayjsonst->description,
//            );
//            if (isset($arrayjsonst->thumbnailId)) {
//                $params['thumbnailId'] = $arrayjsonst->thumbnailId;
//            }
//            if (sizeof($tags) >= 1) {
//                $params['tags'] = $tags;
//            }
//             apiUpdateWimboxItem($arrayjsonst->boxId, $params);
//        }

        return $result;
    }

    function apiGetVideos($parameters) {

        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/search/box/contents');
        $request->body(json_encode($parameters));

        $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        $arrayjsonst = json_decode($result, true);
        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $result;
    }

    function apiGetWimboxItem($boxId) {

        $apiAccessor = getApi();
        $request = $apiAccessor->getRequest('api/box/' . $boxId);
//    $request->body($parameters);

        $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);

//    var_dump(json_decode($result),$result->code);exit;
        $arrayjsonst = json_decode($result, true);
        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $result;
    }

    function apiUpdateWimboxItem($boxId, $parameters) {

        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/box/' . $boxId);
        $request->body(json_encode($parameters));

        $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);


        $arrayjsonst = json_decode($result, true);
        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $result;
    }

    function apiDeleteWimboxItem($boxId) {

        $apiAccessor = getApi();
        $request = $apiAccessor->deleteRequest('api/box/' . $boxId);
//        $request->body(json_encode($parameters));

        $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);


        $arrayjsonst = json_decode($result, true);
        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $result;
    }

    function apiPlayWimboxItem($boxId) {

        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/box/' . $boxId . '/play');

        $request->body("{}");
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);

        $arrayjsonst = json_decode($response, true);
        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

//     PROGRESS
    function apiGetUploadProgress($contentIdentifier) {
        $apiAccessor = getApi();

        $request = $apiAccessor->getRequest('api/progressbar/' . $contentIdentifier);
        $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);

        $arrayjsonst = json_decode($result, true);

        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');

            $result = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $result;
    }

// WIMVOD
    function apiPublishOnShowtime($id, $parameters) {


        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/box/' . $id . '/vod');
        $request->body(json_encode($parameters));
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        $arrayjsonst = json_decode($response, true);

        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {

            $apiAccessor->getRequest('api/user/me');

            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

    
//PrivatePage get info video vod
    function apiGetDetailsShowtime($vodid) {
//api/public/search/vod/contents
        $apiAccessor = getApi();
        $request = $apiAccessor->getRequest('api/vod/' . $vodid);

//        $request->body(json_encode($params));
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);

        $arrayjsonst = json_decode($response, true);
        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

    //POST
    function apiUpdateShowtime($vodid, $params) {
//api/public/search/vod/contents
        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/vod/' . $vodid);

        $request->body(json_encode($params));
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);

        $arrayjsonst = json_decode($response, true);
        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }
//        var_dump($response, $response->code);
//        exit;
        return $response;
    }

    function apiGetInPrivatePage($params) {

        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/search/vod/contents');
//var_dump($params);exit;
        $request->body(json_encode($params));
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);

        $arrayjsonst = json_decode($response, true);
        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

    function apiDeleteFromShowtime($vodid) {
//    $apiAccessor = getApi();
//    $request = $apiAccessor->deleteRequest('videos/' . $id . '/showtime/' . $stid);
//    $request = $apiAccessor->authenticate($request);
//    return $apiAccessor->execute($request);
        $apiAccessor = getApi();
        $request = $apiAccessor->deleteRequest('api/vod/' . $vodid);

        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);

        $arrayjsonst = json_decode($response, true);
        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

    function apiPlayWimVodItem($vodid, $params) {
        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/vod/' . $vodid . '/play');
//          $request = $apiAccessor->getRequest('embed/?vod=3325a1df-1ae0-4956-8603-aa18900016b6');
        if (isset($params)) {
            $request->body(json_encode($params));
        } else {
            $request->body("{}");
        }

        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);

        $arrayjsonst = json_decode($response, true);
        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

    function apiPayToPlayWimVodItem($params, $vodid) {
// {
//  "embedded" : false,
//  "mobile" : false,
//  "returnUrl" : "http://www.wim.tv/live/event/play",
//  "cancelUrl" : "http://www.wim.tv/live/event/rejected"
//}

        $apiAccessor = getApi();

        $request = $apiAccessor->postRequest('api/vod/' . $vodid . '/pay');
        $request->body(json_encode($params));


        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);

        $arrayjsonst = json_decode($response, true);
        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

//    THUMBNAIL

    function apiUploadThumb($parameters) {


        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/thumbnail');

//  var_dump($parameters['thumbnail']);exit;
//    $request->sends(Mime::UPLOAD);
        $request->attach(array('thumbnail' => $parameters['thumbnail']));
//    $request = $apiAccessor->authenticate($request);
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        $arrayjsonst = json_decode($response, true);



        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

    function apiGetThumb($thumbId) {


        $apiAccessor = getApi();
        $request = $apiAccessor->getRequest('asset/thumbnail/' . $thumbId);


        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        $arrayjsonst = json_decode($response, true);


        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

//    PACKET
//      "embedded" : false,
//  "mobile" : false,
//  "returnUrl" : "http://www.wim.tv/license/accepted",
//  "cancelUrl" : "http://www.wim.tv/license/rejected"
    function apiUpgradePacket($licenseName, $params) {

        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/license/' . $licenseName . '/activate');
        $request->body(json_encode($params));
// $request->body('{}');
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        $arrayjsonst = json_decode($response, true);



        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

    function apiPayToUpgradePacket($licenseName, $params) {

        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/license/' . $licenseName . '/subscribe');
        $request->body(json_encode($params));
// $request->body('{}');
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        $arrayjsonst = json_decode($response, true);



        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

    function apiDowngradePacket() {

        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/license/downgrade');
//        $request->body(json_encode($params));
        $request->body('{}');
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        $arrayjsonst = json_decode($response, true);



        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

    function apiGetPacket() {

        $apiAccessor = getApi();
        //inserire path api
        $request = $apiAccessor->getRequest();


        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        $arrayjsonst = json_decode($response, true);


        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }


//     LIVE CHANNEL
//    Bad Request torna un message
//    201 in caso di creazione
    function apiCreateLiveChannel($params) {

        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/live/channel');
        $request->body(json_encode($params));
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        $arrayjsonst = json_decode($response, true);


        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

    function apiReadLiveChannel($channelid) {


        $apiAccessor = getApi();
        $request = $apiAccessor->getRequest('api/live/channel/' . $channelid);


        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        $arrayjsonst = json_decode($response, true);


        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

    function apiUpdateLiveChannel($channelid, $params) {
        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/live/channel/' . $channelid);
        $request->body(json_encode($params));
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        $arrayjsonst = json_decode($response, true);

        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

    function apiDeleteLiveChannel($channelid) {
        $apiAccessor = getApi();
        $request = $apiAccessor->deleteRequest('api/live/channel/' . $channelid);

        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        $arrayjsonst = json_decode($response, true);

        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }
//var_dump($arrayjsonst,$response->code);die;
        return $response;
    }

    function apiSearchLiveChannels($params, $timezone) {

        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/search/live/channels');
        $request->body(json_encode($params));
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true, $timezone);
        $arrayjsonst = json_decode($response, true);

        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true, $timezone);
        }

        return $response;
    }

    function apiPlayOnAirLiveEventInChannels($channelid, $params) {

        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/live/channel/' . $channelid . '/play');
        if (isset($params)) {
            $request->body(json_encode($params));
        } else {
            $request->body("{}");
        }
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        $arrayjsonst = json_decode($response, true);

        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

//    LIVE EVENT



    function apiCreateLiveEvent($channelId, $params, $timezone) {

        $apiAccessor = getApi();


        $request = $apiAccessor->postRequest('api/live/channel/' . $channelId . '/event');

        $request->body(json_encode($params));

        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true, $timezone);
        $arrayjsonst = json_decode($response, true);

        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true, $timezone);
        }

        return $response;
    }

    function apiGetLiveEvent($eventId) {
        $apiAccessor = getApi();
        $request = $apiAccessor->getRequest('api/live/event/' . $eventId);

        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        $arrayjsonst = json_decode($response, true);

        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

    function apiSearchLiveEvents($params, $timezone) {

        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/search/live/events');
        $request->body(json_encode($params));
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true, $timezone);
        $arrayjsonst = json_decode($response, true);

        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true, $timezone);
        }

        return $response;
    }

    function apiUpdateLiveEvent($eventId, $params, $timezone) {

        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/live/event/' . $eventId);
        $request->body(json_encode($params));
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true, $timezone);
        $arrayjsonst = json_decode($response, true);

        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true, $timezone);
        }

        return $response;
    }

    function apiDeleteLiveEvent($eventId) {
        $apiAccessor = getApi();
        $request = $apiAccessor->deleteRequest('api/live/event/' . $eventId);

        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        $arrayjsonst = json_decode($response, true);

        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

    function apiPlayLiveEvent($eventId) {


        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/live/event/' . $eventId . '/play');
        $request->body('{}');
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        $arrayjsonst = json_decode($response, true);

        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

    function apiPayForPlayLiveEvent($eventId, $params) {
        $apiAccessor = getApi();
        $request = $apiAccessor->postRequest('api/live/event/' . $eventId . '/pay');
        $request->body(json_encode($params));
        $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        $arrayjsonst = json_decode($response, true);

        $apiOAuth2 = getApiOAuth2();
        if ($apiOAuth2->resultGetAccessToken($arrayjsonst)) {
            $apiAccessor->getRequest('api/user/me');
            $response = $apiAccessor->executeRequest($request, null, $apiAccessor->language, null, true);
        }

        return $response;
    }

}
