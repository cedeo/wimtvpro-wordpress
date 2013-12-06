<?php

/**
 * @file
 * This file is use for the upload video and function.
 *
 */
function wimtvpro_upload() {
    $view_page = wimtvpro_alert_reg();
    if (!$view_page){
        die();
    }
    $serverActiveFile = ini_get("file_uploads");
    $category = wimtvpro_readOptionCategory();

    if ($serverActiveFile!=1) {
        echo '<div class="error"><p><strong>';
        _e("Attention! Your server does not support upload of files, please modify your server settings with file_uploads = On.","wimtvpro");
        echo '</strong></p></div>';
        die();
    }
    ?>
    <div class='wrap'>
    <?php  echo  wimtvpro_link_help();?>
    <h2><?php echo __("Upload Video","wimtvpro") ?></h2>
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
                    <label for="edit-titlefile"><?php _e("Title"); ?> *</label>
                    <input type="text" id="edit-titlefile" name="titlefile" value="" size="100" maxlength="200" class="form-text required" />
                </p>
                <p>
                    <label for="edit-descriptionfile"><?php _e("Description","wimtvpro"); ?> </label>
                    <br/>
                    <textarea id="edit-descriptionfile" name="descriptionfile" cols="150" rows="5"></textarea>
                </p>

                <p>
                    <label for="edit-videocategory">
                        <?php _e("Category","wimtvpro");?> - <?php _e("Subcategory","wimtvpro");?>
                    </label>
                    <br/>
                    <select onchange="viewCategories(this);" multiple="multiple"
                            name="videoCategory[]" id="edit-videocategory"
                            size="15" class="form-select">
                        <?php echo $category; ?>
                    </select>
                    <br/>
                    (<?php _e("Multiselect with CTRL","wimtvpro");?>)
                </p>

                <p class='description' id='addCategories'></p>
                <input type="hidden" name="wimtvpro_upload" value="Y" />
                <input type="hidden" name="namefunction" id="nameFunction" value="uploadFile" />
                <?php submit_button(__("Upload","wimtvpro"),"primary classupload") ?>
            </div>
        </div>
    </form>

<?php
}
?>