<?php
/**
 * Written by walter at 24/10/13
 */
function wimtvpro_getThumbs_playlist($list,$showtime=FALSE, $private=TRUE, $insert_into_page=FALSE, $type_public="",$playlist=FALSE) {
	$replace_content = get_option("wp_replaceContentWimtv");
	$my_media= "";
	$videoList = explode (",",$list);

 	$array_videos  = dbGetUserVideosIn(get_option("wp_userWimtv"), $videoList, $showtime, $playlist);
	$array_videos_new_drupal = array();

	if ($playlist==TRUE) {

		for ($i=0;$i<count($videoList);$i++){
			foreach ($array_videos  as $record_new) {
				if ($videoList[$i] == $record_new->contentidentifier){
					array_push($array_videos_new_drupal, $record_new);	
				}
			}
		}
	} else {
		$array_videos_new_drupal = $array_videos;
	}

	//Select Showtime
	$details_st  = apiGetShowtimes();
	$arrayjson_st = json_decode( $details_st);
	$st_license = array();
	foreach ($arrayjson_st->items as $st){
		$st_license[$st->showtimeIdentifier] = $st->licenseType;
	}
	$position_new=1;
	//Select video with position
	if (count($array_videos_new_drupal )>0) {
		foreach ($array_videos_new_drupal  as $record_new) {
			if ($showtime) {
				if ((isset($st_license[$record_new->showtimeIdentifier])) && ($st_license[$record_new->showtimeIdentifier] !="PAYPERVIEW"))
					$my_media .= wimtvpro_listThumbs($record_new, $position_new, $replace_content, $showtime, $private, $insert_into_page,$st_license,TRUE);
			}
			else {
				$my_media .= wimtvpro_listThumbs($record_new, $position_new, $replace_content, $showtime, $private, $insert_into_page,$st_license,TRUE);
			}
		}
	}

	return $my_media;
}
?>