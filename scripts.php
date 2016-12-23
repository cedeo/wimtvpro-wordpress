<?php

/**
 *  Questo file viene chiamato via Http,e fornisce funzionalità diverse in base al parametro GET 'namefunction' passato.
 */
global $user, $wpdb;
include("../../../wp-load.php");
include_once("api/api.php");

//header('Content-type: application/json');
//var_dump("CIAOOO init2");exit;
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
        $param = array();
        if (isset($_GET['licenseType'])) {
            $licenseType = $_GET['licenseType'];
            $param['licenseType'] = $licenseType;
        }
        if (isset($_GET['paymentMode']))
            $paymentMode = $_GET['paymentMode'];
        if (isset($_GET['ccType'])) {
            $ccType = $_GET['ccType'];
            $param['ccType'] = $ccType;
        }
        if (isset($_GET['pricePerView'])) {
            $pricePerView = $_GET['pricePerView'];
            $param['pricePerView'] = $pricePerView;
        }
        if (isset($_GET['pricePerViewCurrency']))
            $pricePerViewCurrency = $_GET['pricePerViewCurrency'];

//      NS2016 Commentato  $param = array('licenseType' => $licenseType,
//            'paymentMode' => $paymentMode,
//            'ccType' => $ccType,
//            'pricePerView' => $pricePerView,
//            'pricePerViewCurrency' => $pricePerViewCurrency
//        );
//        $param = array(
//            'public' => 'false', //opzionale, Whether the WimVod item has to be visible in public pages or not. 
//            'licenseType' => $licenseType,
//            'paymentMode' => $paymentMode,
//            'ccType' => $ccType,
//            'bundleId' => "",
//            'pricePerView' => $pricePerView,
//            'pricePerViewCurrency' => $pricePerViewCurrency
//        );

        $param['public'] = 'false';

        $response = apiPublishOnShowtime($id, $param);


        $array_response = json_decode($response);

        if ($response->code == 201) {

            dbUpdateVideoStateByBox($id, $state, $array_response->vodId,$array_response->licenseType,$array_response->pricePerView);
        }


        echo $response->code;

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
        //$stid
        $response = apiDeleteFromShowtime($stid);

        echo $response->code;
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
       
        $params = array(
            'base' => $_GET['titleLive']
        );
       
        $response = apiCreateStreamUrl($params);
//        var_dump($response->code, json_decode($response));
//        exit;
//        $response = apiCreateUrl(urlencode($_GET['titleLive']));
        $json = json_decode($response);
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
        if ($response->code == 204){
        dbDeleteVideo($id);
        
        }
        $json = json_decode($response);
    $array = array();
    $array['code'] = $response->code;
    if(isset($json->message)){
    $array['message'] = $json->message;
    }
    
        echo json_encode($array);
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
    case "UpdateMetadati":

        $boxId = $_POST['boxId'];
        $videoTags = $_POST['video'];
        $title = $_POST['titlefile'];
        $description = $_POST['descriptionfile'];
        $thumbnailId = $_POST['thumbnailId'];

        $params = array(
            'title' => $title,
            'description' => $description,
        );
        if (isset($thumbnailId)) {
            $params['thumbnailId'] = $thumbnailId;
        }
        if (sizeof($videoTags) >= 1 && $videoTags[0] != "") {

            $tags = array();
            if (isset($videoTags)) {
                foreach ($videoTags as $tag) {
                    if ($tag != "") {
                        array_push($tags, $tag);
                    }
                }
            }
            $params['tags'] = $tags;
        }

        $response = apiUpdateWimboxItem($boxId, $params);

        if ($response->code == 200) {
            $array_json = json_decode($response);
            if (isset($array_json->vodId)) {
                $vodId = $array_json->vodId;
            }
            
//            if(isset($array_json->thumbnailId)){
//                $path = __("API_URL", "wimtvpro");
//             $url_thumbs = '<img src="' . $path. 'asset/thumbnail/'.$array_json->thumbnailId. '"  title="' . $title . '" class="wimtv-thumbnail" />';
//            
//            }else{
//             $url_thumbs = '<img src="' . '/wp-content/plugins/wimtvpro/images/empty.jpg"' . '"  title="' . $title . '" class="wimtv-thumbnail" />';
//            }
         dbUpdateMetadati($array_json->title,$array_json->boxId);
//            $res = dbUpdateVideo($array_json->state, $array_json->status, $array_json->title, $urlThumbs, null, $array_json->duration, $vodId, $array_json->boxId, $array_json->contentId, $array_json->thumbnailId, $array_json->source, $array_json->vodCount);
//             var_dump("CI SIAMOOOOO",$res);die;
            }
        echo $response->code;
        break;
    case "uploadFile":


        /**
         * Esegue l'upload di un video su wim.tv.
         */
        $sizefile = filesize($_FILES['videoFile']['tmp_name']);


        $urlfile = @$_FILES['videoFile']['tmp_name'];

//         $dati["BBB"] = $urlfile;
//    echo json_encode($dati);die;
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


            $sizefile_thumbnail = filesize($_FILES['thumbnailFile']['tmp_name']);
//        var_dump($_FILES);
//        print "<hr>";
//        var_dump("sizefile is: " . $sizefile . "<br>");

            $urlfile_thumb = @$_FILES['thumbnailFile']['tmp_name'];
//        $dati["AAAA"] = $urlfile_thumb;
//    echo json_encode($dati);die;
            $uploads_info_thum = wp_upload_dir();
            $directory_thum = $uploads_info_thum["basedir"] . "/imgtmp";
            if (!is_dir($directory_thum)) {
                $directory_create = mkdir($uploads_info_thum["basedir"] . "/imgtmp");
            }
            $unique_temp_filename_thumb = "";
            if ($urlfile_thumb != "") {
                $unique_temp_filename_thumb = $directory_thum . "/" . time() . '.' . preg_replace('/.*?\//', '', "tmp");
                $unique_temp_filename_thumb = str_replace("\\", "/", $unique_temp_filename_thumb);
            }

            if (@move_uploaded_file($urlfile_thumb, $unique_temp_filename_thumb)) {
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
//        $video_category = $_POST['videoCategory'];

        $contentIdentifier = $_POST['uuid'];
        $videoTags = $_POST['video'];

//  $v4uuid = UUID::v4();
//        $uuid = $_POST[$v4uuid];
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

            $thumbnailId = "";
            if (isset($_FILES['thumbnailFile'])) {
                $post = array(
                    'thumbnail' => $unique_temp_filename_thumb
                );
                $response = apiUploadThumb($post);
                if ($response->code == 201) {
                    $arrayjsonst = json_decode($response);
                    $thumbnailId = $arrayjsonst->thumbnailId;
                }
            }

            
                $tags = array();
            if (sizeof($videoTags) >= 1 && $videoTags[0] != "") {


                if (isset($videoTags)) {
                    foreach ($videoTags as $tag) {
                        if ($tag != "") {
                            array_push($tags, $tag);
                           
                        }
                    }
                }

//                $post['tag'] = $tags;
            }
            
            $post = array(
                "file" => $unique_temp_filename,
                "title" => $titlefile,
                "description" => $descriptionfile
//                'contentIdentifier' => $contentIdentifier
            );
            if ($thumbnailId != "") {
                $post['thumbnailId'] = $thumbnailId;
            }

//            var_dump($videoTags);
        

            $response = apiUpload($post,$tags,$contentIdentifier);
            $arrayjsonst = json_decode($response);


            if (isset($arrayjsonst->boxId)) {
                echo '<div class="updated"><p><strong>';
                _e("Upload successful", "wimtvpro");
                $handle = opendir($directory);
                while (($file = readdir($handle)) !== false) {
                    @unlink($directory . "/" . $file);
                }
                closedir($handle);
                echo '</strong></p></div>';
                $status = 'OWNED|' . $_FILES['videoFile']['name'];
//                  $src = "http://52.19.105.240:8080/wimtv-server/asset/thumbnail/".$arrayjsonst->thumbnailId;
//                $url_thumbs = '<img src="' . $src . '"  title="' . $title . '" class="wimtv-thumbnail" />';
                $url_thumbs = '<img src="' . '/wp-content/plugins/wimtvpro/images/empty.jpg"' . '"  title="' . $title . '" class="wimtv-thumbnail" />';

                dbInsertVideo(get_option("wp_userwimtv"), $arrayjsonst->contentId, "", $arrayjsonst->status, $url_thumbs, $arrayjsonst->boxId, "", $arrayjsonst->title, "", "", "", $arrayjsonst->source, $arrayjsonst->vodCount);
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

    case "uploadThumb":

        /**
         * Esegue l'upload di una thumbnail su wim.tv.
         */
        $urn = $_POST['urn'];

        $maxFileSize = 300000;
        $sizefile = filesize($_FILES['fileThumb']['tmp_name']);

        $urlfile = @$_FILES['fileThumb']['tmp_name'];

        // Get reference to uri: "wp-content/uploads/videotmp"
        $uploads_info = wp_upload_dir();
        $directory = $uploads_info["basedir"] . "/videotmp";

        if (!is_dir($directory)) {
            $directory_create = mkdir($uploads_info["basedir"] . "/videotmp");
        }

        $unique_temp_filename_full = "";
        $unique_temp_filename = "";
        if ($urlfile != "") {
            $ext = pathinfo($_FILES['fileThumb']['name'], PATHINFO_EXTENSION);

            $unique_temp_filename = time() . '.' . preg_replace('/.*?\//', '', $ext);
            $unique_temp_filename_full = $directory . "/" . $unique_temp_filename;
            $unique_temp_filename_full = str_replace("\\", "/", $unique_temp_filename_full);

            if (@move_uploaded_file($urlfile, $unique_temp_filename_full)) {
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

        if ($sizefile > $maxFileSize) {
            @unlink($unique_temp_filename);
        }

        if ((strlen(trim($urlfile)) > 0) && ($error == 0)) {
            //UPLOAD THUMB TO WIMTV
            set_time_limit(0);
            // NS: WE HAVE TO SPECIFY BOTH LOCAL FILENAME AND DESTINATION FILENAME
//      NS2016      $post = array("file" => $unique_temp_filename_full . ";filename=" . $unique_temp_filename,
//                "itemId" => $urn,
//            );

            $post = array(
                'thumbnail' => $unique_temp_filename_full
            );
            $response = apiUploadThumb($post);
            $arrayjsonst = json_decode($response);

//            $boxId = dbSelectVideosByContentId();

            if ($response->code == 201) {
                $videos = dbGetVideo($urn);
                $title = (isset($videos[0]) && $videos[0]->title != null) ? $videos[0]->title : "";
                $video_get = apiGetWimboxItem($videos[0]->boxId);
                $arrayjson_video = json_decode($video_get);

                $params = array(
                    'thumbnailId' => $arrayjsonst->thumbnailId,
                    'description' => $arrayjson_video->description,
                    'tags' => $arrayjson_video->tags,
//                    'thumbnailId' => '17d63fa7-0017-41e0-83f7-726810814e62',
                    'title' => $title
                );


                $return = json_decode(apiUpdateWimboxItem($videos[0]->boxId, $params));

                // NEWLY ADDED THUMB HAS BEEN CORRECTLY STORED: INSERT THE NEW THUMB-URL TO LOCAL DB
                $newThumbHTML = '<img src="' . $return->thumbnailId . '"  title="' . $title . '" class="wimtv-thumbnail" />';
                dbUpdateVideoThumb($urn, $newThumbHTML);
                dbUpdateVideoThumbnailId($urn, $arrayjsonst->thumbnailId);
            }

//            if (isset($arrayjsonst->stored)) {
//
//                if ($arrayjsonst->stored == true) {
//                    $videos = dbGetVideo($urn);
//                    $title = (isset($videos[0]) && $videos[0]->title != null) ? $videos[0]->title : "";
//                    // NEWLY ADDED THUMB HAS BEEN CORRECTLY STORED: INSERT THE NEW THUMB-URL TO LOCAL DB
//                    $newThumbHTML = '<img src="' . $arrayjsonst->url . '"  title="' . $title . '" class="wimtv-thumbnail" />';
//                    dbUpdateVideoThumb($urn, $newThumbHTML);
//                }
//            }

            echo $response->code;
        }
        break;

    case "resetThumb":
        /**
         * Esegue il reset della thumbnail di un video su wim.tv.
         */
        $urn = $_POST['urn'];
        $islive = $_POST['islive'];
//        var_dump($_POST);die;
        $response = apiDeleteThumb($urn);
        if ($islive != "true") {
            // WIMBOX: UPDATE STANDARD THUMB FIELD IN LOCAL DB
            $arrayjsonst = json_decode($response);
            $videos = dbGetVideo($urn);
            $title = (isset($videos[0]) && $videos[0]->title != null) ? $videos[0]->title : "";
            $stdThumbHTML = '<img src="' . $arrayjsonst->defaultUrl . '"  title="' . $title . '" class="wimtv-thumbnail" />';
            dbUpdateVideoThumb($urn, $stdThumbHTML);
        }
        // RETURN RESPONSE
        echo $response;
        break;

    default:
        //echo "Non entro";
        die();
}