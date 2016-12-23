<?php

// SETTINGS.PHP
/**
 * Written by walter at 24/10/13
 */
/**
 * Mostra la pagina delle impostazioni presente nel menu laterale,
 * Questa pagina gestisce la pagina principale dei settings.
 * I dati dell'utente vengono presi alla riga 217, e poi in base al parametro GET 'update',
 * viene mostrata la pagina dei settings richiesta.
 */
include_once("settings/configuration.php");
include_once("settings/prices.php");
include_once("settings/monetization.php");
include_once("settings/personal.php");
include_once("settings/live.php");
include_once("settings/features.php");
include_once("settings/user_settings.php");

function wimtvpro_configure() {
    global $WIMTV_API_HOST;
    $uploads_info = wp_upload_dir();
    if (!isset($_GET["pack"])) {

        if (!isset($_GET["update"])) {
            $directory = $uploads_info["basedir"] . "/skinWim";
            $styleReg = "display:none";

            if (isset($_POST['wimtvpro_update']) && $_POST['wimtvpro_update'] == 'Y') {
                //Form data sent
                $error = 0;
                //Upload Skin
                $file = $_FILES['files']['name']["uploadSkin"];
                $tmpfile = $_FILES['files']['tmp_name']["uploadSkin"];
                $arrayFile = explode(".", $file);
                if (!empty($file)) {
                    if ($arrayFile[1] != "zip") {
                        echo '<div class="error"><p><strong>';
                        _e("This file isn't format correct for jwplayer's skin");
                        echo '</strong></p></div>';
                        $error++;
                    } else {
                        if (filesize($tmpfile) > 10485760) {
                            echo '<div class="error"><p><strong>';
                            _e("Uploaded file is ", "wimtvpro");
                            echo " " . round(filesize($tmpfile) / 1048576, 2);
                            _e("Kb. It must be less than", "wimtvpro");
                            echo " 10Mb.";
                            echo '</strong></p></div>';
                            $error++;
                        } else {
                            if (false === @move_uploaded_file($tmpfile, $uploads_info["basedir"] . "/skinWim" . "/" . $file)) {
                                echo '<div class="error"><p><strong>';
                                _e("Internal error.");
                                echo $uploads_info["basedir"] . "/skinWim/" . $file;
                                echo '</strong></p></div>';
                                $error++;
                            }

                            //$return = wimtvpro_unzip($directory . "/" . get_option('wp_nameSkin') . ".zip", $directory);
                            require_once(ABSPATH . '/wp-admin/includes/file.php'); //the cheat

                            WP_Filesystem();
//                            $return = unzip_file($directory . "/" . get_option('wp_nameSkin') . ".zip", $directory."/PINO");
                            $return = unzip_file($directory . "/" . $arrayFile[0] . ".zip", $directory);

                            if ($return) {
                                update_option('wp_nameSkin', $arrayFile[0]);
                            } else {
                                $error++;
                                echo '<div class="error"><p><strong>';
                                _e("Internal error.");
                                var_dump($return);
                                echo '</strong></p></div>';
                            }
                        }
                    }
                } else {
                    update_option('wp_nameSkin', $_POST['nameSkin']);
                }
                // Required
                if (strlen(trim($_POST['userWimtv'])) == 0) {
                    echo '<div class="error"><p><strong>';
                    _e("The username is required", "wimtvpro");
                    echo '</strong></p></div>';
                    $error++;
                }
                // Required
                if (strlen(trim($_POST['passWimtv'])) == 0) {
                    echo '<div class="error"><p><strong>';
                    _e("The password is required", "wimtvpro");
                    echo '</strong></p></div>';
                    $error++;
                }


                if ($error == 0) {

                    if ($_POST['sandbox'] == "No") {
                        update_option('wp_basePathWimtv', 'https://www.wim.tv/wimtv-webapp/rest/');
                    } else {
                        update_option('wp_basePathWimtv', 'http://peer.wim.tv/wimtv-webapp/rest/');
                    }

                    update_option('wp_userwimtv', $_POST['userWimtv']);
                    update_option('wp_passwimtv', $_POST['passWimtv']);
                    update_option('wp_registration', 'TRUE');

//                    initApi(get_option("wp_basePathWimtv"), get_option("wp_userwimtv"), get_option("wp_passwimtv"));
                    initApi(cms_getWimtvApiUrl(), cms_getWimtvUser(), cms_getWimtvPwd());

                    initAnalytics(cms_getWimtvStatsApiUrl(), get_option("wp_userwimtv"), null);
//                    if (get_option("wp_sandbox") == "No") {
//                        initAnalytics("http://www.wim.tv:3131/api/", get_option("wp_userwimtv"), null);
//                    } else {
//                        initAnalytics("http://peer.wim.tv:3131/api/", get_option("wp_userwimtv"), null);
//                    }


                    $response = apiGetProfile();
                    $arrayjsonst = json_decode($response);
                    
                    if ($arrayjsonst != null && $arrayjsonst->paypalEmail != "") {
                        update_option('wp_activePayment', "true");
                    } else {
                        update_option('wp_activePayment', "false");
                    }

                    // NS: DISATTIVATO CHECK SU $_POST['sandbox']
                    // if (($_POST['sandbox'] != get_option('wp_sandbox')) && (($_POST['userWimtv'] == "username") && ($_POST['passWimtv'] == "password"))) {

                    if ((($_POST['userWimtv'] == "username") && ($_POST['passWimtv'] == "password"))) {
                        update_option('wp_registration', 'FALSE');
                        update_option('wp_userwimtv', 'username');
                        update_option('wp_passwimtv', 'password');
                    } else {

                        if (count($arrayjsonst) > 0) {
                            update_option('wp_access_token', '');
                            update_option('wp_refresh_token', '');
                            echo '<div class="updated"><p><strong>';
                            _e('Update successful', "wimtvpro");
                            echo '</strong></p></div>';
                        } else {
                            update_option('wp_userwimtv', "username");
                            update_option('wp_passwimtv', "password");
                            echo '<div class="error"><p><strong>';
                            _e('Can not establish a connection with Wim.tv. Username and/or Password are not correct.', "wimtvpro");
                            echo '</strong></p></div>';
                        }
                    }

                    update_option('wp_heightPreview', $_POST['heightPreview']);
                    update_option('wp_widthPreview', $_POST['widthPreview']);

                    update_option('wp_sandbox', $_POST['sandbox']);
                    update_option('wp_urlVideosWimtv', 'videos');
                    update_option('wp_urlVideosDetailWimtv', 'videos?details=true&incomplete=true');
                    update_option('wp_urlThumbsWimtv', 'videos/{contentIdentifier}/thumbnail');
                    update_option('wp_urlEmbeddedPlayerWimtv', 'videos/{contentIdentifier}/embeddedPlayers?get=1');
                    update_option('wp_urlPostPublicWimtv', 'videos/{contentIdentifier}/showtime');
                    update_option('wp_urlPostPublicAcquiWimtv', 'videos/{contentIdentifier}/acquired/{acquiredIdentifier}/showtime');
                    update_option('wp_urlSTWimtv', 'videos/{contentIdentifier}/showtime/{showtimeIdentifier}');
                    update_option('wp_urlShowTimeWimtv', 'users/{username}/showtime');
                    update_option('wp_urlShowTimeDetailWimtv', 'users/{username}/showtime?details=true');
                    update_option('wp_urlUserProfileWimtv', 'users/{username}/profile');
                    update_option('wp_replaceContentWimtv', '{contentIdentifier}');
                    update_option('wp_replaceUserWimtv', '{username}');
                    update_option('wp_replaceacquiredIdentifier', '{acquiredIdentifier}');
                    update_option('wp_replaceshowtimeIdentifier', '{showtimeIdentifier}');
//                    update_option('wp_publicPage', $_POST['publicPage']);
                    update_option('wp_publicPage', isset($_POST['publicPage']) ? $_POST['publicPage'] : "");

                    update_page_wimvod();
                }
            }
            settings_configuration($directory);
        } else {

            echo "<div class='wrap'>";

            if (isset($_POST['wimtvpro_update']) && $_POST['wimtvpro_update'] == 'Y') {
                //UPDATE INFORMATION
//                var_dump($dati);exit;
//                if(strlen($dati['finance']['vatNumber']) == 0 || strlen($dati['finance']['vatNumber']) == 11 ){
                foreach ($_POST as $key => $value) {
                    if ($value == "")
                        unset($_POST[$key]);
                    //$key = str_replace("Uri","URI",$key);
                    $dati[$key] = $value;
                }
                $company = $dati['affiliate2'];
           
                unset($dati['wimtvpro_update']);
                unset($dati['submit']);
                unset($dati['submit']);
                unset($dati['affiliate2']);
                unset($dati['affiliateConfirm2']);
                

//                $profile = array(
//                'email' => 'nstest1@test.it',
//                'firstName' => 'nstest1',
//                'lastName' => 'nstest1'
//                );
//                $post = array();
//                if(isset($dati['livePassword'])){
//                $post = array(
//                    'profile' => $profile,
//                    'features' => $dati
//                );
//                }
//                 if(isset($dati['billingAddress'])){
//                $post = array(
//                    'profile' => $profile,
//                    'finance' => $dati
//                );
//                }
//                 if(isset($dati['facebookUrl'])){
//                $post = $dati;
//                }
//
//                $urlfile_thumb = @$_FILES['thumbnailFile']['tmp_name'];
//                
//                $uploads_info_thum = wp_upload_dir();
//                $directory_thum = $uploads_info_thum["basedir"] . "/imgtmp";
//                if (!is_dir($directory_thum)) {
//                    $directory_create = mkdir($uploads_info_thum["basedir"] . "/imgtmp");
//                }
//                $unique_temp_filename_thumb = "";
//                if ($urlfile_thumb != "") {
//                    $unique_temp_filename_thumb = $directory_thum . "/" . time() . '.' . preg_replace('/.*?\//', '', "tmp");
//                    $unique_temp_filename_thumb = str_replace("\\", "/", $unique_temp_filename_thumb);
//                
//
//                if (@move_uploaded_file($urlfile_thumb, $unique_temp_filename_thumb)) {
////                echo "FILE HAS BEEN COPIED TO: " . $unique_temp_filename;
//                } else {
////                echo "FILE COPY FAILED";
//                }
//} else {
//            echo '<div class="error"><p><strong>';
//            echo 'The server where your Wordpress is installed does not support upload of large (bigger than 2GB) files. 
//                    Try to set both <code>upload_max_filesize=0</code> and <code>post_max_size=0</code> in your php.ini file.';
//            echo '</strong></p></div>';
//            die();
//        }


                if (strlen($dati['vatNumber']) == 0 || strlen($dati['finance']['vatNumber']) == 11) {
                if(strlen($dati['vatNumber']) == 11){
                    $dati['finance']['vatNumber'] = $dati['vatNumber'];
                }
                
                  if($company){
                        $company_array = array(
                            'companyName' => $dati['companyName'],
                            'companyConfirm' => $dati['companyConfirm']
                        );
                        
                        $dati['finance']['companyName'] = $company_array['companyName'];
                        $dati['finance']['companyConfirm'] = $company_array['companyConfirm'];
                    }
                    
                    $dati_post = array(
                        'finance' => $dati['finance'],
                        'profile' => $dati['profile'],
                        'features' => $dati['features']
                    );

                 

//                 $thumbnailId = "";
//            if (isset($_FILES['thumbnailFile'])) {
//                $post = array(
//                    'thumbnail' => $unique_temp_filename_thumb
//                );
//                $response = apiUploadThumb($post);
//                if ($response->code == 201) {
//                    $arrayjsonst = json_decode($response);
//                    $thumbnailId = $arrayjsonst->thumbnailId;
//                }
//            }
//            
//                if($thumbnailId !=""){
//                    $dati_post['thumbnailId'] = $thumbnailId;
//                }
                  
                    $response = apiEditProfile($dati_post);

                    $arrayjsonst = json_decode($response);
//                    var_dump($arrayjsonst,$response->code);die;
//var_dump($arrayjsonst);
                    if ($dati['paypalEmail'] != "")
                        update_option('wp_activePayment', "true");
                    else
                        update_option('wp_activePayment', "false");

                    if ($response->code == 200) {
                        echo '<div class="updated"><p><strong>';
                        _e("Update successful", "wimtvpro");
                        echo '</strong></p></div>';
                    } else {
                        $testoErrore = "Inserire tutti i campi!";
//                 NS2016   foreach ($arrayjsonst->errors as $message) {
////                        $testoErrore .= $message->field . " : " . $message->message . "<br/>";
//                         $testoErrore .= $message. "<br/>";
//                    }
//                    echo '<div class="error"><p><strong>' . $testoErrore . '</strong></p></div>';
                        if (isset($arrayjsonst->message)) {
                            echo '<div class="error"><p><strong>' . $arrayjsonst->message . '</strong></p></div>';
                        } else {
                            echo '<div class="error"><p><strong>' . $testoErrore . '</strong></p></div>';
                        }
                    }

                    foreach ($dati as $key => $value) {
                        $key = str_replace("URI", "Uri", $key);
                    }
                } else {
                    echo '<div class="error"><p><strong>' . __("VatNumber must be 11 characters!", "wimtvpro") . '</strong></p></div>';
                }
            }
            //Read
            $response = apiGetProfile();

            $dati = json_decode($response, true);

            user_settings_configuration($dati);
//            switch ($_GET['update']) {
//                case "1": //Monetization
//                    settings_monetization($dati);
//                    break;
//
//                case "2": //Live
//                    settings_live($dati);
//                    break;
//
//                case "3": //Personal
//                    settings_personal($dati);
//                    break;
//
////                NS: Disabling "Features Page"
//                case "4": //Features
//                    settings_features($dati);
//                    break;
//            }

            echo "</div>";
        }
    } else {

        settings_prices();
    }
}