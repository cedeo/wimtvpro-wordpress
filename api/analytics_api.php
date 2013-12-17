<?php
/**
 * Written by walter at 30/10/13
 */
include_once("api.php");

use Api\Api;


function initAnalytics($host, $username, $password) {
    Api::initAnalyticsApi($host, $username, $password);
}

function getAnalytics() {
    return Api::getAnalyticsApi();
}

function analyticsGetStreams($from="", $to="") {
    $apiAccessor = getAnalytics();
    if ($from == "" && $to == "")
        $request = $apiAccessor->getRequest("users/" . $apiAccessor->username . "/streams");
    else
        $request = $apiAccessor->getRequest("users/" . $apiAccessor->username . "/streams?from=" . $from . "&to=" . $to);

    return $apiAccessor->execute($request);
}

function analyticsGetUser($from="", $to="") {
    $apiAccessor = getAnalytics();
    if ($from == "" && $to == "")
        $request = $apiAccessor->getRequest("users/" . $apiAccessor->username);
    else
        $request = $apiAccessor->getRequest("users/" . $apiAccessor->username . "?from=" . $from . "&to=" . $to);
    return $apiAccessor->execute($request);
}

function analyticsGetPacket() {
    $apiAccessor = getAnalytics();
    $request = $apiAccessor->getRequest("users/" . $apiAccessor->username . "/commercialPacket/usage");
    return $apiAccessor->execute($request);
}

if (get_option("wp_sandbox") == "No") {
    initAnalytics("http://stats.wim.tv/api/", get_option("wp_userwimtv"), null);
} else {
    initAnalytics("http://peer.wim.tv:3131/api/", get_option("wp_userwimtv"), null);
}

?>
