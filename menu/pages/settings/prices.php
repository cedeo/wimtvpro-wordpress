<?php
/**
 * Written by walter at 31/10/13
 * Updated by Netsense s.r.l. 2016
 */

/**
 * Mostra la pagina dei prezzi nei settings, la logica viene gestita nella prima parte, del codice,
 * il markup rimane sotto.
 * TODO: questa divisione tra markup e logica può essere migliorata prendendo spunto dai templates di Drupal.
 */
function settings_prices() {
  

     $trackingId = null;
    echo "<div class='wrap'>";
    echo wimtvpro_link_help();
    echo "<h2>" . __("Pricing", "wimtvpro");
    if (isset($_GET['return']))
        echo "<a href='?page=" . $_GET['return'] . "' class='add-new-h2'>" . __("Back") . "</a>";
    echo "</h2>";
    $credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
    $uploads_info = wp_upload_dir();
    $directoryCookie = $uploads_info["basedir"] . "/cookieWim";
   
  
    if (!is_dir($directoryCookie)) {
        $directory_create = mkdir($uploads_info["basedir"] . "/cookieWim");
    }
//  var_dump("CIAOOOO");die;
    if(isset($_GET['success_url'])){
        $fileCookie = "cookies_" . get_option("wp_userWimtv") . "_" . $_GET['success_url'] . ".txt";
           $f = fopen($directoryCookie . "/" . $fileCookie, "r");
//              var_dump($f,$directoryCookie . "/" . $fileCookie, "r");die;
               $trackingId = fread($f,36);
             
               fclose($f);

           $params_upgrade = array(
                 'trackingId' => $trackingId
 
             );
//             $response = apiUpgradePacket($data['name'],$params_upgrade);
             $response = apiUpgradePacket($_GET['success_url'],$params_upgrade);
               $arrayjsonst = json_decode($response);
        if ($response->code == 200) {
        $my_page_cancel = admin_url() . "?page=" . __('SETTINGS_urlLink', "wimtvpro") . "&pack=1";
            echo "
                     <script>
                          jQuery(document).ready(function() {
                            jQuery.colorbox({width: '200px',
                            height:'100px',
                             onComplete: function() {
                             jQuery(this).colorbox.resize();            
                              },
                                onLoad: function() {
                                    jQuery('#cboxClose').remove();
                                },
                                html:'<h2>" . "Activated" . "</h2></br><h2><a href=\"" . $my_page_cancel . "\">OK</a> </h2>'
                            })
                         });
                     </script>
                      ";
        } else {
              echo "
                     <script>
                          jQuery(document).ready(function() {
                            jQuery.colorbox({
                            width: '200px',
                            height:'100px',
                             onComplete: function() {
                              jQuery(this).colorbox.resize();            
                             },
                                onLoad: function() {
                                    jQuery('#cboxClose').remove();
                                },
                                html:'<h2>" . $arrayjsonst->message . "</h2></br><h2> <a onClick=\"jQuery(this).colorbox.close();\" href=\"\">" ."OK" . "</a></h2>'
                            })
                         });
                     </script>
                      ";
        }
    }

    if (isset($_GET['upgrade'])) {

        $fileCookie = "cookies_" . get_option("wp_userWimtv") . "_" . $_GET['upgrade'] . ".txt";

        //Update Packet
        $data = array("name" => $_GET['upgrade']);


        // chiama
        $my_page = admin_url() . "?page=" . __('SETTINGS_urlLink', "wimtvpro") . "&pack=1&success_url=" . $_GET['upgrade'];
        $my_page_cancel = admin_url() . "?page=" . __('SETTINGS_urlLink', "wimtvpro") . "&pack=1";
        if (isset($_GET['return']))
            $my_page .= "&return=true";

        $redirect_url = urlencode($my_page);
        $cookiejar = $directoryCookie . "/" . $fileCookie;
        $response = "";
//       var_dump("Siamo QUIII",$data['name']);
        if($data['name'] == "Free"){
//             var_dump("Eccoci");
            $response = apiDowngradePacket();
//            var_dump("Eccoci here",$response);
            
        }else{
            
             $params_post= array(
            "embedded" => false,
            "mobile" => false,
            "returnUrl" => $my_page,
            "cancelUrl" => $my_page_cancel
        );
  
             $response = apiPayToUpgradePacket($data['name'], $params_post);
             $response_json = json_decode($response);
             $trackingId = $response_json->trackingId;
              $f = fopen($directoryCookie . "/" . $fileCookie, "w");
              fwrite($f, $trackingId);
               fclose($f);

           

//        if (!is_file($directoryCookie . "/" . $fileCookie)) {
//           
//            $f = fopen($directoryCookie . "/" . $fileCookie, "w");
//            fwrite($f, $trackingId);
//            fclose($f);
//        }
              echo "
                     <script>
                          jQuery(document).ready(function() {
                          
                             window.open('".$response_json->url."','_blank');
                         });
                     </script>
                      ";
          

//             $params_upgrade = array(
//                 'trackingId' => $response_json->trackingId
// 
//             );
//             $response = apiUpgradePacket($data['name'],$params_upgrade);
//            var_dump("PLAYYYY",$response);
//var_dump("UPGRADEEE",$response_json,$response->code);die;

    
        }
//            $arrayjsonst = json_decode($response);
//        if ($response->code == 200) {
//
//            echo "
//                     <script>
//                          jQuery(document).ready(function() {
//                            jQuery.colorbox({width: '200px',
//                            height:'100px',
//                             onComplete: function() {
//                             jQuery(this).colorbox.resize();            
//                              },
//                                onLoad: function() {
//                                    jQuery('#cboxClose').remove();
//                                },
//                                html:'<h2>" . "Activated" . "</h2></br><h2><a href=\"" . $my_page . "\">OK</a> </h2>'
//                            })
//                         });
//                     </script>
//                      ";
//        } else {
//              echo "
//                     <script>
//                          jQuery(document).ready(function() {
//                            jQuery.colorbox({
//                            width: '200px',
//                            height:'100px',
//                             onComplete: function() {
//                              jQuery(this).colorbox.resize();            
//                             },
//                                onLoad: function() {
//                                    jQuery('#cboxClose').remove();
//                                },
//                                html:'<h2>" . $arrayjsonst->message . "</h2></br><h2> <a onClick=\"jQuery(this).colorbox.close();\" href=\"#\">" ."OK" . "</a></h2>'
//                            })
//                         });
//                     </script>
//                      ";
//        }
    }


    if (!isset($_GET['return'])) {
        $view_page = wimtvpro_alert_reg();
        $submenu = wimtvpro_submenu($view_page);

        echo str_replace("packet", "current", $submenu);
    }


    $response = apiGetPacketProfile();
    $packet_user_json = json_decode($response);


    $licenseName = $packet_user_json->licenseName;
    $daysToExpiration = $packet_user_json->daysToExpiration;


    $free = array(
        'price' => 0,
        'stremingAmount' => 10,
        'licenseName' => 'Free',
        'bandPercent' => 5,
        'storagePercent' => 1,
    );
    
    $entry = array(
        'price' => 10,
        'streamingAmount' => 60,
        'licenseName' => 'Entry',
        'bandPercent' => 30,
        'storagePercent' => 8,
    );
    $basic = array(
        'price' => 20,
        'streamingAmount' => 160,
        'licenseName' => 'Basic',
        'bandPercent' => 80,
        'storagePercent' => 20,
    );
 
    $professional = array(
        'price' => 60,
        'streamingAmount' => 500,
        'licenseName' => 'Professional',
        'bandPercent' => 250,
        'storagePercent' => 65,
    );
    
    $business = array(
        'price' => 180,
        'streamingAmount' => 1600,
        'licenseName' => 'Business',
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
    
//    var_dump($packet_json);exit;
    ?>
    <div class='empty'></div>
    <h4><?php echo __("Use of WimTV requires subscription to a monthly storage and bandwidth package", "wimtvpro") ?></h4>

    <table class='wp-list-table widefat fixed pages'>
        <thead>
            <tr>
                <th></th>
                <?php
                foreach ($packet_json as $a) {
                    echo "<th><b>" . $a['licenseName'] . "</b></th>";
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <tr class='alternate'>
                <td><?php echo __("Bandwidth", "wimtvpro") ?></td>
                <?php
                foreach ($packet_json as $a) {
                    echo "<td>" . $a['bandPercent'] . " GB</td>";
                }
                ?>
            </tr>
            <tr>
                <td><?php echo __("Storage", "wimtvpro") ?></td>
                <?php
                foreach ($packet_json as $a) {
                    echo "<td>" . $a['storagePercent'] . " GB</td>";
                }
                ?>
            </tr>

            <tr>
                <td><?php echo __("Hours of Transmission", "wimtvpro") ?>(*)</td>
                <?php
                foreach ($packet_json as $a) {
                    echo "<td>" . $a['streamingAmount'] . "</td>";
                }
                ?>
            </tr>
            <tr>
                <td><?php printf(__('Price/mo. for %d Mo', 'wimtvpro'), "1") ?> (**)</td>
    <?php
    foreach ($packet_json as $a) {
        echo "<td>" . number_format($a['price'], 2) . " &euro; / " . __("m", "wimtvpro") . "</td>";
    }
    ?>
            </tr>
            <tr class='alternate'>
                <td></td>
                <?php
                foreach ($packet_json as $a) {
                    //echo "<td>" . $a->dayDuration . " - " . $a->id . "</td>";
                    echo "<td>";
                    if ($licenseName == $a['licenseName']) {

                        echo "<img  src='" . plugins_url('../../../images/check.png', __FILE__) . "' title='Checked'><br/>";
                        if ($a['licenseName'] != "Free")
                            echo $daysToExpiration . " " . __("day left", "wimtvpro");
                    }
                    else {
                        echo "<a href='?page=" . __('SETTINGS_urlLink', "wimtvpro") . "&pack=1";
                        if (isset($_GET['return']))
                            echo "&return=true";
                        echo "&upgrade=" . $a['licenseName'];
                        echo "'><img class='icon_upgrade' src='" . plugins_url('../../../images/uncheck.png', __FILE__) . "' title='Upgrade'>";
                        echo "</a>";
                    }
                    echo "</td>";
                }
                ?>
            </tr>
        </tbody>
    </table>
    <h4>(*) <?php echo __("Assuming video+audio encoded at 1 Mbps", "wimtvpro") ?></h4>
    <h4>(**) <?php echo __("VAT to be added", "wimtvpro") ?></h4>
    <p>
    <?php echo __("If, before the end of the month, you", "wimtvpro") ?>
    <ol>
        <li><?php echo __("reach 80% level you will be notified", "wimtvpro") ?></li>
        <li><?php echo __("exceed 100% level you will be asked to upgrade to another package.", "wimtvpro") ?></li>
    </ol>
    </p>
    <h3><?php echo __("Note that, if you stay within the usage limits of the Free Package, use of WimTV is free", "wimtvpro") ?></h3>
    <h3><?php echo __("If you license content and/or provide services in WimTV, revenue sharing will apply", "wimtvpro") ?></h3>
    <h3><?php echo __("Enjoy your WimTVPro video plugin!", "wimtvpro") ?></h3>
    </div>
    <?php
}
?>