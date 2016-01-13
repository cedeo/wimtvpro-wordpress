<?php

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

function cms_getWimtvApiUrl() {
    global $WIMTV_API_HOST;
    return $WIMTV_API_HOST;
}

function cms_getWimtvApiProductionUrl() {
    return get_option("wp_basePathWimtv");
}

function cms_getWimtvApiTestUrl() {
    return "http://peer.wim.tv/wimtv-webapp/rest/";
}

?>
