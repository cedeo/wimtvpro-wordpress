<?php
function wimtvpro_detail_showtime($single, $st_id) {
  if (!$single) {
	$array_detail = apiGetShowtimes();
  }
  else {
    
	$array_detail = apiGetDetailsShowtime($st_id);

  }

  return $array_detail;
}

?>