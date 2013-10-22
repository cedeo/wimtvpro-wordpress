<?php

include_once('httpful.phar');

use Httpful\Request;
use Httpful\Mime;

/**
 * Written by walter at 16/10/13
 */


class Api {
    private $host = null;
    public $username = null;
    public $liveHostsUrl;
    private $password = null;
    private static $instance;

    function __construct($host, $username, $password) {
        $this->host = $host;
        $this->username = $username;
        $this->liveHostsUrl = 'liveStream/' . $this->username . '/' . $this->username . '/hosts';
        $this->password = $password;
    }

    static function getApiAccessor() {
        return Api::$instance;
    }

    static function initApiAccessor($host, $username, $password) {
        Api::$instance = new Api($host, $username, $password);
    }

    function compileUrl($subUrl) {
        $url = $this->host . $subUrl;
        //trigger_error("Calling " . $url, E_USER_NOTICE);
        return $url;
    }

    function getRequest($subUrl) {
        $url = $this->compileUrl($subUrl);
        //trigger_error("Using method GET", E_USER_NOTICE);
        return Request::get($url);
    }

    function postRequest($subUrl) {
        $url = $this->compileUrl($subUrl);
        //trigger_error("Using method POST", E_USER_NOTICE);
        $request = Request::post($url);
        $request->sendsType(Mime::FORM);
        return $request;
    }

    function deleteRequest($subUrl) {
        $url = $this->compileUrl($subUrl);
        //trigger_error("Using method DELETE", E_USER_NOTICE);
        return Request::delete($url);
    }

    function authenticate($request) {
        return $request->authenticateWith($this->username, $this->password);
    }

    function execute($request, $expectedMimeType='text/html') {
        $request->expects($expectedMimeType);
        $request->_curlPrep();
        if ($request->serialized_payload != "")
            trigger_error("Payload: ". $request->serialized_payload, E_USER_NOTICE);
        return $request->send();
    }
}


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

?>