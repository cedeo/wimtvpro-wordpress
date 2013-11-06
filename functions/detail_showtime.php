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

function wimtvpro_elencoLive($type, $identifier, $onlyActive=true){
    echo '
        <script type="text/javascript">

        jQuery(document).ready(function() {
    ';

	if ($type!="table")
		echo 'var url_pathPlugin ="' . plugin_dir_url(__FILE__) . '";';

    echo '
    var timezone = -(new Date().getTimezoneOffset())*60*1000;
	//window.location.assign(window.location + "&timezone="+timezone);

	jQuery.ajax({
			context: this,
			url:  url_pathPlugin + "pages/live.php", 		      
			type: "POST",
			dataType: "html",
			async: false,
			data: "type='. $type . '&timezone =" + timezone  + "&id=' . $identifier . '&onlyActive=' . $onlyActive . '",  
			success: function(response) {
';

	if ($type=="table") {
	
		echo 'jQuery("#tableLive tbody").html(response)';
	
	} else {
	
		echo 'jQuery(".live_' . $type . '").html(response)';
	
	}

echo '
			},
	});
});
</script>
';


}

?>