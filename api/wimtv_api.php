<?php
/**
 * Written by walter at 30/10/13
 */

include_once("api.php");

use Api\Api;
use \Httpful\Mime;
use \Httpful\Request;

function initApi($host, $username, $password) {
    Api::initApiAccessor($host, $username, $password);
}

function getApi() {
    return Api::getApiAccessor();
}

function apiCreateUrl($name) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('liveStream/uri?name=' . $name);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetProfile() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('profile');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiEditProfile($params) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('profile');
    $request->sends(Mime::JSON);
    $request->body($params);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'application/json');
}

function apiChangePassword($password) {
    $apiAccessor = getApi();
    $request = $apiAccessor->putRequest("users/" . $apiAccessor->username . "/updateLivePwd");
    $params = array('liveStreamPwd' => $password);
    $request->body($params);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetShowtimes() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('users/' . $apiAccessor->username . '/showtime?details=true');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetLiveEvents($timezone, $activeOnly) {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl . '?timezone=' . $timezone;
    if ($activeOnly) {
        $url .= '&active=true';
    }
    $request = $apiAccessor->getRequest($url);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'application/json');
}

function apiGetLive($host_id, $timezone="") {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl . '/' . $host_id;
    if (strlen($timezone))
        $url .= '?timezone=' . $timezone;
    $request = $apiAccessor->getRequest($url);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'application/json');
}

function apiGetLiveIframe($host_id, $timezone="") {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl . '/' . $host_id . '/embed';
    if (strlen($timezone))
        $url .= '?timezone=' . $timezone;
    $request = $apiAccessor->getRequest($url);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'text/xml, application/xml');
}

function apiAddLive($parameters) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest($apiAccessor->liveHostsUrl);
    $request->body($parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiModifyLive($host_id, $parameters) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest($apiAccessor->liveHostsUrl . '/' . $host_id);
    $request->body($parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiPublishOnShowtime($id, $parameters) {
    d('ho chiamato api!');
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('videos/' . $id . '/showtime');
    $request->body($parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiDeleteLive($host_id) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest($apiAccessor->liveHostsUrl . '/' . $host_id);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetVideoCategories() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videoCategories');
    return $apiAccessor->execute($request);
}

function apiGetUUID() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('uuid');
    return $apiAccessor->execute($request);
}

function apiUpload($parameters) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('videos');
    $request->body($parameters);
    $request->sends(Mime::UPLOAD);
    $request->attach(array('file' => $parameters['file']));
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiDownload($hostId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videos/' . $hostId . '/download');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, "");
}

function apiGetVideos($details='true') {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videos?details=' . $details );
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, "application/json");
}

function apiDeleteVideo($hostId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest('videos/' . $hostId);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiDeleteFromShowtime($id, $stid ) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest('videos/' . $id . '/showtime/' . $stid);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiRegistration($params) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('register');
    $request->sendsJson();
    $request->body(json_encode($params));
    return $apiAccessor->execute($request);
}

function apiCheckPayment($cookie) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('userpacket/payment/check');
    $request->setCookieFile($cookie);
    return $apiAccessor->execute($request);
}

?>