<?php

/**
 * Written by walter at 30/10/13
 * Updated by Netsense s.r.l. 2014-2016
 */
include_once("api.php");
include_once("wimtv_api_oauth2.php");
include_once("wimtv_api_cmsspecific.php");
include_once("wimtv_public_api.php");
include_once("wimtv_private_api.php");
include_once("UUID.php");

use \Api\Api;
use \Httpful\Mime;
use \Httpful\Request;
use \OAuth2\OAuth2;
use \WimTvPublic\WimTvPublic;
use \WimTvPrivate\WimTvPrivate;

/* * *** API SETTINGS **** */
global $WIMTV_API_TEST, $WIMTV_API_PRODUCTION, $WIMTV_API_HOST;
/** RETRIEVE API URLs  FOR BOTH "TEST" and "PRODUCTION"* */
// PLEASE DO NOT CHANGE
$WIMTV_API_TEST = cms_getWimtvApiTestUrl();
$WIMTV_API_PRODUCTION = cms_getWimtvApiProductionUrl();

/** SET ACTIVE API URL: "TEST" OR "PRODUCTION"* */
//$WIMTV_API_HOST = $WIMTV_API_TEST;
$WIMTV_API_HOST = $WIMTV_API_PRODUCTION;
/* * ******* */

function initApi($host, $username, $password) {
// $host = "http://www.wim.tv/wimtv-webapp/rest/";
//    $host = "http://52.19.105.240:8080/wimtv-server/";
//    $host = "http://peer.wim.tv/";
//    $username = "wp";
//    $password = "f6fd7549-5d2a-43e0-85bd-add81613dcd2";
    Api::initApiAccessor($host, $username, $password);
}

function initApiOauth2($host, $username) {
    OAuth2::initApiOauth2Accessor($host, $username);
}

function initApiWTPublic() {
    WimTvPublic::initApiWTPublic();
}

function initApiWTPrivate() {
    WimTvPrivate::initApiWTPrivate();
}

function getApi() {
    return Api::getApiAccessor();
}

function getApiOAuth2() {
    return OAuth2::getApiOauth2Accessor();
}

function getApiOPublic() {
    return WimTvPublic::getApiWTPublic();
}

function getApiOPrivate() {
    return WimTvPrivate::getApiWTPrivate();
}

function apiRegistration($params) {


    $apiPublic = getApiOPublic();
    $result = $apiPublic->apiRegistration($params);

    return $result;
}

function apiGetProfile() {

    $apiPrivate = getApiOPrivate();
    return $result = $apiPrivate->apiGetProfile();

}

function apiGetPacketProfile() {

    $apiPrivate = getApiOPrivate();
    return $result = $apiPrivate->apiGetPacketProfile();
}

function apiGetVideos($parameters) {

    $apiPrivate = getApiOPrivate();
    return $result = $apiPrivate->apiGetVideos($parameters);
}

function apiEditProfile($params) {


    $apiPrivate = getApiOPrivate();
    return $result = $apiPrivate->apiEditProfile($params);
}

// WIMBOX
function apiUpload($parameters, $tags, $contentIdentifier) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiUpload($parameters, $tags, $contentIdentifier);

    return $result;
}

function apiGetUploadProgress($contentIdentifier) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiGetUploadProgress($contentIdentifier);

    return $result;
}

function apiGetWimboxItem($boxId) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiGetWimboxItem($boxId);

    return $result;
}

function apiUpdateWimboxItem($boxId, $parameters) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiUpdateWimboxItem($boxId, $parameters);

    return $result;
}

function apiDeleteVideo($boxId) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiDeleteWimboxItem($boxId);

    return $result;
}

function apiPlayWimboxItem($boxId) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiPlayWimboxItem($boxId);

    return $result;
}

//VOD
function apiPublishOnShowtime($id, $parameters) {



    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiPublishOnShowtime($id, $parameters);

    return $result;
}

function apiGetDetailsShowtime($vodid) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiGetDetailsShowtime($vodid);

    return $result;
}

function apiUpdateShowtime($vodid, $params) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiUpdateShowtime($vodid, $params);

    return $result;

}

function apiGetDetailsShowtimePublic($vodid) {

    $apiPrivate = getApiOPublic();
    $result = $apiPrivate->apiGetDetailsShowtimePublic($vodid);

    return $result;

}

function apiGetInPrivatePage($params) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiGetInPrivatePage($params);

    return $result;
}

function apiGetInPublicPage($params) {

    $apiPrivate = getApiOPublic();
    $result = $apiPrivate->apiGetInPublicPage($params);

    return $result;
}

function apiDeleteFromShowtime($vodid) {



    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiDeleteFromShowtime($vodid);

    return $result;
}

function apiPlayWimVodItem($vodid, $params = null) {



    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiPlayWimVodItem($vodid, $params);

    return $result;
}

function apiPlayWimVodItemPublic($vodid, $params = null) {
    $apiPublic = getApiOPublic();
    $result = $apiPublic->apiPlayWimVodItemPublic($vodid, $params);

    return $result;
}

function apiPreviewWimVodItem($vodid) {

    $apiPublic = getApiOPublic();
    $result = $apiPublic->apiPreviewWimVodItem($vodid);

    return $result;
}


function apiPayToPlayWimVodItem($params, $vodid) {
//    $apiAccessor = getApi();
//    $request = $apiAccessor->deleteRequest('videos/' . $id . '/showtime/' . $stid);
//    $request = $apiAccessor->authenticate($request);
//    return $apiAccessor->execute($request);


    $apiPublic = getApiOPublic();
    $result = $apiPublic->apiPayToPlayWimVodItemPublic($params, $vodid);

    return $result;
}

//THUMBNAIL
function apiUploadThumb($parameters) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiUploadThumb($parameters);

    return $result;

}

function apiGetThumb($thumbId) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiGetThumb($thumbId);

    return $result;
}




function apiUpgradePacket($licenseName, $params) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiUpgradePacket($licenseName, $params);

    return $result;
}

function apiDowngradePacket() {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiDowngradePacket();

    return $result;
}

function apiPayToUpgradePacket($licenseName, $params) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiPayToUpgradePacket($licenseName, $params);

    return $result;
}

function apiGetPacket() {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiGetPacket($thumbId);

    return $result;
}

function apiCommercialPacket() {


    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiCommercialPacket($thumbId);
    return $result;
}

//LIVE CHANNEL
/**
 * 
 * "name" : "Channel 1",
  "public" : true,
  "description" : "Description 1",
  "tags" : [ "tag1", "tag2" ],
  "thumbnailId" : "f85aa10a-abea-4002-a873-7634133589e4",
  "streamPath" : "channel1"
 * 
 */
function apiCreateLiveChannel($params) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiCreateLiveChannel($params);

    return $result;
}

/**
 * 
 * $channelId -> identificativo canale
 * 
 */
function apiReadLiveChannel($channelid) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiReadLiveChannel($channelid);

    return $result;
}

/**
 * $channelId = identificatico canale
 * "name" : "Channel 1",
  "public" : true,
  "description" : "Description modified",
  "tags" : [ "tag3" ],
  "streamPath" : "channel1"
 * 
 */
function apiUpdateLiveChannel($channelid, $params) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiUpdateLiveChannel($channelid, $params);

    return $result;
}

function apiDeleteLiveChannel($channelid) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiDeleteLiveChannel($channelid);

    return $result;
}

/**
 *  "queryString" : "technology",
  "pageSize" : 20,
  "pageIndex" : 0
 * 
 */
function apiSearchLiveChannels($params, $timezone) {


    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiSearchLiveChannels($params, $timezone);

    return $result;
}

function apiPlayOnAirLiveEventInChannels($channelid, $params=null) {

    $apiPublic = getApiOPublic();
    $result = $apiPublic->apiPlayOnAirLiveEventInChannelsPublic($channelid, $params);

    return $result;
}

function apiCreateStreamUrl($base) {


    $apiPublic = getApiOPublic();
    $result = $apiPublic->apiCreateStreamUrl($base);

    return $result;
}

/**
 *  channelId
 *  "name" : "Event 1",
  "description" : "Description 1",
  "tags" : [ "tag1", "tag2" ],
  "eventDate" : {
  "date" : "29/10/2016",
  "time" : "14:00:00"
  },
  "endDate" : {
  "date" : "29/10/2016",
  "time" : "14:30:00"
  },
  "publicEvent" : false,
  "recordEvent" : true,
  "thumbnailId" : "a4d88670-2dcc-46ac-960c-2758424bde25",
  "paymentMode" : "FREE"
 */
// L'API torna un errore, dice che manca streamPath'
function apiCreateLiveEvent($channelId, $params, $timezone) {


    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiCreateliveEvent($channelId, $params, $timezone);

    return $result;
}

function apiGetLiveEvent($eventId) {


    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiGetLiveEvent($eventId);

    return $result;
}

/**
 * Optional:
 *  queryString,
 * channelId
 * public
 * recorded
 * pastIncluded
 * conditions
 * 
 * 
 * Required:
  "pageSize" : 20,
  "pageIndex" : 0
 * 
 */
function apiSearchLiveEvents($params, $timezone) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiSearchLiveEvents($params, $timezone);

    return $result;
}

function apiSearchLiveEventsPublic($params, $timezone) {

    $apiPublic = getApiOPublic();
    $result = $apiPublic->apiSearchLiveEventsPublic($params, $timezone);

    return $result;
}

/**
  "name" : "Event 1",
  "description" : "Description modified",
  "tags" : [ "tag3" ],
  "eventDate" : {
  "date" : "29/10/2016",
  "time" : "14:30:00"
  },
  "endDate" : {
  "date" : "29/10/2016",
  "time" : "15:00:00"
  },
  "publicEvent" : true,
  "recordEvent" : false,
  "paymentMode" : "PAY_PER_VIEW",
  "pricePerView" : "2,50"
 * 
 */
function apiUpdateLiveEvent($eventId, $params, $timezone) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiUpdateLiveEvent($eventId, $params, $timezone);

    return $result;
}

function apiDeleteLiveEvent($eventId) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiDeleteLiveEvent($eventId);

    return $result;
}

function apiPlayLiveEvent($eventId) {

    $apiPrivate = getApiOPrivate();
    $result = $apiPrivate->apiPlayLiveEvent($eventId);

    return $result;
}

/**
  "embedded" : false,
  "mobile" : false,
  "returnUrl" : "http://www.wim.tv/live/event/play",
  "cancelUrl" : "http://www.wim.tv/live/event/rejected"
 * 
 */
//da provare
function apiPayForPlayLiveEventPublic($eventId, $params) {

    $apiPublic = getApiOPublic();
    $result = $apiPublic->apiPayForPlayLiveEventPublic($eventId, $params);

    return $result;
}


function isConnectedToTestServer() {
    global $WIMTV_API_HOST, $WIMTV_API_TEST, $WIMTV_API_PRODUCTION;
//    print "<li>Configured host: $WIMTV_API_HOST";
//    print "<li>Test host: $WIMTV_API_TEST";
//    print "<li>Production host: $WIMTV_API_PRODUCTION";
    return ($WIMTV_API_HOST === $WIMTV_API_TEST);
}

initApi(cms_getWimtvApiUrl(), cms_getWimtvUser(), cms_getWimtvPwd());
initApiOauth2(cms_getWimtvClientId(), cms_getWimtvSecretKey());
initApiWTPublic();
initApiWTPrivate();
//initApi($WIMTV_API_HOST, variable_get("userWimtv"), variable_get("passWimtv"));