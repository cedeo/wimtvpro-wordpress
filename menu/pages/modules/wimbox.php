<?php

/**
 * Written by walter at 24/10/13
 */

/**
 * Ritorna il markup completo della tabella video presente in WimBox.
 * Filtra in caso di ricerca ed ordina in caso di ordinamento.
 */
function wimtvpro_getVideos($showtime = FALSE, $private = TRUE, $insert_into_page = FALSE, $type_public = "", $sql_where = "", $sql_order = "") {
    global $user, $wpdb, $wp_query;

//    NS: CALL  smartSync FUNCTION ON "pending" VIDEOS TO CHECK WHETHER
//    THEY HAVE BEEN ALREADY TRANSCODED
    wimtvpro_smartSync::sync("pending");

    $replace_content = get_option("wp_replaceContentWimtv");

    $my_media = "";
    $response_st = "";
    if (($showtime) && ($showtime == "TRUE")) {
        $showtime_only = true;
    } else {
        $showtime_only = false;
    }
    $public = 0;
    if (!$private) {
        if ($type_public == "block") {
            $public = 1;
        }
        if ($type_public == "page") {
            $public = 2;
        }
    }

    $resultCount = dbGetVideosCount(get_option("wp_userwimtv"), $showtime_only, $public, $sql_where);
    $array_count = $resultCount[0]->count;

//    $rows = 10;
    $rows = (isset($_GET['rowsperpage'])) ? $_GET['rowsperpage'] : 10;
    $current_page = isset($_GET['paged']) ? $_GET['paged'] : "";
    $current = (intval($current_page)) ? intval($current_page) : 1;
    $number_page = ceil($array_count / $rows);
    $offset = ( $current * $rows ) - $rows;


    // NS: RETRIEVE DATA FROM WordPress DB
    $array_videos_new_wp = dbGetUserVideos(get_option("wp_userwimtv"), $showtime_only, $public, $offset, $rows, $sql_where, $sql_order);
    $details_st = apiGetShowtimes();
    $arrayjSonST = json_decode($details_st);
    $stLicense = array();
    if (isset($arrayjSonST)) {
        foreach ($arrayjSonST->items as $st) {
            $stLicense[$st->showtimeIdentifier] = $st->licenseType;
        }
    }
    $position_new = 1;

//    NS:
    //Con posizione
    if (count($array_videos_new_wp) > 0) {
        foreach ($array_videos_new_wp as $record_new) {
//            var_dump($record_new->status);
            $my_media .= wimtvpro_listThumbs($record_new, $position_new, $replace_content, $showtime, $private, $insert_into_page, $stLicense, FALSE);
        }
    }
    //Position 0
    /* if (count($array_videos_new_wp0)>0) {
      foreach ($array_videos_new_wp0 as $record_new) {
      $my_media .= wimtvpro_listThumbs($record_new, $position_new, $replace_content, $showtime, $private, $insert_into_page,$stLicense);
      }
      } */


    if ($number_page > 1) {
        $pagination = '<div id="wp_pagination">';

        if ($current > 1) {
            $pagination = '<a class="first page button" href="' . get_pagenum_link() . '">&laquo;</a>';
            $pagination .= '<a class="previous page button" href="' . get_pagenum_link(($current - 1 > 0 ? $current - 1 : 1)) . '">&lsaquo;</a>';
        }
        for ($i = 1; $i <= $number_page; $i++)
            $pagination .= '<a class="' . ($i == $current ? 'active ' : '') . 'page button" href="' . get_pagenum_link($i) . '">' . $i . '</a>';

        if ($current < $number_page) {
            $pagination .= '<a class="next page button" href="' . get_pagenum_link(($current + 1 <= $number_page ? $current + 1 : $number_page)) . '">&rsaquo;</a>';
            $pagination .= '<a class="last page button" href="' . get_pagenum_link($number_page) . '">&raquo;</a>';
        }


        //Videos per page BLOCK
        $rowsperpage_values = array(
            '5' => '5',
            '10' => '10',
            '20' => '20',
        );
        // FORM OBJECT
        $rowsperpageSelector = "<form name='rowsperpageForm' id='rowsperpageForm' method='GET'>";
        foreach ($_GET as $key => $value) {
            $rowsperpageSelector .= "<input type='hidden' name='$key' value='$value'/>";
        }
        $rowsperpageSelector .="</form>";
        // SELECT LABEL OBJECT
        $rowsperpageSelector .="<label for='rowsperpage' style='display: inline; margin-left: 100px;'>" . __("Videos per page", "wimtvpro") . "</label>";
        // SELECT OBJECT
        $rowsperpageSelector .="<select name='rowsperpage' onChange='document.getElementById(\"rowsperpageForm\").submit();' form='rowsperpageForm'>";
        foreach ($rowsperpage_values as $opt_value => $opt_label) {
            $selected = ($opt_value == $rows) ? " selected='selected'" : "";
            $rowsperpageSelector.="<option value='" . $opt_value . "'" . $selected . ">" . $opt_label . "</option>";
        }
        $rowsperpageSelector.="</select>";

        $pagination .= $rowsperpageSelector;
        $pagination .= '</div>';
    } else {
        $pagination = "";
    }

    $my_media.="<div>" . __("Found", "wimtvpro") . ": " . $array_count . " videos";
    return $my_media . $pagination;
}

?>
