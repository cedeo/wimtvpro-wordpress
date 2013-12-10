<?php
/**
 * Written by walter at 31/10/13
 */
function settings_personal($dati) {
    $view_page = wimtvpro_alert_reg();
    $submenu = wimtvpro_submenu($view_page);
    $facebookUri = isset($dati['facebookURI']) ? $dati['facebookURI'] : "";
    $twitterUri = isset($dati['twitterURI']) ? $dati['twitterURI'] : "";
    $linkedInUri = isset($dati['linkedInURI']) ? $dati['linkedInURI'] : "";

    ?>
    <script type="text/javascript">
    jQuery(document).ready(function(){
      jQuery( ".pickadate" ).datepicker({
        dateFormat: "dd/mm/y"
      });
    });
    </script>
	<?php  echo  wimtvpro_link_help();?>
    <h2><?php _e("Personal Info","wimtvpro") ?></h2>

    <?php echo str_replace("user","current",$submenu) ?>

    <div class="clear"></div>
    <form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">
        <h4><?php __("Personal Info","wimtvpro") ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="edit-name"><?php echo __("First Name","wimtvpro")  ?><span class="form-required" title="">*</span></label></th>
                <td><input type="text" id="edit-name" name="name" value="<?php echo $dati['name']  ?>" size="40" maxlength="200"/></td>
            </tr>
            <tr><th><label for="edit-Surname"><?php echo __("Last Name","wimtvpro")  ?><span class="form-required" title="">*</span></label></th>
                <td><input type="text" id="edit-Surname" name="surname" value="<?php echo $dati['surname']  ?>" size="40" maxlength="200"/></td>
            </tr>
            <tr>
                <th><label for="edit-Email">Email<span class="form-required" title="">*</span></label></th>
                <td><input type="text" id="edit-email" name="email" value="<?php echo $dati['email']  ?>" size="80" maxlength="200"/></td>
            </tr>

            <tr>
                <th><label for="sex"><?php echo __("Gender","wimtvpro")  ?><span class="form-required" title="">*</span></label></th>
                <td>
                    <select id="edit-sex" name="sex" class="form-select">
                        <option value="M" <?php if ( $dati['sex']=="M") echo 'selected="selected"' ?>>M</option>
					    <option value="F" <?php if ( $dati['sex']=="F") echo 'selected="selected"';?>>F</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="dateOfBirth"><?php echo __("Date of Birth","wimtvpro")  ?></label>
                </th>
                <td>
                    <input  type="text" class="pickadate" id="edit-giorno" name="dateOfBirth" value="<?php echo $dati['dateOfBirth']  ?>" size="10" maxlength="10">
                    <div class="description">dd/mm/yy</div>
                </td>
            </tr>
        </table>
        <h4><?php __("Social networks","wimtvpro")  ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="facebookUri">Facebook http://</label></th>
                <td>
                    <input  type="text"  id="edit-facebookURI" name="facebookUri" value="<?php echo $facebookUri  ?>" size="100" maxlength="100">
                </td>
            </tr>
            <tr>
                <th><label for="twitterUri">Twitter http://</label></th>
                <td>
                    <input  type="text"  id="edit-twitterURI" name="twitterUri" value="<?php echo $twitterUri ?>" size="100" maxlength="100">
                </td>
            </tr>
            <tr>
                <th>
                    <label for="linkedInUri">LinkedIn http://</label>
                </th>
                <td>
                    <input  type="text"  id="edit-LinkedInUri" name="linkedInUri" value="<?php echo $linkedInUri  ?>" size="100" maxlength="100">
                </td>
            </tr>
        </table>
        <div class="hidden_value"></div>
        <input type="hidden" name="wimtvpro_update" value="Y" />
    <?php submit_button(__("Update","wimtvpro")) ?>


    </form>
<?php
}
?>