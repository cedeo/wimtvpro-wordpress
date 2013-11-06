<?php
/**
 * Written by walter at 06/11/13
 */
function wimtvpro_alert_reg() {

    //If user isn't registered or not inser user and password
    if ((get_option("wp_registration")=='FALSE') && ((get_option("wp_userwimtv")=="username") && get_option("wp_passwimtv")=="password")){
        echo "<div class='error'>" . __("If you don't have a WimTV account","wimtvpro") . " <a href='?page=WimTvPro_Registration'>" . __("REGISTER","wimtvpro") . "</a> | <a href='?page=WimTvPro'>" . __("LOGIN","wimtvpro") . "</a> " .   __("with your WimTV credentials","wimtvpro") . "</div>";
        return FALSE;
    } else {
        return TRUE;
    }
}

?>