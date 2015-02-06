<?php

/**
 *  Questo file viene chiamato via Http,e fornisce funzionalità diverse in base al parametro GET 'namefunction' passato.
 */
global $user, $wpdb;
include("../../../wp-load.php");
include_once("api/api.php");

header('Content-type: application/json');

$url_video = get_option("wp_basePathWimtv") . get_option("wp_urlVideosDetailWimtv");
$credential = get_option("wp_userwimtv") . ":" . get_option("wp_passwimtv");
$table_name = $wpdb->prefix . 'wimtvpro_video';

$uploadMaxFile = return_bytes(ini_get('upload_max_filesize'));
$postmaxsize = return_bytes(ini_get('post_max_size'));
$uploadMaxFile_mb = number_format($uploadMaxFile / 1048576, 2) . 'MB';
$postmaxsize_mb = number_format($postmaxsize / 1048576, 2) . 'MB';

$function = "";
$id = "";
$acid = "";
$ordina = "";

if (isset($_GET['namefunction']))
    $function = $_GET["namefunction"];
else if (isset($_POST['namefunction']))
    $function = $_POST["namefunction"];
if (isset($_GET['id']))
    $id = $_GET['id'];
if (isset($_GET['showtimeId']))
    $stid = $_GET['showtimeId'];
if (isset($_GET['ordina']))
    $ordina = $_GET['ordina'];

if (empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    /**
     * Questo caso si presenta quando la dimensione del file caricato è maggiore del massimo permesso dall'installazione di Wordpress.
     * Viene fatta una post che arriva con payload vuoto.
     */
    echo '<div class="error"><p><strong>';
    echo str_replace("%d", $postmaxsize_mb, __("The server where your Wordpress is installed does not support upload of files exceeding %d. If you want to upload videos larger than %d, please modify your server settings. WimTV supports up to 2GB file size per upload.", "wimtvpro"));
    echo '</strong></p></div>';
}

//trigger_error($function, E_USER_NOTICE);
switch ($function) {

    case "putST":
        /**
         * Richiede che vengano passati anche come parametri GET 'id', e tutti i valori presenti nell'array $param.
         * Aggiunge un video a WimVod (lo mette in Showtime).
         */
        $licenseType = "";
        $paymentMode = "";
        $ccType = "";
        $pricePerView = "";
        $pricePerViewCurrency = "";

        if (isset($_GET['licenseType']))
            $licenseType = $_GET['licenseType'];
        if (isset($_GET['paymentMode']))
            $paymentMode = $_GET['paymentMode'];
        if (isset($_GET['ccType']))
            $ccType = $_GET['ccType'];
        if (isset($_GET['pricePerView']))
            $pricePerView = $_GET['pricePerView'];
        if (isset($_GET['pricePerViewCurrency']))
            $pricePerViewCurrency = $_GET['pricePerViewCurrency'];

        $param = array('licenseType' => $licenseType,
            'paymentMode' => $paymentMode,
            'ccType' => $ccType,
            'pricePerView' => $pricePerView,
            'pricePerViewCurrency' => $pricePerViewCurrency
        );

        $response = apiPublishOnShowtime($id, $param);

        if ($response) {
            $state = "showtime";
        }
        $array_response = json_decode($response);


        if ($array_response->result == "SUCCESS") {
            dbUpdateVideoState($id, $state, $array_response->showtimeIdentifier);
        }


        echo $response;

        die();
        break;
    case "putAcqST":
        /**
         * Richiede che vengano passati anche come parametri GET 'id', 'coId' e tutti i valori presenti nell'array $param.
         * Aggiunge un video acquired, con acquiredIdentifier corrispondente a 'coId' a WimVod (lo mette in Showtime).
         */
        $licenseType = "";
        $paymentMode = "";
        $ccType = "";
        $pricePerView = "";
        $pricePerViewCurrency = "";

        if (isset($_GET['coId']))
            $acid = $_GET['coId'];
        if (isset($_GET['licenseType']))
            $licenseType = $_GET['licenseType'];
        if (isset($_GET['paymentMode']))
            $paymentMode = $_GET['paymentMode'];
        if (isset($_GET['ccType']))
            $ccType = $_GET['ccType'];
        if (isset($_GET['pricePerView']))
            $pricePerView = $_GET['pricePerView'];
        if (isset($_GET['pricePerViewCurrency']))
            $pricePerViewCurrency = $_GET['pricePerViewCurrency'];

        $params = array('licenseType' => $licenseType,
            'paymentMode' => $paymentMode,
            'ccType' => $ccType,
            'pricePerView' => $pricePerView,
            'pricePerViewCurrency' => $pricePerViewCurrency
        );

        $state = "showtime";

        dbUpdateVideoState($id, $state);

        //Richiamo API  http://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime
        //curl -u {username}:{password} -d "licens e_type=TEMPLATE_LICENSE&paymentMode=PAYPERVIEW&pricePerView=50.00&pricePerViewCurrency=EUR" http://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime
        $response = apiPublishAcquiredOnShowtime($id, $acid, $params);
        echo $response;
        die();
        break;
    case "removeST";
        /**
         * Richiede che vengano passati anche come parametri GET 'id' e 'showtimeId'.
         * Rimuove un video da WimVod (lo toglie dallo showtime).
         */
        dbSetVideoPosition($id, "0", "");

        $response = apiDeleteFromShowtime($id, $stid);

        echo $response;
        die();
        break;
    case "StateViewThumbs":
        $state = $_GET['state'];
        dbSetViewVideoModule($id, $state);

        update_page_wimvod();

        echo $state;
        die();
        break;
    case "ReSortable":
        /**
         * Richiede che venga passato anche come parametro GET 'ordina'.
         * Riordina i video nella tabella WimBox o WimVod.
         */
        $list_video = explode(",", $ordina);
        foreach ($list_video as $position => $item) {
            $position = $position + 1;
            dbSetVideoPosition($item, $position);

            update_page_wimvod();
        }

        die();
        break;

    case "urlCreate":
        /**
         * Richiede che venga passato anche come parametro GET 'titleLive'.
         * Crea e ritorna l'url del video live con titolo passato.
         */
        $response = apiCreateUrl(urlencode($_GET['titleLive']));
        echo $response;
        break;

    case "passCreate":
        /**
         * Richiede che venga passato anche come parametro GET 'newPass'.
         * Cambia la password dei live dell'utente autenticato.
         */
        // NS: We changed the behaviour of this "case" because 
        // the method apiChangePassword(...) calls a remote API that
        // seems to be unavailable.
        // We now call apiEditProfile(...)
//        $response = apiChangePassword($_GET['newPass']);
//        echo $response;
//        die();

        $params = array();
        $params["liveStreamPwd"] = $_GET['newPass'];
        $response = apiEditProfile($params);
        $arrayjsonst = json_decode($response);
        $response_string = "";
        if ($arrayjsonst->result == "SUCCESS") {
            $response_string = _e("Update successful", "wimtvpro");
        } else {
            foreach ($arrayjsonst->messages as $message) {
                $response_string .= $message->field . " : " . $message->message . "<br/>";
            }
        }
        return $response_string;
        break;

    case "RemoveVideo":
        /**
         * Richiede che venga passato anche come parametro GET 'id'.
         * Rimuove il video con host_id corrispondente al parametro 'id' da wim.tv.
         */
        //connect at API for upload video to wimtv
        $response = apiDeleteVideo($id);
        $arrayjsonst = json_decode($response);
        if ($arrayjsonst->result == "SUCCESS")
            dbDeleteVideo($id);
        echo $response;
        break;

    case "getUsers":
        $sqlVideos = dbGetViewVideoModule($id);
        $stateView = explode("|", $sqlVideos[0]->viewVideoModule);
        $arrayUsers = explode(",", $stateView[1]);

        $q_users = mysql_query("SELECT ID,user_login FROM " . $wpdb->prefix . "users");
        while ($username = mysql_fetch_array($q_users)) {
            $valueOption = "U-" . $username['ID'];
            echo "<option value='" . $valueOption . "'";
            foreach ($arrayUsers as $typeUser) {
                if ($valueOption == $typeUser)
                    echo " selected='selected' ";
            }
            echo ">" . $username['user_login'] . "</option>";
        }
        die();
        break;

    case "getRoles":
        $sqlVideos = dbGetViewVideoModule($id);
        $stateView = explode("|", $sqlVideos[0]->viewVideoModule);
        $arrayRoles = explode(",", $stateView[1]);

        global $wp_roles;
        $roles = $wp_roles->get_names();
        foreach ($roles as $role => $value) {
            $valueOption = "R-" . $role;
            echo "<option value='" . $valueOption . "'";
            foreach ($arrayRoles as $typeRole) {
                if ($valueOption == $typeRole)
                    echo " selected='selected' ";
            }
            echo ">" . $value . "</option>";
        }
        die();
        break;

    case "getAlls":
        $sqlVideos = dbGetViewVideoModule($id);
        $stateView = explode("|", $sqlVideos[0]->viewVideoModule);
        echo "<option value='All'";
        if (($stateView[1] == "") || ($stateView[1] == "All"))
            echo " selected='selected' ";
        echo ">" . __('Everybody', "wimtvpro") . "</option>";

        echo "<option value='No'";
        if ($stateView[1] == "No")
            echo " selected='selected' ";
        echo ">" . __('Nobody (Administrators Only)', "wimtvpro") . "</option>";


        die();
        break;



    case "uploadFile":
        /**
         * Esegue l'upload di un video su wim.tv.
         */
        $sizefile = filesize($_FILES['videoFile']['tmp_name']);
//        var_dump($_FILES);
//        print "<hr>";
//        var_dump("sizefile is: " . $sizefile . "<br>");

        $urlfile = @$_FILES['videoFile']['tmp_name'];
        $uploads_info = wp_upload_dir();
        $directory = $uploads_info["basedir"] . "/videotmp";
        if (!is_dir($directory)) {
            $directory_create = mkdir($uploads_info["basedir"] . "/videotmp");
        }
        $unique_temp_filename = "";
        if ($urlfile != "") {
            $unique_temp_filename = $directory . "/" . time() . '.' . preg_replace('/.*?\//', '', "tmp");
            $unique_temp_filename = str_replace("\\", "/", $unique_temp_filename);
            if (@move_uploaded_file($urlfile, $unique_temp_filename)) {
//                echo "FILE HAS BEEN COPIED TO: " . $unique_temp_filename;
            } else {
//                echo "FILE COPY FAILED";
            }
        } else {
            echo '<div class="error"><p><strong>';
            echo 'The server where your Wordpress is installed does not support upload of large (bigger than 2GB) files. 
                    Try to set both <code>upload_max_filesize=0</code> and <code>post_max_size=0</code> in your php.ini file.';
            echo '</strong></p></div>';
            die();
        }
        $error = 0;
        $titlefile = $_POST['titlefile'];
        $descriptionfile = $_POST['descriptionfile'];
        $video_category = $_POST['videoCategory'];
        $contentIdentifier = $_POST['uploadIdentifier'];

        // Required
        if (strlen(trim($titlefile)) == 0) {
            echo '<div class="error"><p><strong>';
            _e("You must write a title", "wimtvpro");
            echo '</strong></p></div>';
            $error++;
        }

        if ((strlen(trim($urlfile)) > 0) && ($error == 0)) {
            global $user, $wpdb;

            $table_name = $wpdb->prefix . 'wimtvpro_video';

            //UPLOAD VIDEO INTO WIMTV
            set_time_limit(0);

            $category_tmp = array();
            $subcategory_tmp = array();
            $post = array("file" => $unique_temp_filename,
                "title" => $titlefile,
                "description" => $descriptionfile,
                'uploadIdentifier' => $contentIdentifier);

            if (count($video_category) > 0) {
                $id = 0;
                foreach ($video_category as $cat) {
                    $subcat = explode("|", $cat);
                    if ($subcat[0] != "") {
                        $post['category[' . $id . ']'] = $subcat[0];
                        $post['subcategory[' . $id . ']'] = $subcat[1];
                        $id++;
                    }
                }
            }

            $response = apiUpload($post);
            $arrayjsonst = json_decode($response);

            if (isset($arrayjsonst->contentIdentifier)) {
                echo '<div class="updated"><p><strong>';
                _e("Upload successful", "wimtvpro");
                $handle = opendir($directory);
                while (($file = readdir($handle)) !== false) {
                    @unlink($directory . "/" . $file);
                }
                closedir($handle);
                echo '</strong></p></div>';
                $status = 'OWNED|' . $_FILES['videoFile']['name'];
                dbInsertVideo(get_option("wp_userwimtv"), $arrayjsonst->contentIdentifier, "", $status, $arrayjsonst->urlThumbs, "", "", $titlefile, "", "", "");
            } else {
                $error++;
                echo '<div class="error"><p><strong>';
                _e("Upload error", "wimtvpro");
                echo $response . '</strong></p></div>';
            }
        } else {

            $error++;
            if ($_FILES['videoFile']['name'] == "") {

                $error++;
                echo '<div class="error"><p><strong>';
                _e("You must upload a file", "wimtvpro");
                echo '</strong></p></div>';
            } else {

                switch ($_FILES['videoFile']['error']) {

                    case "1":
                        echo '<div class="error"><p><strong>';
                        echo str_replace("%d", $uploadMaxFile_mb, __("The server where your Wordpress is installed does not support upload of files exceeding %d. If you want to upload videos larger than %d, please modify your server settings. WimTV supports up to 2GB file size per upload.", "wimtvpro")) . " [upload_max_filesize] ";
                        echo '</strong></p></div>';
                        break;

                    case "2":
                        echo '<div class="error"><p><strong>';
                        echo str_replace("%d", $postmaxsize_mb, __("The server where your Wordpress is installed does not support upload of files exceeding %d. If you want to upload videos larger than %d, please modify your server settings. WimTV supports up to 2GB file size per upload.", "wimtvpro")) . " [MAX_FILE_SIZE] ";
                        echo '</strong></p></div>';
                        break;
                }
            }
            die();
        }



        break;

    default:
        //echo "Non entro";
        die();
}
?>
