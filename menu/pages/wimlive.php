<?php
/**
 * Written by walter at 24/10/13
 */
include_once('modules/wimlive.php');


function wimtvpro_live() {
    $view_page = wimtvpro_alert_reg();
    if (!$view_page){
        die();
    }

    $response = apiGetProfile();
    $dati = json_decode($response, true);
    $enabledLive = $dati["liveStreamEnabled"];

    if (strtoupper($enabledLive) != "TRUE") {
        echo "<div class='error'>" . __("To use WimLive, you need to enable live streaming in Live Configuration in your Settings","wimtvpro") .
            " <a href='admin.php?page=WimTvPro&update=2'>Live streaming</a></div>";
        die();
    }

    $page = isset($_GET['namefunction']) ? $_GET['namefunction'] : "";
    $noneElenco = FALSE;
    switch ($page) {

        case "addLive":

            $noneElenco = TRUE;
            //aggiungere script per pickdata e pickhour
            if (isset($_POST["wimtvpro_live"])) {
                wimtvpro_savelive("insert");
            }
            $name = "";
            $payperview = "0";
            $url = "";
            $giorno = "";
            $ora = "00:00";
            $durata = "00h00";
            $timezone = "";

            break;
        case "modifyLive":

            $noneElenco = TRUE;

            if (isset($_POST["wimtvpro_live"])) {
                wimtvpro_savelive("modify");
            }

            $dati = apiGetLive($_GET['id'], $_GET['timezone']);

            $arraydati = json_decode($dati);
            $name = $arraydati->name;
            if ($arraydati->paymentMode=="FREEOFCHARGE")
                $payperview = "0";
            else
                $payperview =  $arraydati->pricePerView;
            $url = $arraydati->url;
            $giorno = $arraydati->eventDate;
            $timezone = $arraydati->timeZone;
            $data = $arraydati->eventDateMillisec;
            $timezoneOffset = intval($arraydati->timezoneOffset);
            $timestamp = intval($data)/1000;
            $start = new DateTime("@$timestamp");
            $timezoneName = timezone_name_from_abbr("", $timezoneOffset, false);
            $real_timezone = new DateTimeZone($timezoneName);
            $start->setTimezone($real_timezone);
            $ora = $start->format('H') . ":" . $start->format('i');
            $tempo = $arraydati->duration;
            $ore = floor($tempo / 60);
            $minuti = $tempo % 60;

            $durata = $ore . "h";
            if ($minuti<10)
                $durata .= "0";
            $durata .= $minuti;


            break;

        case "deleteLive":
            $response = apiDeleteLive($_GET['id']);
            break;

        default:
            break;
    }

    ?>
    <script type="text/javascript">
		function clickImg(obj){
			jQuery("a.clickWebProducer img").attr("src", "<?php echo get_option('wp_wimtvPluginPath') . 'images/webcam.png' ?>");
			jQuery(obj).attr("src", "<?php echo get_option('wp_wimtvPluginPath') . 'images/live_rec.gif' ?>");

		}
	</script>
    <?php
        if (!$noneElenco) {
            global $post_type_object;
            $screen = get_current_screen();
    ?>
        <div class='wrap'>
        <h2>WimLive
        <a href='<?php echo $_SERVER['REQUEST_URI'] . "&namefunction=addLive" ?>' class='add-new-h2'><?php echo __( 'New','wimtvpro' ) ?></a>
        </h2>
        <p>
            <?php echo _e("Here you can create and post live streaming events to your website.","wimtvpro") ?>
            <br/>
            <?php echo _e("This service can be used in one of these two modalities:","wimtvpro") ?>
            <ol>
                <li>
                <?php echo _e("Install a third party video encoding software (e.g. Adobe Flash Media Live Encoder, Wirecast etc.) on your pc: this solution is recommended if you want to connect an external video camera to your pc","wimtvpro") ?>
                </li>
                <li>
                <?php echo _e('Use WimTV encoding software. Broadcast directly from your webcam, by simply clicking the icon "Live now". By clicking “Live now” icon, the producer will open in a new browser tab. Keep it open during the whole transmission.',"wimtvpro") ?>
                </li>
            </ol>
        </p>
        <p>Shortcode: <b>[wimlive]</b></p>
        <span><strong>* <?php _e("Time is shown according to timezone of your device", "wimtvpro") ?></strong></span>
        <table id='tableLive' class='wp-list-table widefat fixed pages'>
        <thead>
        <tr>
            <th><?php echo __("Title") ?></th>
            <th>Live Now</th>
            <th>Pay-Per-View</th>
            <th>URL</th>
            <th>* <?php echo __("Schedule") ?></th>
            <th><?php echo __("Embed Code","wimtvpro") ?></th>
            <th><?php echo __("Tools") ?></th>
        </tr>
        </thead>
        <tbody>
            <?php wimtvpro_elencoLive("table", "all") ?>
        </tbody>
        </table>
        </div>
    <?php } else { ?>
        <script type="text/javascript">
              jQuery(document).ready(function(){
                var timezone = -(new Date().getTimezoneOffset())*60*1000;
                jQuery("#timelivejs").val(timezone);
              });
              jQuery(document).ready( function () {
                  jQuery( ".pickatime" ).timepicker({  defaultTime:"00:00"  });
              });
              jQuery(document).ready( function () {
                  jQuery( ".pickaduration" ).timepicker(
                      {defaultTime: "00h05",
                       showPeriodLabels: false,
                       timeSeparator: "h"
                      });
              });

              jQuery(document).ready( function () {
                  jQuery( ".pickadate" ).datepicker(
                      {dateFormat: "dd/mm/yy",
                       autoSize: true,
                       minDate: 0
                      });
              });

              jQuery(".edit-eventTimeZone[value=\"<?php echo $timezone ?>\"]").attr("selected", "selected");

        </script>
        <div class='wrap'><h2>WimLive
        <a href='<?php echo $_SERVER['REQUEST_URI'] . "&namefunction=listLive" ?>' class='add-new-h2'><?php echo __( 'Return to list', 'wimtvpro') ?></a>
        </h2>
        <p>
        <?php echo _e("Please complete all the fields marked with an *","wimtvpro") ?>
        <form action="#" method="post" id="wimtvpro-wimlive-form" accept-charset="UTF-8">

            <p><label for="edit-name"><?php _e("Title"); ?> <span>*</span></label>
                <input type="text" id="edit-name" name="name" value="<?php echo $name;?>" size="100" maxlength="200"></p>
            <div class="description"><?php _e("Please insert the title of the live event","wimtvpro"); ?>*</div>

            <p><label for="edit-payperview"><?php _e("Set the price to access the event","wimtvpro"); ?> *</label>
                <input type="text" id="edit-payperview" name="payperview" value="<?php echo $payperview;?>" size="10" maxlength="5" class="form-text required"></p>
            <div class="description">
                <?php _e("Please, set a price for viewing your event (set 0 for free of charge). Prices are expressed in &euro;","wimtvpro"); ?></div>

            <p><label for="edit-url">Url *</label>
                <input type="text" id="edit-url" name="Url" value="<?php echo $url;?>" size="100" maxlength="800" class="form-text required">
            </p>


            <div class="description"><p id="urlcreate"><?php _e('You need the streaming server URL. Click “Obtain URL" button to get one',"wimtvpro"); ?>
                    <b class="button createUrl"><?php _e("Obtain URL","wimtvpro"); ?></b></p>
                <b id="'<?php echo get_option("wp_userWimtv");?>'" class="removeUrl"><?php _e("Remove");?> Url</b>
                <div class="passwordUrlLive">
                    <?php _e("Password Live is missing, insert a password for live streaming:","wimtvpro"); ?>
                    <input type="password" id="passwordLive" value="" />
                    <b class="button  createPass"><?php _e("Save");?></b>
                </div>
            </div>

            <p>
                <label for="edit-url"><?php _e("Event status","wimtvpro"); ?> * </label><br/>
                <?php _e("Public","wimtvpro"); ?> <input type="radio" name="Public" value="true" checked="checked"/> |
                <?php _e("Private","wimtvpro"); ?> <input type="radio" name="Public" value="false"/>
                <div class="description">
                    <?php
                    sprintf(_e('If you want to index your event on %d, and in WimView app, select "Public"','wimtvpro'),'<a target="_blank" href="http://wimlive.wim.tv">wimlive.wim.tv</a>')
                    ?>
                </div>
            </p>

            <p>
                <label for="edit-record"><?php _e("Record event","wimtvpro"); ?></label><br/>
                <?php _e("Yes");?> <input type="radio" name="Record" value="true" checked="checked"/> |
                <?php _e("No","wimtvpro");?> <input type="radio" name="Record" value="false"/>
                <div class="description"><?php _e("Select “Yes” if you want to record your event. The recorded video will be listed among your videos in WimBox","wimtvpro"); ?></div>

            </p>
            <?php
                $currentTimeZone =ini_get('date.timezone');
            ?>
            <p><label for="edit-giorno"><?php _e("Start date","wimtvpro");?> <?php _e("dd/mm/yy","wimtvpro");?> *</label>
                <input  type="text" class="pickadate" id="edit-giorno" name="Giorno" value="<?php echo $giorno;?>" size="10" maxlength="10"></p>

            <p><label for="edit-ora"><?php _e("Start time","wimtvpro");?> *</label>
                <input class="pickatime" type="text" id="edit-ora" name="Ora" value="<?php echo $ora;?>" size="10" maxlength="10">
                <label for="edit-eventTimeZone"><?php _e("Time zone","wimtvpro");?></label>
                <select id="edit-eventTimeZone" name="eventTimeZone">

                    <option value="">----------------------------------</option>
                    <option value="Kwajalein">(GMT -12:00) Eniwetok, Kwajalein</option>
                    <option value="Pacific/Pago_Pago">(GMT -11:00) Midway Island, Samoa</option>
                    <option value="US/Hawaii">(GMT -10:00) Hawaii</option>
                    <option value="US/Alaska">(GMT -9:00) Alaska</option>
                    <option value="America/Los_Angeles">(GMT -8:00) Pacific Time (US &amp; Canada)</option>
                    <option value="America/Denver">(GMT -7:00) Mountain Time (US &amp; Canada)</option>
                    <option value="America/Chicago">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
                    <option value="America/New_York">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
                    <option value="America/Halifax">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
                    <option value="Canada/Newfoundlan">(GMT -3:30) Newfoundland</option>
                    <option value="America/Sao_Paulo">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
                    <option value="Atlantic/South_Georgia">(GMT -2:00) Mid-Atlantic</option>
                    <option value="Atlantic/Cape_Verde">(GMT -1:00 hour) Azores, Cape Verde Islands</option>
                    <option value="Europe/London">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
                    <option value="Europe/Rome">(GMT +1:00 hour) Rome, Madrid, Paris, Copenhagen</option>
                    <option value="Europe/Istanbul">(GMT +2:00) Helsinki, Istanbul, Kaliningrad, South Africa</option>
                    <option value="Europe/Moscow">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
                    <option value="Asia/Tehran">(GMT +3:30) Tehran</option>
                    <option value="Asia/Dubai">(GMT +4:00) Abu Dhabi, Dubai, Muscat, Baku, Tbilisi</option>
                    <option value="Asia/Kabul">(GMT +4:30) Kabul</option>
                    <option value="Indian/Maldives">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
                    <option value="Asia/Calcutta">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
                    <option value="Asia/Katmandu">(GMT +5:45) Kathmandu</option>
                    <option value="Asia/Dacca">(GMT +6:00) Almaty, Dhaka, Colombo</option>
                    <option value="Asia/Bangkok">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
                    <option value="Asia/Hong_Kong">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
                    <option value="Asia/Tokyo">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
                    <option value="Australia/Adelaide">(GMT +9:30) Adelaide, Darwin</option>
                    <option value="Australia/Sydney">(GMT +10:00) Sydney, Melbourne, Brisbane, Vladivostok</option>
                    <option value="Asia/Magadan">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
                    <option value="Australia/Auckland">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
                </select>
            </p>



            <p><label for="edit-duration"><?php _e("Duration","wimtvpro");?> *</label>
                <input class="pickaduration" type="text" id="edit-duration" name="Duration" value="<?php echo $durata;?>" size="10" maxlength="10">
            </p>
            <input type="hidden" name="wimtvpro_live" value="Y" />
            <input type="hidden" id="timelivejs" name="timelivejs" value="" />
            <?php submit_button(__("Create","wimtvpro")); ?>

        </form>
        </div>

<?php
    }
}
?>