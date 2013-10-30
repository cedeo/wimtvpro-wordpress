<?php

include_once 'kint/Kint.class.php';

function defaultLog() {

    $username = get_option("wp_userwimtv");
    $password = get_option("wp_passwimtv");
    $registration = get_option('wp_registration');
    $nameskin = get_option('wp_nameSkin');
    $uploadskin = get_option('wp_uploadSkin');
    $heightPreview = get_option('wp_heightPreview');
    $registration = get_option('wp_widthPreview');
    $widthPreview = get_option('wp_basePathWimtv');
    $urlVideosWimtv = get_option('wp_urlVideosWimtv');
    $urlVideosDetailWimtv = get_option('wp_urlVideosDetailWimtv');
    $urlThumbsWimtv = get_option('wp_urlThumbsWimtv');
    $urlEmbeddedPlayerWimtv = get_option('wp_urlEmbeddedPlayerWimtv');
    $urlPostPublicWimtv = get_option('wp_urlPostPublicWimtv');
    $urlPostPublicAcquiWimtv = get_option('wp_urlPostPublicAcquiWimtv');
    $urlSTWimtv = get_option('wp_urlSTWimtv');
    $urlShowTimeWimtv = get_option('wp_urlShowTimeWimtv');
    $urlShowTimeDetailWimtv = get_option('wp_urlShowTimeDetailWimtv');
    $urlUserProfileWimtv = get_option('wp_urlUserProfileWimtv');
    $replaceContentWimtv = get_option('wp_replaceContentWimtv');
    $replaceUserWimtv = get_option('wp_replaceUserWimtv');
    $replaceacquiredIdentifier = get_option('wp_replaceacquiredIdentifier');
    $replaceshowtimeIdentifier = get_option('wp_replaceshowtimeIdentifier');
    $sandbox = get_option('wp_sandbox');
    $activeLive = get_option('wp_activeLive');
    $activePayment = get_option('wp_activePayment');
    $shareVideo = get_option('wp_shareVideo');
    $name = get_option('wp_name');
    $logo = get_option('wp_logo');
    $date = get_option('wp_date');
    $email = get_option('wp_email');
    $social = get_option('wp_social');


    d( $_SERVER );
    d( $_POST );
    d( $_GET );
    //d( $_REQUEST );
    //d( $_FILES );
    d( $username );
    d( $password );
    d($registration );
    d($nameskin );
    //d($uploadskin );
    //d($heightPreview );
    //d($registration );
    //d($widthPreview );
    //d($urlVideosWimtv );
    //d($urlVideosDetailWimtv );
    //d($urlThumbsWimtv );
    //d($urlEmbeddedPlayerWimtv );
    //d($urlPostPublicWimtv );
    //d($urlPostPublicAcquiWimtv );
    //d($urlSTWimtv );
    //d($urlShowTimeWimtv );
    //d($urlShowTimeDetailWimtv );
    //d($urlUserProfileWimtv );
    //d($replaceContentWimtv );
    //d($replaceUserWimtv );
    //d($replaceacquiredIdentifier );
    //d($replaceshowtimeIdentifier );
    //d($sandbox );
    //d($activeLive );
    //d($activePayment );
    //d($shareVideo );
    //d($name );
    //d($logo );
    //d($date );
    //d($email );
    //d($social );
}

function debug($message) {
    d($message);
}

function logParams() {
    d($_SERVER);
    d($_POST);
    d($_GET);
}

?>
