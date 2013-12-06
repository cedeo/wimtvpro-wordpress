<?php
/**
 * Written by walter at 30/10/13
 */
function writeGraph($from_dmy, $to_dmy, $dateNumber, $dateTraffic) {
    $dateRange = getDateRange($from_dmy, $to_dmy);
    $count_date = count($dateRange);
    $count_single= 0;
    $traffic_single = 0;
    if (count($dateNumber)>0) {
        $number_view_max = max($dateNumber);
        $single_percent = (100/$number_view_max);
    }
    else
        $single_percent = 0;
    $single_traffic_media = array();
    foreach ($dateTraffic as $dateFormat => $traffic_number){
        $single_traffic_media[$dateFormat] = round(array_sum($dateTraffic[$dateFormat]) / count($dateTraffic[$dateFormat]),2);
    }
    if (count($single_traffic_media)>0) {
        $traffic_view_max = max($single_traffic_media);
		if ($traffic_view_max!=0)
        	$single_traffic_percent = (100/$traffic_view_max);
    	else
			$single_traffic_percent = 0;
	}
    else {
        $traffic_view_max = 0;
    }


    echo "<div id='view_graph' class='view'>";

    echo "<div class='cols'>";


    echo "<div class='col'><div class='date'>" . __("Date","wimtvpro") . "</div><div class='title'>" . __("Total viewers","wimtvpro") . "</div><div class='title'>" . __("Average Traffic","wimtvpro") . "</div></div>";
    for ($i=0;$i<$count_date;$i++){
        if (isset($dateNumber[$dateRange[$i]]))
            $count_single = $single_percent * $dateNumber[$dateRange[$i]];
        if (isset($single_traffic_media[$dateRange[$i]]))
            $traffic_single = $single_traffic_percent * $single_traffic_media[$dateRange[$i]];

        echo "<div class='col' >
                <div class='date'>" . $dateRange[$i] . "</div>
                <div class='countview'><div class='bar' style='width:" . $count_single . "%'>";
        if ($dateNumber[$dateRange[$i]]>1)
            echo $dateNumber[$dateRange[$i]] . " " . __("viewers");
        if ($dateNumber[$dateRange[$i]]==1)
            echo $dateNumber[$dateRange[$i]] . "  " . __("viewer");
        echo "</div></div>
                <div class='countview'><div class='barTraffic' style='width:" . $traffic_single . "%'>";
        if ($single_traffic_media[$dateRange[$i]]>0)
            echo $single_traffic_media[$dateRange[$i]] . " MB";
        echo "</div></div>
                </div>";
        $count_single = 0;
        $traffic_single = 0;
    }

    echo "</div>";
    echo "<div class='clear'></div>
          </div>
          <div class='clear'></div>
          </div>";
}

function serializeStatistics($arrayStreams) {
    $streams = array();
    $megabyte = 1024*1024;
    foreach ($arrayStreams as $index=>$stream) {
        $arrayPlay = dbGetVideo($stream->contentId);
        $thumbs = "";
        if (count($arrayPlay)) {
            $thumbs = $arrayPlay[0]->urlThumbs;
            $thumbs = str_replace('\"','',$thumbs);
        }
        if ((isset($stream->title)))
            $stream->thumb = $thumbs . "<br/><b>" . $stream->title . "</b><br/>" . $stream->type ;
        else
            $stream->thumb = $thumbs . "<br/>" . $stream->id;

        $stream->views_list = array();

        foreach ($stream->views_expanded as $value){
            $value->traffic =  round($value->traffic / $megabyte, 2) . " MB";
            $value->date_human =  date('d/m/Y', ($value->end_time/1000));

            array_push($stream->views_list, $value);

        }
        $streams[$index] = $stream;
    }
    return $streams;
}