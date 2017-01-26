<?php
/**
 * Written by walter at 31/10/13
 */

/**
 * Mostra la pagina di configurazione dei settings, la logica viene gestita nella prima parte, del codice,
 * il markup rimane sotto.
 * TODO: questa divisione tra markup e logica può essere migliorata prendendo spunto dai templates di Drupal.
 */
function settings_configuration($directory) {
  
    $elencoSkin = array();
    $uploads_info = wp_upload_dir();

    if (!is_dir($directory)) {
        $directory_create = mkdir($uploads_info["basedir"] . "/skinWim");
    }
    $elencoSkin[""] = "-- Base Skin --";
    if (is_dir($directory)) {
        if ($directory_handle = opendir($directory)) {
            //Read directory for skin JWPLAYER
            while (($file = readdir($directory_handle)) !== FALSE) {
                if ((is_dir($directory . DIRECTORY_SEPARATOR . $file) && ($file != ".") && ($file != ".."))) {
                    $elencoSkin[$file] = $file;
                }
//            while (($file = readdir($directory_handle)) !== FALSE) {
//                if ((!is_dir($file)) && ($file != ".") && ($file != "..")) {
//                    $explodeFile = explode(".", $file);
//                    if ($explodeFile[1] == "zip")
//                        $elencoSkin[$explodeFile[0]] = $explodeFile[0];
//                }
            }
            closedir($directory_handle);
        }
    }
    //Create option select form Skin
    $createSelect = "";
    foreach ($elencoSkin as $key => $value) {
        $createSelect .= "<option value='" . $key . "'";
        if ($value == get_option("wp_nameSkin"))
            $createSelect .= " selected='selected' ";
        $createSelect .= ">" . $value . "</option>";
    }


    $uploads_info = wp_upload_dir();
    ?>
    <div class="wrap">
        <?php echo wimtvpro_link_help(); ?>
 <h2><?php _e('CURRENTLY THE PLUGIN MAY SHOW INSTABILITIES', "wimtvpro"); ?></h2>
 <h2><?php _e('SHOULD THIS HAPPEN YOU CAN TAKE ADVANTAGE', "wimtvpro"); ?><a target="_blank" href="http://www.wim.tv">http://www.wim.tv</a> <?php _e('WIMTV OFFERS SOME ADDITIONAL FEATURES', "wimtvpro"); ?></h2>
        <h2><?php _e('SETTINGS_pageTitle', "wimtvpro");     ?></h2>

        <?php
        $view_page = wimtvpro_alert_reg();
        $submenu = wimtvpro_submenu($view_page);
        echo $submenu;
        ?>

        <div>
            <div class="empty"></div>
            <h4><?php _e("Connect to your account on WimTV", "wimtvpro"); ?></h4>

            <form enctype="multipart/form-data" action="<?php echo add_query_arg($_GET) ?>" method="post" id="configwimtvpro-group" accept-charset="UTF-8">

                <table class="form-table">

                    <tr>
                        <th><label for="edit-userwimtv"><?php _e("Username", "wimtvpro"); ?><span class="form-required" title="">*</span></label></th>
                        <td><input type="text" id="edit-userwimtv" name="userWimtv" value="<?php echo get_option("wp_userwimtv"); ?>" size="100" maxlength="200"/></td>
                    </tr>

                    <tr>
                        <th><label for="edit-passwimtv">Password<span class="form-required" title="">*</span></label></th>
                        <td><input value="<?php echo get_option("wp_passwimtv"); ?>" type="password" id="edit-passwimtv" name="passWimtv" size="100" maxlength="200" class="form-text required" /></td>
                    </tr>
                </table>

                <h4><?php _e("Upload and/or choose a skin for your player", "wimtvpro"); ?>. <?php _e("Download it from", "wimtvpro") ?> <a target='new' href='http://www.longtailvideo.com/addons/skins'>Jwplayer skin</a></h4>

                <table class="form-table">
                    <tr>
                        <th><label for="edit-nameskin"><?php _e("Skin Name", "wimtvpro"); ?></label></th>
                        <td><select id="edit-nameskin" name="nameSkin" class="form-select"><?php echo $createSelect; ?></select></td>
                    </tr>
                    <tr>
                        <th><label for="edit-uploadskin"><?php _e("upload a new skin for your player", "wimtvpro"); ?></label></th>
                        <td><input type="file" id="edit-uploadskin" name="files[uploadSkin]" size="100" class="form-file" />
                            <div class="description"><?php
                                echo __("Only .zip files are supported.","wimtvpro"). "<br/>" .
                                __("To use the skin of your choice, copy the", "wimtvpro") . " <a href='http://plugins.longtailvideo.com/crossdomain.xml' target='_new'>crossdomain.xml</a> " . __("file to the root directory (e.g. http://www.mysite.com). You can do it all via FTP  (e.g. FileZilla, Classic FTP, etc).", "wimtvpro") . " <a href='http://www.adobe.com/devnet/adobe-media-server/articles/cross-domain-xml-for-streaming.html'>" . __("More information", "wimtvpro") . "</a>";
                                ?>
                            </div>
                        </td>
                    </tr>
                </table>
                <!-- NS: SET THE display PROPERTY 'none'/'block' TO HIDE/SHOW WIDTH AND HIGHT VIDEO SETTINGS-->
                <!-- <div style="display: block;"> -->
                <h4><?php _e("Size of the player for your videos", "wimtvpro"); ?></h4>

                <table class="form-table">
                    <tr>
                        <th><label for="edit-heightpreview"><?php _e("Height"); ?> (default: 280px)</label></th>
                        <td><input type="text" id="edit-heightpreview" name="heightPreview" value="<?php echo get_option("wp_heightPreview"); ?>" size="100" maxlength="200" class="form-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="edit-widthpreview"><?php _e("Width"); ?> (default: 500px) </label></th>
                        <td><input type="text" id="edit-widthpreview" name="widthPreview" value="<?php echo get_option("wp_widthPreview"); ?>" size="100" maxlength="200" class="form-text" /></td>
                    </tr>

                </table>
           
                <input type="hidden" value="No" name="sandbox">


                <input type="hidden" name="wimtvpro_update" value="Y" />
                <?php submit_button(__("Save changes", "wimtvpro")); ?>
            </form>
        </div>

    </div>
    <?php
}
?>
