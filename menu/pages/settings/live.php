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



$link = "admin.php?page=config&update2"; ?></h2>

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

         
        </table>
        <div class="hidden_value"></div>
        <input type="hidden" name="wimtvpro_update" value="Y" />
    

    <?php
}
?>