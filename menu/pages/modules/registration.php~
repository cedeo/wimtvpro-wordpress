<?php

/**
 * Written by walter at 24/10/13
 */

/**
 * Esegue la registrazione di un utente su wim.tv.
 * Permette anche di switchare tra peer e www attraverso il passaggio del parametro $sandbox.
 */
function wimtvpro_register($name, $surname, $email, $username, $password, $password_repeat, $sex, $sandbox) {

    // NS: Currently 'sandbox' is always set to "No" because dropdown selection 
    // of test vs production server has been disabled
    // (see "menu/pages/settings/configuration.php")
    if ($sandbox == "No") {
        update_option('wp_basePathWimtv', 'https://www.wim.tv/wimtv-webapp/rest/');
    } else {
        update_option('wp_basePathWimtv', 'http://peer.wim.tv/wimtv-webapp/rest/');
    }
    $error = 0;

    if ($_POST['reg_RepeatPassword'] != $_POST['reg_Password']) {
        $error++;
        $testoErrore = __("Password isn't same", "wimtvpro") . "<br/>";
        $_POST['reg_RepeatPassword'] = "";
        $_POST['reg_Password'] = "";
    }
    if (($error == 0) && (isset($_POST['reg_acceptEula'])) &&
            ($name != "") && ($surname != "") &&
            ($email != "") && ($username != "") && ($password != "") &&
            ($password_repeat != "") && ($sex != "")) {

        $post = array('acceptEula' => $_POST['reg_acceptEula'],
            'name' => $name,
            "surname" => $surname,
            "email" => $email,
            "username" => $username,
            "password" => $password,
            "role" => "webtv",
            "sex" => $sex,
            "dateOfBirth" => "01/01/1900");

        $response = apiRegistration($post);
        $arrayjsonst = json_decode($response);

        if ($arrayjsonst) {
            if ($arrayjsonst->result == "SUCCESS") {

                echo '
              <script type="text/javascript">
				<!--
				window.location = "admin.php?page=WimTvPro&pack=1";
				//-->
              </script>';
                echo '<div class="updated"><p><strong>';
                _e("Registration successful.", "wimtvpro");
                echo '</strong></p></div>';
                update_option('wp_userwimtv', $_POST['reg_Username']);
                update_option('wp_passwimtv', $_POST['reg_Password']);
                update_option('wp_registration', 'TRUE');

                // NS: AFTER USER CREATION IMMEDIATLY SET VISIBILITY OF VIDEOS IN WIMVOD TO PUBLIC
                if (isset($_POST['reg_Password']) && isset($_POST['reg_Password'])) {
                    initApi(get_option("wp_basePathWimtv"), $_POST['reg_Username'], $_POST['reg_Password']);
                    $dati["hidePublicShowtimeVideos"] = "false";
                    $response = apiEditProfile($dati);
                }
            } else {
                foreach ($arrayjsonst->messages as $message) {
                    $testoErrore = $message->field . " : " . $message->message . "<br/>";
                }
                $error++;
            }
        } else {
            $testoErrore = __("Internal error. Contact wimTV's administrator.", "wimtvpro");
            $error++;
        }
    } else {
        $error++;
        $testoErrore = __("You did not compile all requested fields", "wimtvpro");
    }

    if ($error > 0) {
        $styleReg = "display:block";
        echo '<div class="error"><p><strong>' . $testoErrore . '</strong></p></div>';
    }
}

?>