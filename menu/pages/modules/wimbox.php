<?php
/**
 * Written by walter at 24/10/13
 */

function wimtvpro_getVideos($showtime=FALSE, $private=TRUE, $insert_into_page=FALSE, $type_public="", $sql_where="", $sql_order="") {
    global $user, $wpdb, $wp_query;

    $replace_content = get_option("wp_replaceContentWimtv");

    $table_name = $wpdb->prefix . 'wimtvpro_video';
    $my_media= "";
    $response_st = "";
    if (($showtime) && ($showtime=="TRUE")) $sql_where .= " AND state='showtime'";
    if (!$private) {
        if ($type_public == "block") {
            $sql_where .= " AND ((viewVideoModule like '1%') OR (viewVideoModule like '3%')) ";
        }
        if ($type_public == "page") {
            $sql_where .= " AND ((viewVideoModule like '2%') OR (viewVideoModule like '3%')) ";
        }
    }

    $resultCount = $wpdb->get_results("SELECT count(*) as count FROM " . $table_name  . " WHERE uid='" . get_option("wp_userwimtv") . "' " . $sql_where);
    $array_count  = $resultCount[0]->count;

    $rows_per_page = 10 ;
    $current_page = isset($_GET['paged']) ? $_GET['paged'] : "";
    $current = (intval($current_page)) ? intval($current_page) : 1;
    $number_page = ceil($array_count/$rows_per_page);
    $offset = ( $current  * $rows_per_page ) - $rows_per_page;
    $sqllimit = "  LIMIT ${offset}, ${rows_per_page}" ;

    $array_videos_new_wp = $wpdb->get_results("SELECT * FROM {$table_name} WHERE uid='" . get_option("wp_userwimtv") . "' " . $sql_where . " ORDER BY Title ASC" . $sqllimit);

    /* $array_videos_new_wp0 = $wpdb->get_results("SELECT * FROM  {$table_name} WHERE uid='" . get_option("wp_userwimtv") . "' AND  position=0 " . $sql_where . " ORDER BY " . $sql_order);*/

    //Select Showtime

    $details_st  = apiGetShowtimes();
    $arrayjSonST = json_decode($details_st);
    $stLicense = array();
    foreach ($arrayjSonST->items as $st){
        $stLicense[$st->showtimeIdentifier] = $st->licenseType;
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