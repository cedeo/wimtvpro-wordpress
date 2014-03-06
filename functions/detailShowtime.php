<?php
/**
 * Questa funzione ritorna i video presenti in WimVod, se il parametro $single è false, altrimenti,
 * se presente il parametro $st_id ed esso corrisponde allo showtime_id di un video in WimVod, ritorna i dettagli del suddetto video.
 */
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