<?php
/**
 * Written by walter at 31/10/13
 */

/**
 * Mostra la pagina delle impostazioni dei live nei settings, la logica viene gestita nella prima parte, del codice,
 * il markup rimane sotto.
 * TODO: questa divisione tra markup e logica può essere migliorata prendendo spunto dai templates di Drupal.
 */
function settings_live($dati, $type = 'hidden') {
//    $view_page = wimtvpro_alert_reg();
//    $submenu = wimtvpro_submenu($view_page);
//    
//    
//   NS2016 if (!isset($dati['vodPublic']))
//        $dati['features']['livePassword'] = "";
//    if ($dati['features']['livePassword'] == "null")
//        $dati['features']['livePassword'] = "";


$link = "admin.php?page=config&update2";
    //"liveStreamPwd": "-- pwd per il live di wim.tv --",
    //"liveStreamEnabled": "-- abilita live true|false --"
    //eventResellerEnabled": "-- abilita event reselling true|false --",
    //"eventOrganizerEnabled": "-- abilita event organizing true|false --",
    ?>

<!--    <h2>-->
        <?php //echo wimtvpro_link_help(); ?>
        <?php //echo __('Live configuration', "wimtvpro") ?></h2>

    <?php //echo str_replace("live", "current", $submenu) ?>
    

    <!--<div class="clear"></div>-->
    <p><?php echo __('this section you can enable live streaming settings to better match your specific needs. Choose between "Live streaming" to stream your own events, or use the features reserved for Event Organisers and Event Resellers to play the role of organiser or distributor (on behalf of Event Organiser) of live events.', "wimtvpro") ?></p>
    <script>
        jQuery(document).ready(function() {
          jQuery("#edit-liveStreamEnabled").click(function() {
                    if (jQuery(this).is(':checked')) {
                        jQuery("#edit-liveStreamEnabled").attr("value","true");
                    }
                    else {
                        jQuery("#edit-liveStreamEnabled").attr("value","false");
                    }
            });
     
        });
    </script>

    <!--<form enctype="multipart/form-data" action="<?php //echo add_query_arg($_GET)?>" method="post" id="configwimtvpro-group" accept-charset="UTF-8">-->
        <table class="form-table" >
            <tr>
                <th><label for="vodPublic"><?php echo __("Live streaming", "wimtvpro") ?></label></th>
                <td>
                    <input type="checkbox" id="edit-liveStreamEnabled"
                           name="features[vodPublic]" 
                           <?php
                           if ($dati['features']['vodPublic'] == true) {
                               echo ' checked value ="true"';
                               update_option('wp_activeLive', "true");
                           } else {
                               update_option('wp_activeLive', "false");
                           }
                           ?> />

                    <div class="description"><?php echo __("Enables you to live stream your events with WimTV", "wimtvpro") ?></div>
                </td>
            </tr>

            <tr>
                <th><label for="livePassword"><?php echo __("Password") ?></label></th>
                <td>
                    <input type="password" id="edit-liveStreamPwd" name="features[livePassword]" value="<?php echo $dati['features']['livePassword'] ?>"/>
                    <div class="description"><?php echo __("A password is required for live streaming (for authenticating yourself with the streaming server).", "wimtvpro") ?></div>
                </td>
            </tr>

 <input type="hidden" name="features[transcoderNotifyEnabled]" value="false" />

            <!--  // NS: We hide the following rows to disable "Event Resell" and "Live  stream event organization -->   

        <!--            <tr>
                        <th><label for="eventResellerEnabled"><?php// echo __("Live stream events resale", "wimtvpro") ?></label></th>
                        <td>
                          <input type="checkbox" id="edit-eventResellerEnabled"
                                 name="eventResellerEnabled" value="true" <?php //if (strtoupper($dati['eventResellerEnabled']) == "TRUE") echo ' checked="checked"' ?> />
                          <div class="description"><?php //echo __("Enables you to distribute live events organised by other parties (Event Organisers).", "wimtvpro") ?></div>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="eventOrganizerEnabled"><?php //echo __("Live stream events organisation", "wimtvpro") ?></label></th>
                        <td>
                          <input type="checkbox" id="edit-eventOrganizerEnabled" name="eventOrganizerEnabled" value="true" <?php if (strtoupper($dati['eventOrganizerEnabled']) == "TRUE") echo ' checked="checked"' ?> />
                          <div class="description"><?php //echo __("Select if you want to organise live evants and collaborate with an Event Reseller for their distribution.", "wimtvpro") ?></div>
                        </td>
                    </tr>-->
        </table>
        <div class="hidden_value"></div>
        <input type="hidden" name="wimtvpro_update" value="Y" />
        <?php// echo submit_button(__("Update", "wimtvpro")) ?>
    <!--</form>-->

    <?php
}
?>