<?php
/**
 * Written by walter at 31/10/13
 */
function settings_configuration($directory) {
    $elencoSkin = array();
    $uploads_info = wp_upload_dir();

    if (!is_dir($directory)) {
        $directory_create = mkdir($uploads_info["basedir"] . "/skinWim");
    }

    if (is_dir($directory)) {
        if ($directory_handle = opendir($directory)) {
            //Read directory for skin JWPLAYER
            $elencoSkin[""] = "-- Base Skin --";
            while (($file = readdir($directory_handle)) !== FALSE) {
                if ((!is_dir($file)) && ($file!=".") && ($file!="..")) {
                    $explodeFile = explode("." , $file);
                    if ($explodeFile[1]=="zip")
                        $elencoSkin[$explodeFile[0]] = $explodeFile[0];
                }
            }
            closedir($directory_handle);
        }
    }
    //Create option select form Skin
    $createSelect = "";
    foreach ($elencoSkin as $key => $value){
        $createSelect .= "<option value='" . $key . "'";
        if ($value==get_option("wp_nameSkin"))  $createSelect .= " selected='selected' ";
        $createSelect .= ">" . $value . "</option>";
    }


    $uploads_info = wp_upload_dir();
?>
<div class="wrap">
    <?php  echo  wimtvpro_link_help();?>
    
    <h2><?php _e("Configuration","wimtvpro");?></h2>
	
    <?php
	$view_page = wimtvpro_alert_reg();
    $submenu = wimtvpro_submenu($view_page);

    ?>

    <?php echo str_replace("config","current",$submenu)  ?>

    <div>
        <div class="empty"></div>
        <h4><?php _e("Connect to your account on WimTV","wimtvpro");?></h4>

        <form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">

            <table class="form-table">

                <tr>
                    <th><label for="edit-userwimtv"><?php _e("Username","wimtvpro"); ?><span class="form-required" title="">*</span></label></th>
                    <td><input type="text" id="edit-userwimtv" name="userWimtv" value="<?php echo get_option("wp_userwimtv");?>" size="100" maxlength="200"/></td>
                </tr>

                <tr>
                    <th><label for="edit-passwimtv">Password<span class="form-required" title="">*</span></label></th>
                    <td><input value="<?php echo get_option("wp_passwimtv");?>" type="password" id="edit-passwimtv" name="passWimtv" size="100" maxlength="200" class="form-text required" /></td>
                </tr>
            </table>

            <h4><?php _e("Upload and/or choose a skin for your player","wimtvpro");?>. <?php _e("Download it from","wimtvpro")?> <a target='new' href='http://www.longtailvideo.com/addons/skins'>Jwplayer skin</a></h4>

            <table class="form-table">
                <tr>
                    <th><label for="edit-nameskin"><?php _e("Skin Name","wimtvpro");?></label></th>
                    <td><select id="edit-nameskin" name="nameSkin" class="form-select"><?php echo $createSelect; ?></select></td>
                </tr>
                <tr>
                    <th><label for="edit-uploadskin"><?php _e("upload a new skin for your player","wimtvpro");?></label></th>
                    <td><input type="file" id="edit-uploadskin" name="files[uploadSkin]" size="100" class="form-file" />
                        <div class="description"><?php echo __("Only .zip files are supported Save wp-content/uploads/skinWim to a public URL","wimtvpro") . "<br/>" .
                                __("To use the skin of your choice, copy the","wimtvpro") .  " <a href='http://plugins.longtailvideo.com/crossdomain.xml' target='_new'>crossdomain.xml</a> " . __("file to the root directory (e.g. http://www.mysite.com). You can do it all via FTP  (e.g. FileZilla, Classic FTP, etc).","wimtvpro") . " <a href='http://www.adobe.com/devnet/adobe-media-server/articles/cross-domain-xml-for-streaming.html'>" . __("More information","wimtvpro") . "</a>"; ?>
                        </div>
                    </td>
                </tr>
            </table>

            <h4><?php _e("Size of the player for your videos","wimtvpro" ); ?></h4>

            <table class="form-table">
                <tr>
                    <th><label for="edit-heightpreview"><?php _e("Height");?> (default: 280px)</label></th>
                    <td><input type="text" id="edit-heightpreview" name="heightPreview" value="<?php echo get_option("wp_heightPreview");?>" size="100" maxlength="200" class="form-text" /></td>
                </tr>
                <tr>
                    <th><label for="edit-widthpreview"><?php _e("Width");?> (default: 500px) </label></th>
                    <td><input type="text" id="edit-widthpreview" name="widthPreview" value="<?php echo get_option("wp_widthPreview");?>" size="100" maxlength="200" class="form-text" /></td>
                </tr>

            </table>

            <!--h4><?php _e("Other information" ,"wimtvpro"); ?></h4-->

            <input type="hidden" value="No" name="sandbox">
            <!--<table class="form-table">
                <tr>
					<th><label for="edit-sandbox">Please select "no" to use the plugin on the WimTV server. Select "yes" to try the service only on test server</label></th>
					<td>
						<select id="edit-sandbox" name="sandbox" class="form-select">
						<option value="No" <?php if (get_option("wp_sandbox")=="No") echo "selected='selected'" ?>>No</option>
						<option value="Yes" <?php if (get_option("wp_sandbox")=="Yes") echo "selected='selected'" ?>>Yes, for Developer or Test</option>
						</select>
					</td>
				</tr>

                <tr>
                    <th><label for="edit-publicPage"><?php _e('Would you like to add the "Share" button in the video player?',"wimtvpro");?></label></th>
                    <td>
                        <select id="edit-publicPage" name="publicPage" class="form-select">
                            <option value="No" <?php if (get_option("wp_shareVideo")=="No") echo "selected='selected'" ?>>No</option>
                            <option value="Yes" <?php if (get_option("wp_shareVideo")=="Yes") echo "selected='selected'" ?>><?php _e("Yes"); ?></option>
                        </select>

                    </td>
                </tr>

                <tr>
                    <th><label for="edit-publicPage"><?php _e("Would you like to add a public WimVod Page to your site?","wimtvpro");?></label></th>
                    <td>
                        <select id="edit-publicPage" name="publicPage" class="form-select">
                            <option value="No" <?php if (get_option("wp_publicPage")=="No") echo "selected='selected'" ?>>No</option>
                            <option value="Yes" <?php if (get_option("wp_publicPage")=="Yes") echo "selected='selected'" ?>><?php _e("Yes"); ?></option>
                        </select>

                    </td>
                </tr>

            </table -->

            <input type="hidden" name="wimtvpro_update" value="Y" />
            <?php submit_button(__("Save changes","wimtvpro")); ?>
        </form>
    </div>

</div>
<?php
}
?>