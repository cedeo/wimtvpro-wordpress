<?php

/**
 * Questa funzione, presente in tutti i plugin più o meno alla stessa maniera, si occupa di ritornare la tabella dei video presenti in WimBox e WimVod.
 * E' ancora abbastanza caotica, andrebbe rifattorizzata dividendo il template dall'elaborazione dei dati, in quanto la maniera in cui viene generata ora la tabella,
 * ovvero appendendo stringhe a result e ritornando alla fine l'unione di tanti pezzi di tabella sotto forma di stringhe.
 */
function wimtvpro_listThumbs($record_new, $position_new, $replace_content, $showtime, $private, $insert_into_page, $stLicense, $playlist) {
//    var_dump($record_new, $position_new, $replace_content, $showtime, $private, $insert_into_page,$stLicense,$playlist);
    global $user, $wpdb;

    $form = "";
    $my_media = "";
    $content_item_new = $record_new->contentidentifier;
    $boxId = $record_new->boxId;
    $thumbnailId = $record_new->thumbnailId;
    $state = $record_new->state;
    $source = $record_new->source;
    $vodCount = $record_new->vodCount;
    $vodId = $record_new->showtimeIdentifier;
//  NS2016 dovrebbe essere questo  $state = $record_new->source;
    $position = $record_new->position;
    $status_array = explode("|", $record_new->status);
    $duration = "";
    $urlThumbs = $record_new->urlThumbs;
    $urlPlay = $record_new->urlPlay;
//    $acquider_id = $record_new->acquiredIdentifier;
    $file_name = explode("|", $record_new->mytimestamp);
    $view_video_state = $record_new->viewVideoModule;
    $title = $record_new->title;
    $showtime_identifier = $record_new->showtimeIdentifier;
    $stateView = explode("|", $view_video_state);
    $array = isset($stateView[1]) ? explode(",", $stateView[1]) : array();
    $typeUser["U"] = array();
    $typeUser["R"] = array();
    $viewPublicVideo = FALSE;
// NS2016   $status = $status_array[0];
    $status = $record_new->status;

    //NS: Pending videos
    $status_pending = (isset($status_array[1])) ? $status_array[1] : false;

    if ($status_pending) {
//        $videothumb = "<img src='' class='wimtv-thumbnail'/>";
        $videothumb = $urlThumbs;
        $title = (($record_new->title) != "") ? $record_new->title : $status_pending;
        $my_media = "<tr class='disabledItem' id='" . $boxId . "'>";
        $my_media .= "<td class='image' colspan='6' ><span class='wimtv-thumbnail' >" . $videothumb . "</span><br/>$title <br/>" . __('This video has not yet been processed, wait a few minutes and try to synchronize', "wimtvpro");
        $my_media .= "</tr>";
        return $my_media;
    }

    $title = stripslashes($title);

    foreach ($array as $key => $value) {
        $var = explode("-", $value);

        if ($var[0] == "U") {
            array_push($typeUser["U"], $var[1]);
        } elseif ($var[0] == "R") {
            array_push($typeUser["R"], $var[1]);
        } else
            $typeUser[$var[0]] = "";

        if (($var[0] == "All") || ($var[0] == "")) {

            $viewPublicVideo = TRUE;
        }
    }

    $user = wp_get_current_user();

    $idUser = $user->ID;
    $userRole = $user->roles[0];
    //Video is visible only a user

    if ((!$private && $viewPublicVideo) ||
            (($userRole == "administrator") ||
            (in_array($idUser, $typeUser["U"])) ||
            (in_array($userRole, $typeUser["R"])) ||
            (array_key_exists("All", $typeUser)) ||
            (array_key_exists("", $typeUser)))) {
        // NS2016:
//         $replace_video = apiGetThumbsVideo($content_item_new);
//         var_dump("THUMMM",$record_new);exit;
//$replace_video = apiGetThumb($thumbnailId);
        $replace_video = $thumbnailId;
        $licenseType = "";
        if ($showtime_identifier != "") {
            $licenseType = isset($stLicense[$showtime_identifier]) ? $stLicense[$showtime_identifier] : "";
        }
    }


    $isfound = false;
//var_dump($replace_video);exit;
    if ((!isset($replace_video)) || (!strstr($replace_video, 'Not Found')) || ($replace_video == "")) {
        $isfound = true;
    }

    $licenze_video = "";

    if ($isfound != "") {
        // NS:

        if (($status != "PENDING")) {
//        $replace_video = '<img src="' . __("API_URL","wimtvpro").'asset/thumbnail/' . $thumbnailId . '" title="' . $title . '" class="" />';

            $replace_video = stripslashes($record_new->urlThumbs);
        } else if ($status == "PENDING") {

//            $videothumb = "<img src='' class='wimtv-thumbnail' title='" . $title."' />"; 
            $videothumb = $urlThumbs;
            $replace_video = __("Transcoding Video", "wimtvpro");
            $replace_video .= $videothumb;

//        $replace_video = stripslashes($record_new->urlThumbs);
        }
        if ($licenseType != "") {
            $licenze_video = '<div class="icon_licence ' . $licenseType . '"></div>';
        }
    }

    $wimtvpro_url = "";
    //For Admin
    if ($isfound) {
//        $video = "<span class='wimtv-thumbnail' >" . $replace_video . "</span>";
        $video = "<span class='wimtv-thumbnail' >" . $licenze_video . $replace_video . "</span>";
    } else {
        $video = $replace_video;
        $replace_video = false;
    }

    if ($replace_video) {

//        $form_st = '
//		<div class="free">' . __("FREE OF CHARGE", "wimtvpro") . '</div>
//		
//		<div class="cc">' . __("CREATIVE COMMONS", "wimtvpro") . '</div>
//                <div class="cbundle">' . "CONTENT BUNDLE" . '</div>
//	';

        $form_st = '
		<div class="free">' . __("FREE OF CHARGE", "wimtvpro") . '</div>
		
		<div class="cc">' . __("CREATIVE COMMONS", "wimtvpro") . '</div>
	';

//      NS2016  if (get_option("wp_activePayment") == "true")
//            $form_st .= '<div class="ppv">' . __("PAY PER VIEW", "wimtvpro") . '</div>';
//        else
//            $form_st .= '<div class="ppvNoActive">' . __("PAY PER VIEW", "wimtvpro") . '</div>';
        $profile_user = apiGetProfile();
        $json_user = json_decode($profile_user);
        $paypal_email = $json_user->finance->paypalEmail;

        if (isset($paypal_email) || $paypal_email != "") {

            $form_st .= '<div class="ppv">' . __("PAY PER VIEW", "wimtvpro") . '</div>';
        } else {
            $form_st .= '<div class="ppvNoActive">' . __("PAY PER VIEW", "wimtvpro") . '</div>';
        }
//NS2016

        if (!$insert_into_page) {
            if ($showtime_identifier != "") {
                $my_media .= "<tr class='streams' id='" . $boxId . "'>";
            } else {
                $my_media .= "<tr id='" . $boxId . "'>";
            }
        } else {
            $my_media .= "<tr>";
        }

        $form = "";
        //if ($private)
        //$action .= "<div class='thumb ui-state-default'>";
        //else 
        //$action .= "<div class='thumbPublic'>";

        if ($private) {

//        NS:
//	$response = apiGetDetailsVideo($content_item_new);
//	$arrayjson   = json_decode($response);
//	
//        var_dump($response);
//        print "<hr/>";
//        var_dump($arrayjson);
//        exit;
            $action = "";
            if ((!$showtime) || (trim($showtime) == "FALSE")) {
//            if ((!$showtime) || (trim($showtime) == "FALSE")) {
                //NS: thumb
                $action .="<td>" . getEditThumbnailControl($content_item_new) . "</td>";

                $id = "";
                $title_add = __("Add to WimVod", "wimtvpro");
                $title_remove = __("Remove from WimVod", "wimtvpro");

//                if ($state != "") {
                if ($source != "") {
                    //The video is into My Streaming
                    $id = "id='" . $vodId . "'";

//                    if ($state == "ACQUIRED") {
                    if ($source == "MARKET_PLACE") {
                        $class_r = "AcqRemoveshowtime";
                        $class_a = "AcquPutshowtime";
                    } else {
                        $class_r = "Removeshowtime";
                        $class_a = "Putshowtime";
                    }

                    if ($user->roles[0] == "administrator") {
                        if ($vodCount == 0) {
//                    NS2016    $action .= "<td><span title='" . $title_remove . "' class='icon_" . $class_r . "' " . $id . "></span>";
//                        $action .= "<span title='" . $title_add . "' class='add icon_" . $class_a . "' " . $id . " style='display:none;'></span></td>";
//                     $action .= "<td><span title='" . $title_remove . "' class='icon_" . $class_r . "' " . $id . "style='display:none;'></span>";
//                        $action .= "<span title='" . $title_add . "' class='add icon_" . $class_a . "' " . $id . " ></span></td>";

                            $action .= "<td class='icon'><span title='" . $title_remove . "' class='icon_" . $class_r . "' " . $id . " style='display:none;'></span>";
                            if (($status != "PENDING" && $status != "FAILED")) {
                                $action .= "<span title='" . $title_add . "' class='add icon_" . $class_a . "' " . $id . " ></span>";
                            }
                            $action .= "<div class='formVideo'>" . $form_st . "</div></td>";
                        } else {
                            $action .= "<td class='icon'><span title='" . $title_remove . "' class='icon_" . $class_r . "' " . $id . " ></span>";
                            $action .= "<span title='" . $title_add . "' class='add icon_" . $class_a . "' " . $id . " style='display:none;'></span>";
                        }
                    }
                } else {
                    //The video isn't into showtime	
                    $id = "id='" . $acquider_id . "'";
//                    if ($status == "ACQUIRED") { NS2016 levare acquiredid
                    if ($source == "MARKET_PLACE") {
                        $class_r = "AcqRemoveshowtime";
                        $class_a = "AcquPutshowtime";
                    } elseif ($source == "UPLOAD") {
                        //NS: Pending videos
//                        if (!$status_pending) {
                        $class_r = "Removeshowtime";
                        $class_a = "Putshowtime";
//                        } 
                    } else {
                        $class_a = "";
                        $class_r = "";
                    }

                    if ($class_a != "") {

                        if ($user->roles[0] == "administrator") {
                            if ($vodCount == 0) {
                                $action .= "<td><span title='" . $title_remove . "' class='icon_" . $class_r . "' " . $id . "></span>";
                                $action .= "<span title='" . $title_add . "' class='add icon_" . $class_a . "' " . $id . " style='display:none;'></span></td>";
                            } else {
// NS2016                            $action .= "<td class='icon'><span title='" . $title_remove . "' class='icon_" . $class_r . "' " . $id . " style='display:none;'></span>";
//                            $action .= "<span title='" . $title_add . "' class='add icon_" . $class_a . "' " . $id . " ></span>";
//                            $action .= "<div class='formVideo'>" . $form_st . "</div></td>";
                            }
                        }
                    }
                }
            } else {

                if ($user->roles[0] == "administrator") {
                    // NS: Added "translation" in title, i.e.: __("Remove from WimVod", "wimtvpro")
                    $action .= "<td class='icon'><span class='icon_RemoveshowtimeInto' title='" . __("Remove from WimVod", "wimtvpro") . "' id='" . $showtime_identifier . "'></span></td>";
                    $action .= "<td><span class='icon_moveThumbs' title='" . __("Drag", "wimtvpro") . "'></span></td>";
// NS: HIDE PRIVACY
//                    $action .= "<td><span class='icon_viewVideo' rel='" . $view_video_state . "' title='Video Privacy'></span></td>";
//                      $details_video = apiGetDetailsShowtime($showtime_identifier);
//                    $json_details = json_decode($details_video);
//
//                    $licenseType = $json_details->licenseType;
//       


                    $licenseType = $record_new->licenseType;
                    if ($licenseType == "PAY_PER_VIEW") {
                        $action .= "<td>" .$licenseType. '<img id="icon_play" src="' . site_url() . '/wp-content/plugins/wimtvpro/images/pay.png" style="margin: 4px 4px 0px;" />' . $record_new->price_per_view . "€ </td>";
                    } else if ($licenseType == "FREE") {
                        $action .= "<td>" .$licenseType. '<img id="icon_play" src="' . site_url() . '/wp-content/plugins/wimtvpro/images/free.png" style="margin: 4px 4px 0px;" />' . " </td>";
                    } else if ($licenseType == "CREATIVE_COMMONS") {
                        $action .= "<td>" .$licenseType.'<img id="icon_play" src="' . site_url() . '/wp-content/plugins/wimtvpro/images/cc.png" style="margin: 4px 4px 0px;" />' . " </td>";
                    }

                    $action .= "<td><textarea style='resize: none; width:90%;height:30%; readonly='readonly' onclick='this.focus(); this.select();'>[streamingWimtv  id='" . $showtime_identifier . "' width='" . get_option("wp_widthPreview") . "' height='" . get_option("wp_heightPreview") . "']</textarea></td>";

                    /* if ($licenseType!="PAYPERVIEW") $action  .= "<td><span class='icon_playlist' rel='" . $showtime_identifier . "' title='Add to Playlist selected'></span></td>"; */
                } else if (current_user_can("editor")) {
//                    $action .= "<td><textarea style='resize: none; width:90%;height:30%; readonly='readonly' onclick='this.focus(); this.select();'>[streamingWimtv  id='" . $showtime_identifier . "' width='" . get_option("wp_widthPreview") . "' height='" . get_option("wp_heightPreview") . "']</textarea></td>";
                    $licenseType = $record_new->licenseType;
                    if ($licenseType == "PAY_PER_VIEW") {
                        $action .= "<td>" . $licenseType.'<img id="icon_play" src="' . site_url() . '/wp-content/plugins/wimtvpro/images/pay.png"  style="margin: 4px 4px 0px;"/>' . $record_new->price_per_view . "€ </td>";
                    } else if ($licenseType == "FREE") {
                        $action .= "<td>" . $licenseType.'<img id="icon_play" src="' . site_url() . '/wp-content/plugins/wimtvpro/images/pay.png" style="margin: 4px 4px 0px;"/>' . " </td>";
                    } else if ($licenseType == "CREATIVE_COMMONS") {
                        $action .= "<td>" . $licenseType.'<img id="icon_play" src="' . site_url() . '/wp-content/plugins/wimtvpro/images/cc.png" style="margin: 4px 4px 0px;"/>' . " </td>";
                    }
                    $action .= "<td><textarea style='resize: none; width:90%;height:30%; readonly='readonly' onclick='this.focus(); this.select();'>[streamingWimtv id='" . $showtime_identifier . "' width='" . get_option("wp_widthPreview") . "' height='" . get_option("wp_heightPreview") . "']</textarea></td>";
                }
            }
            if ($isfound && ($status != "PENDING" && $status != "FAILED") && (!$showtime)) {
                $urlVideo = wimtvpro_checkCleanUrl("functions", "download.php?host_id=" . $content_item_new);
                //$urlVideo = downloadVideo($content_item_new,$status_array[0]);
//                $action .= "<td><a class='icon_download' href='" . $urlVideo . "' title='Download'></a></td>";
                $action .= "<td><span class='icon_download_Nactive' title='Download'></span></td>";
            } else if (!$showtime) {
                $action .= "<td><span class='icon_downloadNone' title='Download'></span></td>";
            }

            if ($vodId != "") {
                $style_view = "";
                if (($private) && ($licenseType == "PAYPERVIEW"))
                    $href_view = wimtvpro_checkCleanUrl("embedded", "embedded.php?c=" . $content_item_new . "&s=" . $vodid);
                else
                    $href_view = wimtvpro_checkCleanUrl("embedded", "embedded.php?c=" . $content_item_new . "&s=" . $vodId);

                $title_view = __("View Video", "wimtvpro");
                $play = TRUE;
            }
            else {
                $style_view = "";
                if ($urlPlay != "") {

                    // NS: possible vug about size of preview player. Conversely player is OK in case of "View Video" (6 lines up)
                    $href_view = wimtvpro_checkCleanUrl("embedded", "embedded.php?c=" . $content_item_new . "&s=" . $boxId . "&box=true");
                    $play = TRUE;
                } else {
                    $href_view = wimtvpro_checkCleanUrl("embedded", "embedded.php?c=" . $content_item_new . "&s=" . $boxId . "&box=true");
                    $play = FALSE;
//                    $href_view = wimtvpro_checkCleanUrl("embedded", "embedded.php?c=" . $content_item_new . "&s=" . $boxId."&box=true");
//            $action = "<td style='display:none;'><a class='viewThumb' " . $style_view . " title='" . $title_view . "' href='#' id='" . $href_view . "'><span class='icon_view'></span></a></td>";
                }
                $title_view = __("Preview Video", "wimtvpro");
            }
            $linkView = "";

            if ($play == TRUE && ($status != "PENDING" && $status != "FAILED")) {

//                $action .= "<td><a class='viewThumb' " . $style_view . " title='" . $title_view . "' href='#' id='" . $href_view . "'><span class='icon_view'></span></a></td>";
                $action .= "<td><a class='viewThumb' " . $style_view .  "' href='#' id='" . $href_view . "'><span class='icon_view'></span></a></td>";
                if ($playlist)
//                    $linkView = "<a class='viewThumb' " . $style_view . " title='" . $title_view . "' href='#' id='" . $href_view . "'><span class='icon_view'></span></a>";
                    $linkView = "<a class='viewThumb' " . $style_view .  "' href='#' id='" . $href_view . "'><span class='icon_view'></span></a>";
            } else if (($status != "PENDING" && $status != "FAILED")) {
//                $action .= "<td><a class='viewThumb' " . $style_view . " title='" . $title_view . "' href='#' id='" . $href_view . "'><span class='icon_view'></span></a></td>";
                $action .= "<td><a class='viewThumb' " . $style_view .  "' href='#' id='" . $href_view . "'><span class='icon_view'></span></a></td>";
            } else
                $action .= "<td></td>";
//         var_dump($showtime);

            if (($status != "PENDING" && $status != "FAILED") && (!$showtime)) {
//                var_dump("QUAAAA");die;
                $action .= "<td><span title='" . __("Remove") . "' class='icon_remove' " . $id . " ></span></td>";
                $href_edit = wimtvpro_checkCleanUrl("embedded", "embedded_video_edit.php?b=" . $boxId);
                $action .= "<td><a class='editVideoMetadati' id='" . $href_edit . "' href='#' > <span title='" . __("Edit") . "' class='icon_edit'  ></span></a></td>";
            } else if ((!$showtime) && $status == "FAILED") {
                $action .= "<td><span title='" . __("Remove") . "' class='icon_remove' " . $id . " ></span></td>";
            } else if ((!$showtime)) {
                $action .= "<td></td>";
                $href_edit = wimtvpro_checkCleanUrl("embedded", "embedded_video_edit.php?b=" . $boxId);
//                $action .= "<td><span title='" . __("Remove") . "' class='icon_remove' " . $id . " ></span></td>";
                   $action .= "<td><a class='editVideoMetadati' id='" . $href_edit . "' href='#' > <span title='" . __("Edit") . "' class='icon_edit'  ></span></a></td>";
            }



//            var_dump("CIAO");die;
            $action .= $form . "<div class='loader'></div></div>";
        } else {

            $style_view = "";
            $title_view = "";

            $href_view = wimtvpro_checkCleanUrl("embedded", "embedded.php?c=" . $content_item_new . "&s=" . $showtime_identifier);
            $action = "<td style='display:none;'><a class='viewThumb' " . $style_view . " title='" . $title_view . "' href='#' id='" . $href_view . "'><span class='icon_view'></span></a></td>";
        }

        if ($playlist) {
            $action = "";
        }
//        $my_media .= "<td class='image'>" . $licenze_video . $video . "<br/>";
        $my_media .= "<td class='image'>" . $video . "<br/>";

        if ($private) {
            $my_media .="<b><span id='wimtvpro-title-detail'>" . $title . "</span></b>";

            if ($showtime) {
//             $my_media .="</br><b><span id='wimtvpro-title-detail'>" .  . "</span></b>";
            }
        }
// NS2016 if($showtime_identifier == ""){
//     $href_view = wimtvpro_checkCleanUrl("embedded", "embedded.php?c=" . $content_item_new . "&s=" . $boxId."&box=true");
//            $action = "<td style='display:none;'><a class='viewThumb' " . $style_view . " title='" . $title_view . "' href='#' id='" . $href_view . "'><span class='icon_view'></span></a></td>";
//}
        $my_media .= $linkView . "</td>" . $action;
        $send = "";

        if ($insert_into_page) {
            $my_media .= '<td>';

            $my_media .= '<input type="hidden" value="' . $_GET['post_id'] . '" name="post_id">';

            // NS: COMMENT THE FOLLOWING LINES AND UNCOMMENT THE NEXT ONE TO HIDE WIDTH AND HIGHT VIDEO SETTINGS    
            $my_media .= "W <input maxweight='3' class='w insert-media_W' type='text' value='" . get_option("wp_widthPreview") . "'>px  <br/>" .
                    "  H <input maxweight='3' class='h insert-media_H' type='text' value='" . get_option("wp_heightPreview") . "'>px<br/></span>";

//            $my_media .="<input style='display: none;' maxweight='3' class='w insert-media_W' type='text' value='" . get_option("wp_widthPreview") . "'>px  <br/>" .
//                    "<input style='display: none;' maxweight='3' class='h insert-media_H' type='text' value='" . get_option("wp_heightPreview") . "'>px<br/></span>";
//            

            $send = get_submit_button(__('Insert into Post', "wimtvpro"), 'buttonInsert', $content_item_new, false);
        }

        $my_media .= $send . "</td></tr>";

        $position_new = $position;
    }

    return $my_media;
}

?>
