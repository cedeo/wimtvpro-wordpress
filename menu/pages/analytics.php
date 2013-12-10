<?php
/**
 * Written by walter at 24/10/13
 */
$user = trim(get_option("wp_userWimtv"));

include_once("modules/graph.php");

function wimtvpro_report (){

    global $user;
    $view_page = wimtvpro_alert_reg();
    $megabyte = 1024*1024;


    if (!$view_page){
        die();
    }

    $from = isset($_POST['from']) ? $_POST['from'] : "";
    $to = isset($_POST['to']) ? $_POST['to'] : "";

    $dateNumber = array();
    $dateTraffic = array();

    if (($from!="") && ($to!="")) {
        list($day_from, $month_from, $year_from) = explode('/',$from);
        list($day_to, $month_to, $year_to) = explode('/',$to);

        $from_tm = mktime (0, 0, 0, $month_from , $day_from, $year_from)*1000;
        $to_tm = mktime (0, 0, 0,  $month_to , $day_to, $year_to)*1000;


        $from_dmy =$month_from . "/" . $day_from . "/" . $year_from;
        $to_dmy= $month_to . "/" . $day_to . "/" . $year_to;

        $title_streams = __("Streams","wimtvpro") . " (" . __("From","wimtvpro") . " " . $from . " " . __("To","wimtvpro") . " "  . $to . ")";
        $title_user = "<a href='?page=WimTVPro_Report'>" . __("Current month","wimtvpro") . "</a> " . __("Change Date","wimtvpro");
        $style_date = "";
        $user_response = analyticsGetUser($from_tm, $to_tm);
        $traffic_json = json_decode($user_response);
        $traffic = $traffic_json->traffic;
        $storage = $traffic_json->storage;

        $packet = analyticsGetPacket();
        $commercialPacket_json = json_decode($packet);
        $currentPacket = $commercialPacket_json->current_packet;
        if (($currentPacket->id)>0)
            $namePacket =  $currentPacket->name;
        else
            $namePacket =  $currentPacket->error;
        $byteToMb = "<b>" . round($traffic/ $megabyte, 2) . ' MB</b>';
        $byteToMbS = "<b>" . round($storage/ $megabyte, 2) . ' MB</b>';


    } else {
        $from_dmy = date("m") . "/01/" . date("y");

        $dayMe=cal_days_in_month(CAL_GREGORIAN, date("m"), date("y"));
        $to_dmy = date("m") . "/" . $dayMe . "/" . date("y");
        $from_tm = "";
        $to_tm = "";

        $title_streams =  __("Streams","wimtvpro") . " (" . __("Current month","wimtvpro") . ")";
        $title_user = __("Current month","wimtvpro")  . " <a href='#' id='customReport'>" . __("Change Date","wimtvpro") . "</a> ";
        $style_date = "display:none;";

        $user_response = analyticsGetUser();
        $traffic_json = json_decode($user_response);
        $traffic = $traffic_json->traffic;

        $packet = analyticsGetPacket();
        $commercialPacket_json = json_decode($packet);
        $currentPacket = $commercialPacket_json->current_packet;
        if (($currentPacket->id)>0)
            $namePacket =  $currentPacket->name;
        else
            $namePacket =  $currentPacket->error;

        $traffic_of = " of " . $currentPacket->band_human;
        $storage_of = " of " . $currentPacket->storage_human;

        $traffic_bar = "<div class='progress'><div class='bar' style='width:" . $commercialPacket_json->traffic->percent . "%'>" . $commercialPacket_json->traffic->percent_human . "%</div></div>";
        $storage_bar = "<div class='progress'><div class='bar' style='width:" . $commercialPacket_json->storage->percent . "%'>" . $commercialPacket_json->storage->percent_human . "%</div></div>";

        $byteToMb = "<b>" . $commercialPacket_json->traffic->current_human . '</b>' . $traffic_of . $traffic_bar;
        $byteToMbS = "<b>" . $commercialPacket_json->storage->current_human . '</b>' . $storage_of . $storage_bar;

    }

    $response = analyticsGetStreams($from_tm, $to_tm);
    $arrayStreams = json_decode($response);

    $streams = serializeStatistics($arrayStreams);

    foreach($streams as $stream) {
        foreach ($stream->views_expanded as $value) {
            if (isset($dateNumber[$value->date_human]))
                $dateNumber[$value->date_human] = $dateNumber[$value->date_human] + 1;
            else
                $dateNumber[$value->date_human] = 1;

            if (isset($dateTraffic[$value->date_human]))
                array_push($dateTraffic[$value->date_human], $value->traffic);
            else
                $dateTraffic[$value->date_human] = array($value->traffic);
        }
    }

?>


    <script type="text/javascript">
        jQuery(document).ready(function(){
          jQuery( ".pickadate" ).datepicker({
              dateFormat: "dd/mm/yy",
              maxDate: 0});
          jQuery("#customReport").click(function(){
            jQuery("#fr_custom_date").fadeToggle();
            jQuery("#changeTitle").html("<a href=\'?page=WimTVPro_Report\'><?php echo __("Current month","wimtvpro") ?></a><?php echo __("Change Date","wimtvpro") ?>")});

          jQuery(".tabs span").click(function(){
            var idSpan = jQuery(this).attr("id");
            jQuery(".view").fadeOut();
            jQuery("#view_" + idSpan).fadeIn();
            jQuery(".tabs span").attr("class","");
            jQuery(this).attr("class","active");
          });

        });
    </script>
    
    <div class='wrap'>
    <?php  echo  wimtvpro_link_help();?>
    <h2>Report user Wimtv <?php echo $user ?></h2>
    <h3 id='changeTitle'><?php echo $title_user ?></h3>

    <div class="registration" id="fr_custom_date" style="<?php echo $style_date ?>">
        <form method="post">
            <fieldset>
                <span><?php echo  __("From","wimtvpro") ?></span>
                <input  type="text" class="pickadate" id="edit-from" name="from" value="<?php echo $from ?>" size="10" maxlength="10" />
                <span><?php echo  __("To","wimtvpro") ?></span>
                <input  type="text" class="pickadate" id="edit-to" name="to" value="<?php echo $to ?>" size="10" maxlength="10" />
                <input type="submit" value=">" class="button button-primary" />
            </fieldset>
        </form>
    </div>
    <p><?php echo __("You commercial packet","wimtvpro") ?>:
        <b><?php echo $namePacket ?></b> - <a href='?page=WimTvPro&pack=1&return=WimTVPro_Report'><?php echo __("Change","wimtvpro") ?></a>
    </p>
    <?php if ($traffic == "") { ?>
        <p><?php echo __("You did not generate any traffic in this period","wimtvpro") ?></p>
    <?php } else { ?>
        <p><?php echo __("Traffic","wimtvpro") . ": " . $byteToMb ?></p>
        <p><?php echo __("Storage space","wimtvpro") . ": " . $byteToMbS ?></p>
        <div class="summary"><div class="tabs">
            <span id="stream" class="active"><?php echo  __("Streams","wimtvpro") ?></span>
            <span id="graph"><?php _e("Chart","wimtvpro") ?></span>
        </div>
        <div id="view_stream" class="view">
            <h3><?php echo $title_streams; ?></h3>
            <table class="wp-list-table widefat fixed posts" style="text-align:center;">
              <tr>
                <th class="manage-column column-title">Video</th>
                <th class="manage-column column-title"><?php echo  __("Viewers","wimtvpro") ?></th>
                <th class="manage-column column-title"><?php echo  __("Activate Viewers","wimtvpro") ?></th>
                <th class="manage-column column-title"><?php echo  __("Max viewers","wimtvpro") ?></th>
              </tr>
              <?php foreach($streams as $stream) { ?>
                <tr class='alternate'>
                    <td class='image'><?php echo $stream->thumb ?></td>
                    <td>
                        <b><?php echo  __("Total","wimtvpro") . ": " . $stream->views . " " . __("Viewers","wimtvpro") ?></b>
                        <div class="wp-list-table">
                            <table class='wp-list-table'>
                                <tr>
                                    <th class='manage-column column-title' style='font-size:10px;'><?php echo __("Date","wimtvpro") ?></th>
                                    <th class='manage-column column-title' style='font-size:10px;'><?php echo __("Duration","wimtvpro") ?></th>
                                    <th class='manage-column column-title' style='font-size:10px;'><?php echo __("Traffic","wimtvpro") ?></th>
                                </tr>
                                <?php foreach($stream->views_list as $value) { ?>
                                    <tr>
                                       <td style='font-size:10px;'><?php echo $value->date_human ?></td>
                                       <td style='font-size:10px;'><?php echo $value->duration ?>s</td>
                                       <td style='font-size:10px;'><?php echo $value->traffic  ?></td>
                                    </tr>
                                <?php } ?>
                            </table>
                           </div>
                    </td>
                    <td><?php echo $stream->viewers ?></td>
                    <td><?php echo $stream->max_viewers ?></td>
                </tr>

              <?php
                }
              ?>
            </table>
            <div class='clear'>
            </div>
        </div>


<?php
        writeGraph($from_dmy, $to_dmy, $dateNumber, $dateTraffic);
    }
    echo "</div>";
}
?>