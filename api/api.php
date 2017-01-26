<?php

/**
 * Written by walter at 16/10/13
 * Updated by Netsense s.r.l. 2014-2015
 */

namespace Api;

require("Httpful/Bootstrap.php");
\Httpful\Bootstrap::init();

use \Httpful\Request;
use \Httpful\Mime;

class Api {

    public $host = null;
    public $username = null;
    public $liveHostsUrl;
    public $password = null;
    private static $instance;
    private static $analytics;
    public $language = "it";

    function __construct($host, $username, $password) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->liveHostsUrl = 'liveStream/' . $this->username . '/' . $this->username . '/hosts';
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

    // NS API PROGRAMMINGS
    function getHost() {
        return $this->host;
    }

    function compileUrl($subUrl) {
        $url = $this->host . $subUrl;
        return $url;
    }

    function getRequest($subUrl) {
        $url = $this->compileUrl($subUrl);
        return Request::get($url);
    }

    function postRequest($subUrl) {
        $url = $this->compileUrl($subUrl);
        $request = Request::post($url);
        $request->sendsType(Mime::FORM);
        return $request;
    }

    function deleteRequest($subUrl) {
        $url = $this->compileUrl($subUrl);
        return Request::delete($url);
    }

    function downloadRequest($subUrl) {
        $request = Api::getRequest($subUrl);
        $request->no_body = true;
        return $request;
    }

    function authenticate($request) {
        return $request->authenticateWith($this->username, $this->password);
    }

    function execute($request, $expectedMimeType = 'text/html', $clientLanguage = null) {
        $request->expects($expectedMimeType);
        $request->addHeader("X-Wimtv-Pro-Plugin-Name", cms_getName());
        if ($clientLanguage)
            $request->addHeader('Accept-Language', $clientLanguage);
        else
            $request->addHeader('Accept-Language', $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
        $request->_curlPrep();
        try {
            $result = $request->send();
        } catch (\Exception $exception) {
            //trigger_error($exception->getMessage(), E_USER_NOTICE);
            $result = "";
        }
        return $result;
    }

    function executeRequest($request, $expectedMimeType = 'text/html', $clientLanguage = null, $progress = null,$private = false,$timezone = null) {
   
   
        
        if($private){
        $request->addHeader("Authorization", "Bearer ".get_option('wp_access_token'));
     }

        $request->expects($expectedMimeType);
        if(isset($timezone)){
        $request->addHeader("X-Wimtv-timezone", "3600000");
        }

        if (isset($progress)) {
            $request->addHeader("X-Wimtv-progressbarId", $progress);
//             $request->addHeader("Content-Type", "multipart/form-data");
        }
        
        
        $request->addHeader("Accept", "application/json");


        $request->addHeader("Content-Type", "application/json");
//        $request->addHeader("Content-Type", "multipart/form-data");
        

        if ($clientLanguage){
            $request->addHeader('Accept-Language', $clientLanguage);
        }else if(isset($language)){
            if($language){
            $request->addHeader('Accept-Language', $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
            }
        }else{
//            $request->addHeader('Accept-Language', $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
        }
//      var_dump("PREPARE");
        $request->_curlPrep();
//       var_dump($request);die;
        try {
            $result = $request->send();
        } catch (\Exception $exception) {

            //trigger_error($exception->getMessage(), E_USER_NOTICE);
            $result = "";
        }

        return $result;
    }

}

?>
