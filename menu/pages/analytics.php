<?php
/**
 * Written by walter at 24/10/13
 */
/**
 * Mostra la pagina delle statistiche nel menu laterale, la logica viene gestita nella prima parte, del codice,
 * il markup rimane sotto.
 * TODO: questa divisione tra markup e logica può essere migliorata prendendo spunto dai templates di Drupal.
 */
$user = trim(get_option("wp_userWimtv"));

include_once("modules/graph.php");

function wimtvpro_report() {

    




 $response = apiGetPacketProfile();
    $packet_user_json = json_decode($response);

     
    global $user;
    $view_page = wimtvpro_alert_reg();
    $megabyte = 1024 * 1024;


    if (!$view_page) {
        die();
    }

    $from = isset($_POST['from']) ? $_POST['from'] : "";
    $to = isset($_POST['to']) ? $_POST['to'] : "";

    $dateNumber = array();
    $dateTraffic = array();

    if (($from != "") && ($to != "")) {
        list($day_from, $month_from, $year_from) = explode('/', $from);
        list($day_to, $month_to, $year_to) = explode('/', $to);

        $from_tm = mktime(0, 0, 0, $month_from, $day_from, $year_from) * 1000;
        $to_tm = mktime(0, 0, 0, $month_to, $day_to, $year_to) * 1000;


        $from_dmy = $month_from . "/" . $day_from . "/" . $year_from;
        $to_dmy = $month_to . "/" . $day_to . "/" . $year_to;

        $title_streams = __("Streams", "wimtvpro") . " (" . __("From", "wimtvpro") . " " . $from . " " . __("To", "wimtvpro") . " " . $to . ")";
        $title_user = "<a href='?page=" . __('ANALYTICS_urlLink', "wimtvpro") . "'>" . __("Current month", "wimtvpro") . "</a> " . __("Change Date", "wimtvpro");
        $style_date = "";
        $user_response = analyticsGetUser($from_tm, $to_tm);
        $traffic_json = json_decode($user_response);
        $traffic = $traffic_json->traffic;
        $storage = $traffic_json->storage;

        $packet = analyticsGetPacket();
        $commercialPacket_json = json_decode($packet);
        $currentPacket = $commercialPacket_json->current_packet;
        if (($currentPacket->id) > 0)
            $namePacket = $currentPacket->name;
        else
            $namePacket = $currentPacket->error;
        $byteToMb = "<b>" . round($traffic / $megabyte, 2) . ' MB</b>';
        $byteToMbS = "<b>" . round($storage / $megabyte, 2) . ' MB</b>';
    } else {
        $from_dmy = date("m") . "/01/" . date("y");

        $dayMe = cal_days_in_month(CAL_GREGORIAN, date("m"), date("y"));
        $to_dmy = date("m") . "/" . $dayMe . "/" . date("y");
        $from_tm = "";
        $to_tm = "";

        $title_streams = __("Streams", "wimtvpro") . " (" . __("Current month", "wimtvpro") . ")";
        $title_user = __("Current month", "wimtvpro") . " <a href='#' id='customReport'>" . __("Change Date", "wimtvpro") . "</a> ";
        $style_date = "display:none;";

        $user_response = analyticsGetUser();
        $traffic_json = json_decode($user_response);
        $traffic = $traffic_json->traffic;

        $packet = analyticsGetPacket();
        $commercialPacket_json = json_decode($packet);
        $currentPacket = $commercialPacket_json->current_packet;
        if (($currentPacket->id) > 0)
            $namePacket = $currentPacket->name;
        else
            $namePacket = $currentPacket->error;
        
        
    $licenseName = $packet_user_json->licenseName;
   $free = array(
        
        'bandPercent' => 5,
        'storagePercent' => 1,
    );
    
    $entry = array(
       
        'bandPercent' => 30,
        'storagePercent' => 8,
    );
    $basic = array(

        'bandPercent' => 80,
        'storagePercent' => 20,
    );
 
    $professional = array(
  
        'bandPercent' => 250,
        'storagePercent' => 65,
    );
    
    $business = array(
 
        'bandPercent' => 800,
        'storagePercent' => 200,
    );
    
    $packet_json = array(
        'Free' => $free,
        'Entry' => $entry,
        'Basic' => $basic,
        'Professional' => $professional,
        "Business" => $business
    );
   
    $license = $packet_json[$licenseName];
    $str = str_replace('http://', 'http://cache.', $str);
        $band = str_replace(',',".",$packet_user_json->bandPercent);
        $storage =str_replace(',',".",$packet_user_json->storagePercent);
        $traffic_of = " of " . $license['bandPercent'] ." GB" ;
        $storage_of = " of " . $license['storagePercent'] . " GB";

        $traffic_bar = "<div class='progress'><div class='bar' style='width:" . $band . "%'>" . $band  . "%</div></div>";
        $storage_bar = "<div class='progress'><div class='bar' style='width:" . $storage . "%'>" . $storage . "%</div></div>";

        $byteToMb = "<b>" . $band . ' % </b>' . $traffic_of . $traffic_bar;
        $byteToMbS = "<b>" . $storage. ' % </b>' . $storage_of . $storage_bar;
        
     



 
    }

//    $response = analyticsGetStreams($from_tm, $to_tm);
//    $arrayStreams = json_decode($response);
//
//    $streams = serializeStatistics($arrayStreams);
//
//    foreach ($streams as $stream) {
//        foreach ($stream->views_expanded as $value) {
//            if (isset($dateNumber[$value->date_human]))
//                $dateNumber[$value->date_human] = $dateNumber[$value->date_human] + 1;
//            else
//                $dateNumber[$value->date_human] = 1;
//
//            if (isset($dateTraffic[$value->date_human]))
//                array_push($dateTraffic[$value->date_human], $value->traffic);
//            else
//                $dateTraffic[$value->date_human] = array($value->traffic);
//        }
//    }
    ?>


    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery(".pickadate").datepicker({
                dateFormat: "dd/mm/yy",
                maxDate: 0});
            jQuery("#customReport").click(function() {
                jQuery("#fr_custom_date").fadeToggle();
                jQuery("#changeTitle").html("<a href=\'?page=<?php _e('ANALYTICS_urlLink', "wimtvpro") ?>\'><?php echo __("Current month", "wimtvpro") ?></a><?php echo __("Change Date", "wimtvpro") ?>")
            });

            jQuery(".tabs span").click(function() {
                var idSpan = jQuery(this).attr("id");
                jQuery(".view").fadeOut();
                jQuery("#view_" + idSpan).fadeIn();
                jQuery(".tabs span").attr("class", "");
                jQuery(this).attr("class", "active");
            });

        });
    </script>

    <div class='wrap'>
        <?php echo wimtvpro_link_help(); ?>


        <div class="registration" id="fr_custom_date" style="<?php echo $style_date ?>">
            <form method="post">
                <fieldset>
                    <span><?php echo __("From", "wimtvpro") ?></span>
                    <input  type="text" class="pickadate" id="edit-from" name="from" value="<?php echo $from ?>" size="10" maxlength="10" />
                    <span><?php echo __("To", "wimtvpro") ?></span>
                    <input  type="text" class="pickadate" id="edit-to" name="to" value="<?php echo $to ?>" size="10" maxlength="10" />
                    <input type="submit" value=">" class="button button-primary" />
                </fieldset>
            </form>
        </div>
        <p><?php echo __("You commercial packet", "wimtvpro") ?>:
            <b><?php echo $namePacket ?></b> - <a href='?page=<?php _e('SETTINGS_urlLink', "wimtvpro") ?>&pack=1&return=<?php _e('ANALYTICS_urlLink', "wimtvpro") ?>'><?php echo __("Change", "wimtvpro") ?></a>
        </p>
        <?php //if ($traffic == "") { ?>
            <p><?php //echo __("You did not generate any traffic in this period", "wimtvpro") ?></p>
        <?php //} else { ?>
            <p><?php echo __("Traffic", "wimtvpro") . ": " . $byteToMb ?></p>
            <p><?php echo __("Storage space", "wimtvpro") . ": " . $byteToMbS ?></p>
        
            


                <?php
                writeGraph($from_dmy, $to_dmy, $dateNumber, $dateTraffic);
//            }
            echo "</div>";
        }
        ?>