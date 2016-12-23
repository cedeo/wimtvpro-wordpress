<?php
include_once("wimtv_api.php");
/**
 * Written by Netsense s.r.l. 2015
 * 
 * CMS: WORDPRESS
 */
function cms_getWimtvUser() {
    return get_option("wp_userwimtv");
}

function cms_getWimtvPwd() {
    return get_option("wp_passwimtv");
}

function cms_getWimtvClientId() {
    return get_option("wp_client_id");
}

function cms_getWimtvSecretKey() {
    return get_option("wp_secret_key");
}

function cms_getWimtvAccessToken() {
    return get_option("wp_access_token");
}

function cms_getWimtvRefreshToken() {
    return get_option("wp_refresh_token");
}

function cms_getWimtvApiUrl() {
    global $WIMTV_API_HOST;
    return $WIMTV_API_HOST;
}

function cms_getWimtvApiProductionUrl() {
    return get_option("wp_basePathWimtv");
}

function cms_getWimtvApiTestUrl() {
    return "https://peer.wim.tv/wimtv-webapp/rest/";
}

function cms_getWimtvStatsApiUrl() {
    $statsApiUrl = isConnectedToTestServer()? cms_getWimtvStatsApiTestUrl():cms_getWimtvStatsApiProductionUrl();
    return $statsApiUrl;
}


function cms_getWimtvStatsApiProductionUrl() {
    return "http://www.wim.tv:3131/api/";
}

function cms_getWimtvStatsApiTestUrl() {
    return "http://peer.wim.tv:3131/api/";
}



function cms_getName(){
    return "Wordpress";
}
?>
