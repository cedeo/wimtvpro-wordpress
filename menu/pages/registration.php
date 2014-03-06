<?php

/**
 * Mostra la pagina delle playlist presente nel menu laterale, la logica viene gestita nella prima parte, del codice,
 * il markup rimane sotto.
 * TODO: questa divisione tra markup e logica può essere migliorata prendendo spunto dai templates di Drupal.
 */
include_once('modules/registration.php');

function wimtvpro_registration() {
    $registration = isset($_POST['register']) ? $_POST['register'] : "";
    $sandbox = isset($_POST['sandbox']) ? $_POST['sandbox'] : "";
    $reg_name = isset($_POST['reg_name']) ? $_POST['reg_name'] : "";
    $reg_surname = isset($_POST['reg_Surname']) ? $_POST['reg_Surname'] : "";
    $reg_email = isset($_POST['reg_Email']) ? $_POST['reg_Email'] : "";
    $reg_sex = isset($_POST['reg_sex']) ? $_POST['reg_sex'] : "";
    $reg_username = isset($_POST['reg_Username']) ? $_POST['reg_Username'] : "";
    $reg_password = isset($_POST['reg_Password']) ? $_POST['reg_Password'] : "";
    $reg_password_r = isset($_POST['reg_RepeatPassword']) ? $_POST['reg_RepeatPassword'] : "";


    if ($registration == 'Y'){
        wimtvpro_register($reg_name, $reg_surname, $reg_email, $reg_username, $reg_password, $reg_password_r, $reg_sex, $sandbox);
    }

    ?>
    <div class='wrap'>
        <?php  echo  wimtvpro_link_help();?>
        <h2> <?php echo __("WIMTV REGISTRATION","wimtvpro") ?> </h2>
    </div>

    <form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">

        <h4><?php _e("Personal Info","wimtvpro");?></h4>
        <table class="form-table">
            <tr>
                <th><label for="edit-name"><?php _e("First Name");?><span class="form-required" title="">*</span></label></th>
                <td><input type="text" id="edit-name" name="reg_name" value="<?php echo $reg_name ?>" size="40" maxlength="200"/></td>
            </tr>
            <tr>
                <th><label for="edit-Surname"><?php _e("Last Name","wimtvpro");?><span class="form-required" title="">*</span></label></th>
                <td><input type="text" id="edit-Surname" name="reg_Surname" value="<?php echo $reg_surname?>" size="40" maxlength="200"/></td>
            </tr>
            <tr>
                <th><label for="edit-Email">Email<span class="form-required" title="">*</span></label></th>
                <td><input type="text" id="edit-Email" name="reg_Email" value="<?php echo $reg_email ?>" size="80" maxlength="200"/></td>
            </tr>

            <tr>
                <th><label for="edit-sex"><?php _e("Gender","wimtvpro");?><span class="form-required" title="">*</span></label></th>
                <td>
                    <select id="edit-sex" name="reg_sex" class="form-select">
                        <option value="M" <?php if ( $reg_sex=="M") echo "selected='selected'" ?>>M</option>
                        <option value="F" <?php if ( $reg_sex=="F") echo "selected='selected'" ?>>F</option>
                    </select>

                </td>
            </tr>


        </table>

        <h4><?php _e("Login Credentials","wimtvpro");?></h4>
        <input type="hidden" value="No" name="sandbox">
        <table class="form-table"> <!-- SANDBOX -->
            <!--tr>
					<th><label for="edit-sandbox">Please select "no" to use the plugin on the WimTV server. Select "yes" to try the service only on test server</label></th>
					<td>
						<select id="edit-sandbox" name="sandbox" class="form-select">
						<option value="No" <?php if (get_option("wp_sandbox")=="No") echo "selected='selected'" ?>>No</option>
						<option value="Yes" <?php if (get_option("wp_sandbox")=="Yes") echo "selected='selected'" ?>>Yes, for Developer or Test</option>
						</select>
					</td>
				</tr-->

            <tr>
                <th><label for="edit-name"><?php _e("Username","wimtvpro");?><span class="form-required" title="">*</span></label></th>
                <td><input type="text" id="edit-Username" name="reg_Username" value="<?php echo $reg_username ?>" size="30" maxlength="200"/></td>
            </tr>

            <tr>
                <th><label for="edit-Password">Password<span class="form-required" title="">*</span></label></th>
                <td><input type="password" id="edit-Password" name="reg_Password" value="<?php echo $reg_password ?>" size="30" maxlength="200"/></td>
            </tr>

            <tr>
                <th><label for="edit-repPassword"><?php _e("Repeat","wimtvpro");?> Password<span class="form-required" title="">*</span></label></th>
                <td><input type="password" id="edit-repPassword" name="reg_RepeatPassword" value="<?php echo $reg_password_r ?>" size="30" maxlength="200"/></td>
            </tr>

            <tr>
                <?php
                if ($sandbox=="No") {
                    $baseWimtv = "https://www.wim.tv/";
                } else {
                    $baseWimtv = "http://peer.wim.tv:8080/";
                }


                ?>
                <th><label for="edit-acceptEula"><?php _e("Terms of Service","wimtvpro");?><span class="form-required" title="">*</span></label></th>
                <td>
                    <div class="description"><input type="checkbox" id="edit-acceptEula" name="reg_acceptEula" value="true" <?php if (isset($_POST['reg_acceptEula'])) echo "checked='checked'"; ?>/> <?php _e("I have read and agree to WimTV","wimtvpro");?>
                        <a class="termsLink" href="<?php echo $baseWimtv; ?>wimtv-webapp/term.do"><?php _e("Terms of Service","wimtvpro"); ?></a>
                        and
                        <a class="termsLink" href="<?php echo $baseWimtv; ?>wimtv-webapp/privacy.do"><?php _e("Privacy Policies","wimtvpro");?></a></div>
                </td>

            </tr>

        </table>

        <input type="hidden" name="register" value="Y" />
        <?php submit_button(__("Register")); ?>


    </form>


<?php

}

?>