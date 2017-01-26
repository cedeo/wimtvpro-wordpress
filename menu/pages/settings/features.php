<?php
/**
 * Written by walter at 31/10/13
 */
/**
 * Mostra la pagina delle features nei settings, la logica viene gestita nella prima parte, del codice,
 * il markup rimane sotto.
 * TODO: questa divisione tra markup e logica può essere migliorata prendendo spunto dai templates di Drupal.
 */
function settings_features($dati) {

    $page_name = "";
    if (isset($dati['profile']['pageTitle'])) {
        $page_name = $dati['profile']['pageTitle'];
    }
    $page_description = "";
    if (isset($dati['profile']['pageDescription'])) {
        $page_description = $dati['profile']['pageDescription'];
    }
    
     if (isset($dati['features']['transcoderNotifyEnabled'])) {
        $transcoderNotifyEnabled = $dati['features']['transcoderNotifyEnabled'];
    }



    ?>
    <script type="text/javascript">
        jQuery(document).ready(function(){
          jQuery( "#edit-hidePublicShowtimeVideos" ).change( function(){

            if  (jQuery(this).val()=="false") {
                jQuery("#viewPage").fadeIn();
              }else{
                jQuery("#viewPage").fadeOut();

              }

          });
  jQuery("#edit-transcoding").click(function() {
                    if (jQuery(this).is(':checked')) {
                        jQuery("#edit-transcoding").attr("value","true");
                    }
                    else {
                        jQuery("#edit-transcoding").attr("value","false");
                    }
            });
        });
    </script>
 
   
   
    <div class="clear"></div>  

        <table id="viewPage" class="form-table">
            <tr>
                <td colspan="2"><h4><?php echo __("WimTV Page","wimtvpro") ?></h4></td>
            </tr>
            <tr>
                <th><label for="pageName"><?php echo __("Page Name","wimtvpro") ?></label></th>
                <td>
                    <input  type="text"  id="edit-pageName" name="profile[pageTitle]" value="<?php echo $page_name ?>" size="100" maxlength="100">
                </td>
            </tr>
            <tr>
                <th>
                    <label for="pageDescription"><?php echo __("Page Description","wimtvpro") ?></label>
                </th>
                <td>
                    <textarea  type="text" style="width:260px; height:90px;" id="edit-pageDescription" name="profile[pageDescription]"><?php echo $page_description ?></textarea>
                </td>
            </tr>
            <tr>
                <th><label for="transcoding"><?php echo __("Transcoding notification","wimtvpro") ?></label></th>
                <td>
                    <input type="checkbox" id="edit-transcoding" name="features[transcoderNotifyEnabled]"  <?php if (isset($transcoderNotifyEnabled)) {
                                   echo ' checked value="true"';
                               }
                               
                               ?>/>
                    <div class="description"><?php echo __("Want receive email notification for every video transcoded correctly?","wimtvpro") ?></div>
                </td>
            </tr>
       </table>

        <input type="hidden" name="wimtvpro_update" value="Y" />
       
<?php
}
?>