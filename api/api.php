<?php


namespace Api;

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
    private static $analytics;

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

    static function getAnalyticsApi() {
        return Api::$analytics;
    }

    static function initAnalyticsApi($host, $username, $password) {
        Api::$analytics = new Api($host, $username, $password);
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
        /*if ($request->serialized_payload != "")
            trigger_error("Payload: ". $request->serialized_payload, E_USER_NOTICE);*/
        return $request->send();
    }
}


?>