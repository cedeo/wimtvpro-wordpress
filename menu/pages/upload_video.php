<?php

/**
 * Mostra la pagina di upload di un video presente nel menu laterale,
 * la logica viene gestita nella prima parte, del codice,
 * il markup rimane sotto.
 * TODO: questa divisione tra markup e logica può essere migliorata prendendo spunto dai templates di Drupal.
 */
function wimtvpro_upload() {

    $view_page = wimtvpro_alert_reg();
    if (!$view_page){
        die();
    }
    $serverActiveFile = ini_get("file_uploads");

    
    if ($serverActiveFile!=1) {
        echo '<div class="error"><p><strong>';
        _e("Attention! Your server does not support upload of files, please modify your server settings with file_uploads = On.","wimtvpro");
        echo '</strong></p></div>';
        die();
    }
    ?>
    <div class='wrap'>
    <?php  echo  wimtvpro_link_help();?>
    <h2><?php echo __("UPLOAD_pageTitle","wimtvpro") ?></h2>
    <div id='message'></div>

    <div class="progress-bar">
        <span></span>
        <p><?php echo __("Do not leave this page until the upload is complete","wimtvpro") ?></p>
    </div>
    <form enctype="multipart/form-data" action="#" method="post" id="wimtvpro-upload" accept-charset="UTF-8">
        <div>
            <div class="form-item form-type-textfield form-item-titlefile">
                <p>
                    <label for="edit-videofile"><?php _e('Select video','wimtvpro'); ?>*</label>
                    <input type="file" id="edit-videofile" name="videoFile" size="60" class="form-file required" />
                </p>
                <p>
                    <label for="edit-thumbnailfile"><?php echo _e("Select thumbnail","wimtvpro")  ?></label>
                    <input type="file" id="edit-thumbnailfile" name="thumbnailFile" size="60" class="form-file" />
                </p>
                <p>
                    <label for="edit-titlefile"><?php _e("Title"); ?> *</label>
                    <input type="text" id="edit-titlefile" name="titlefile" value="" size="100" maxlength="200" class="form-text required" />
                </p>
                <p>
                    <label for="edit-descriptionfile"><?php _e("Description","wimtvpro"); ?> </label>
                    <br/>
                    <!--NS: We have reduced number of columns-->
                    <textarea id="edit-descriptionfile" name="descriptionfile" cols="110" rows="5"></textarea>
                </p>
                 <p id="parag_tags">
                    <label for="edit-tags"><?php echo "Tag" ?> </label>
                    <br/>
                    <!--NS: We have reduced number of columns-->
                    <input type="text" id="edit-titlefile" name="video[]" value="" size="10" maxlength="200" class="form-text required" />
              
                 
                               </p>
                <input type="button" id="add_tag"  style="font-size:15px" value="<?php echo __("Add another tag","wimtvpro")?>"> <i class="fa fa-plus-square"></i></input>
                <input type="hidden" name="wimtvpro_upload" value="Y" />
                <input type="hidden" name="namefunction" id="nameFunction" value="uploadFile" />
                <?php submit_button(__("Upload","wimtvpro"),"primary classupload") ?>
            </div>
        </div>
    </form>
   <script>

        jQuery("#add_tag").click(function() {
           
                jQuery('#parag_tags').append('<br><input type="text" id="edit-titlefile" name="video[]" value="" size="10" maxlength="200" class="form-text required" />');
           

        });

    </script>
<?php
}
?>