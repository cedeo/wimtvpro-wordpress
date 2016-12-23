<?php

/**
 * Written by walter at 06/11/13
 */

/**
 * Questa funzione ritorna un booleano che indica qualora l'utente abbia effettuato la registrazione o no.
 * In caso di utente non registrato, mostra anche un alert per reindirizzarlo alla pagina di registrazione/login.
 * Dovrebbe essere chiamata all'inizio di ogni pagina che richiede che l'utente sia loggato per funzionare.
 */
function wimtvpro_alert_reg() {
    //If user isn't registered or not inser user and password
    if ((get_option("wp_registration") == 'FALSE') && ((get_option("wp_userwimtv") == "username") && get_option("wp_passwimtv") == "password")) {
        echo "<div class='error'>" .
        __("If you don't have a WimTV account", "wimtvpro") .
        " <a href='?page=" . __('REGISTER_urlLink', "wimtvpro") . "'>" . __('REGISTER_menuLink', "wimtvpro") .
        "</a> | <a href='?page=" . __('SETTINGS_urlLink', "wimtvpro") . "'>" . __("LOGIN", "wimtvpro") . "</a> "
        . __("with your WimTV credentials", "wimtvpro") . "</div>";
        return FALSE;
    } else {
        return TRUE;
    }
}

?>