<?php
/**
 * Written by walter at 31/10/13
 */
function settings_prices() {

    echo "<div class='wrap'>";
	echo  wimtvpro_link_help();
    echo "<h2>" . __("Pricing","wimtvpro");
    if (isset($_GET['return']))  echo "<a href='?page=WimTVPro_Report' class='add-new-h2'>" . __("Back") . "</a>";
    echo "</h2>";
    $credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
    $uploads_info = wp_upload_dir();
    $directoryCookie = $uploads_info["basedir"] .  "/cookieWim";
    if (!is_dir($directoryCookie)) {
        $directory_create = mkdir($uploads_info["basedir"] . "/cookieWim");
    }

    if (isset($_GET['upgrade'])){

        $fileCookie = "cookies_" . get_option("wp_userWimtv") . "_" . $_GET['upgrade'] . ".txt";

        if (!is_file($directoryCookie. "/" . $fileCookie)) {
            $f = fopen($directoryCookie. "/" . $fileCookie,"w");
            fwrite($f,"");
            fclose($f);
        }
        //Update Packet
        $data = array("name" => $_GET['upgrade']);
        $data_string = json_encode($data);

        // chiama
        $my_page = admin_url() . "?page=WimTvPro&pack=1&success=" . $_GET['upgrade'];
        if (isset($_GET['return'])) $my_page .= "&return=true";

        $redirect_url = urlencode ($my_page);
        $cookiejar = $directoryCookie . "/" . $fileCookie;
        $result = apiUpgradePacket($redirect_url, $cookiejar, $data_string);

        $arrayjsonst = json_decode($result);

        if ($arrayjsonst->result=="REDIRECT") {
            echo "
                     <script>
                          jQuery(document).ready(function() {
                            jQuery.colorbox({
                                onLoad: function() {
                                    jQuery('#cboxClose').remove();
                                },
                                html:'<h2>" . $arrayjsonst->message . "</h2><h2><a href=\"" . $arrayjsonst->successUrl . "\">Yes</a> | <a onClick=\"jQuery(this).colorbox.close();\" href=\"#\">" . __("No") . "</a></h2>'
                            })
                         });
                     </script>
                      ";

        }  else {
            //d($arrayjsonst);
        }
    }

    if (isset($_GET['success'])) {

        //controlla stato pagamento
        $fileCookie = "cookies_" . get_option("wp_userWimtv") . "_" . $_GET['success'] . ".txt";
        $cookie = $directoryCookie . "/" . $fileCookie;
        $result = apiCheckPayment($cookie);
        $arrayjsonst = json_decode($result);

    }

    if (!isset($_GET['return'])){
        $view_page = wimtvpro_alert_reg();
        $submenu = wimtvpro_submenu($view_page);

        echo str_replace("packet","current",$submenu) ;
    }

    $response = apiGetPacket();
    $packet_user_json = json_decode($response);
    $id_packet_user = $packet_user_json->id;
    $count_date = $packet_user_json->daysLeft;

    $response2 = apiCommercialPacket();
    $packet_json = json_decode($response2);

    ?>
    <div class='empty'></div>
    <h4><?php echo __("Use of WimTV requires subscription to a monthly storage and bandwidth package","wimtvpro") ?></h4>

    <table class='wp-list-table widefat fixed pages'>
        <thead>
            <tr>
                <th></th>
                <?php foreach ($packet_json -> items as $a) {
                          echo "<th><b>" . $a->name . "</b></th>";

                      }
                ?>
            </tr>
        </thead>
        <tbody>
            <tr class='alternate'>
                <td><?php echo __("Bandwidth","wimtvpro") ?></td>
                <?php foreach ($packet_json -> items as $a) {
                          echo "<td>" . $a->band . " GB</td>";
                      }
                ?>
            </tr>
            <tr>
                <td><?php echo __("Storage","wimtvpro") ?></td>
                <?php foreach ($packet_json -> items as $a) {
                          echo "<td>" . $a->storage . " GB</td>";
                      }
                ?>
            </tr>
            <tr class='alternate'>
                <td><?php echo __("Support","wimtvpro") ?></td>
                <?php foreach ($packet_json -> items as $a) {
                          echo "<td>" . $a->support . "</td>";
                      }
                ?>
            </tr>
            <tr>
                <td><?php echo __("Hours of Transmission","wimtvpro") ?>(*)</td>
                <?php foreach ($packet_json -> items as $a) {
                          echo "<td>" . $a->streamingAmount . "</td>";
                      }
                ?>
            </tr>
            <tr>
                <td><?php printf(__( 'Price/mo. for %d Mo', 'wimtvpro' ), "1" ) ?> (**)</td>
                <?php foreach ($packet_json -> items as $a) {
                          echo "<td>" . number_format($a->price,2) . " &euro; / " . __("m","wimtvpro") . "</td>";
                      }
                ?>
            </tr>
            <tr class='alternate'>
                <td></td>
                <?php foreach ($packet_json -> items as $a) {
                    //echo "<td>" . $a->dayDuration . " - " . $a->id . "</td>";
                    echo "<td>";
                    if ($id_packet_user==$a->id) {

                        echo "<img  src='" . plugins_url('../../../images/check.png', __FILE__) . "' title='Checked'><br/>";
                        if ($a->name!="Free")
                            echo $count_date . " " . __("day left","wimtvpro");
                    }
                    else {
                        echo "<a href='?page=WimTvPro&pack=1";
                        if (isset($_GET['return'])) echo "&return=true";
                        echo "&upgrade=" . $a->name;
                        echo "'><img class='icon_upgrade' src='" . plugins_url('../../../images/uncheck.png', __FILE__) . "' title='Upgrade'>";
                        echo "</a>";
                    }
                    echo "</td>";
                } ?>
            </tr>
        </tbody>
    </table>
    <h4>(*) <?php echo __("Assuming video+audio encoded at 1 Mbps","wimtvpro") ?></h4>
    <h4>(**) <?php echo __("VAT to be added","wimtvpro") ?></h4>
    <p>
        <?php echo __("If, before the end of the month, you","wimtvpro") ?>
        <ol>
            <li><?php echo __("reach 80% level you will be notified","wimtvpro") ?></li>
            <li><?php echo __("exceed 100% level you will be asked to upgrade to another package.","wimtvpro")?></li>
        </ol>
    </p>
    <h3><?php echo __("Note that, if you stay within the usage limits of the Free Package, use of WimTV is free","wimtvpro") ?></h3>
    <h3><?php echo __("If you license content and/or provide services in WimTV, revenue sharing will apply","wimtvpro") ?></h3>
    <h3><?php echo __("Enjoy your WimTVPro video plugin!","wimtvpro") ?></h3>
 </div>
<?php
}
?>