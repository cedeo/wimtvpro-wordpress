<?php
/**
 * Written by walter at 24/10/13
 */
 
function wimtvpro_getVideos($showtime=FALSE, $private=TRUE, $insert_into_page=FALSE, $type_public="", $sql_where="", $sql_order="") {
    global $user, $wpdb, $wp_query;

    $replace_content = get_option("wp_replaceContentWimtv");

    $my_media= "";
    $response_st = "";
    if (($showtime) && ($showtime=="TRUE")) {
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

    $resultCount = dbGetVideosCount(get_option("wp_userwimtv"), $showtime_only, $public);
    $array_count  = $resultCount[0]->count;

    $rows = 10;
    $current_page = isset($_GET['paged']) ? $_GET['paged'] : "";
    $current = (intval($current_page)) ? intval($current_page) : 1;
    $number_page = ceil($array_count/$rows);
    $offset = ( $current  * $rows ) - $rows;

    $array_videos_new_wp = dbGetUserVideos(get_option("wp_userwimtv"), $showtime_only, $public, $offset, $rows);

    $details_st  = apiGetShowtimes();
    $arrayjSonST = json_decode($details_st);
    $stLicense = array();
	if (isset($arrayjSonST)) {
		foreach ($arrayjSonST->items as $st){
			$stLicense[$st->showtimeIdentifier] = $st->licenseType;
		}
	}
    $position_new=1;
    //Con posizione
    if (count($array_videos_new_wp  )>0) {
        foreach ($array_videos_new_wp   as $record_new) {
            $my_media .= wimtvpro_listThumbs($record_new, $position_new, $replace_content, $showtime, $private, $insert_into_page,$stLicense,FALSE);
        }
    }
    //Position 0
    /* if (count($array_videos_new_wp0)>0) {
       foreach ($array_videos_new_wp0 as $record_new) {
         $my_media .= wimtvpro_listThumbs($record_new, $position_new, $replace_content, $showtime, $private, $insert_into_page,$stLicense);
       }
     }*/


    if ($number_page>1) {
        $pagination = '<div id="wp_pagination">';

        if ($current>1){
            $pagination = '<a class="first page button" href="'. get_pagenum_link().'">&laquo;</a>';
            $pagination .= '<a class="previous page button" href="'. get_pagenum_link(($current-1 > 0 ? $current-1 : 1)).'">&lsaquo;</a>';
        }
        for($i=1;$i<=$number_page;$i++)
            $pagination .= '<a class="'.($i == $current ? 'active ' : '').'page button" href="'.get_pagenum_link($i).'">'.$i.'</a>';

        if ($current<$number_page){
            $pagination .= '<a class="next page button" href="'.get_pagenum_link(($current+1 <= $number_page ? $current+1 : $number_page)).'">&rsaquo;</a>';
            $pagination .= '<a class="last page button" href="'.get_pagenum_link($number_page).'">&raquo;</a>';
        }

        $pagination .= '</div>';

    } else
        $pagination = "";

    return $my_media . $pagination;
}
?>