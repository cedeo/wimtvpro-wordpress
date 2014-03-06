<?php
/**
 * Written by walter at 31/10/13
 */
/**
 * Mostra la pagina della monetizzazione nei settings, la logica viene gestita nella prima parte, del codice,
 * il markup rimane sotto.
 * TODO: questa divisione tra markup e logica può essere migliorata prendendo spunto dai templates di Drupal.
 */
function settings_monetization($dati) {

    $view_page = wimtvpro_alert_reg();
    $submenu = wimtvpro_submenu($view_page);
    if (strtoupper($dati['affiliate'])!="TRUE") {
        $style="style='display:none'";
    }
    $companyName = isset($dati['companyName']) ? $dati['companyName'] : "";
    $paypalEmail = isset($dati['paypalEmail']) ? $dati['paypalEmail'] : "";
    $taxCode = isset($dati['taxCode']) ? $dati['taxCode'] : "";
    $vatCode = isset($dati['vatCode']) ? $dati['vatCode'] : "";
    $street = isset($dati['billingAddress']['street']) ? $dati['billingAddress']['street'] : "";
    $state = isset($dati['billingAddress']['state']) ? $dati['billingAddress']['state'] : "";
    $city = isset($dati['billingAddress']['city']) ? $dati['billingAddress']['city'] : "";
    $zipCode = isset($dati['billingAddress']['zipCode']) ? $dati['billingAddress']['zipCode'] : "";

    /*
    "paypalEmail": "-- indirizzo email account Pay Pal --",
    "companyName": "-- nome azienda --",
    "affiliateConfirm": "-- hai i diritti legali per operare come affiliato dell&#65533;azienza --",
    "vatCode": "-- P. iva --",
    "taxCode": "-- CF --",
    "billingAddress": {
        "street": "-- via  --",
        "city": "-- citt&#65533; --",
        "state": "-- provincia --",
        "zipCode": "-- cap --"
        }
    */

    ?>
	<?php  echo  wimtvpro_link_help();?>
    <h2><?php echo __("Monetisation","wimtvpro")  ?></h2>
    <?php echo str_replace("payment","current",$submenu) ?>
    <div class="clear"></div>
	<p>
    <?php _e("Please complete the following fields if you wish to make financial transactions on Wim.tv (e.g. buy or sell videos, post pay per view videos or bundles). You may wish to fill your data now or do it later by returning in this section of your Settings.","wimtvpro") ?>
    </p>
    <script>
        jQuery(document).ready(function() {

            jQuery("#edit-affiliate").click(function() {
                    var name = jQuery(this).attr("name");
                    if (jQuery(this).attr("checked")=="checked") {
                        jQuery(".affiliateTr").show();
                        jQuery("#edit-affiliateHidden").value("true");
                    }
                    else {
                        jQuery(".affiliateTr").hide();
                        jQuery("#edit-affiliateHidden").attr("value","false");
                        jQuery("#edit-affiliateConfirmHidden").attr("value","false");
                        jQuery("#edit-companyName").attr("value","");
                    }
                });

            jQuery("#edit-affiliateConfirm").click(function() {
                var name = jQuery(this).attr("name");
                    if (jQuery(this).attr("checked")=="checked") {
                        jQuery("#edit-affiliateConfirmHidden").attr("value","true");
                    }
                    else {
                        jQuery("#edit-affiliateConfirmHidden").attr("value","false");
                    }
            });
        });
    </script>

    <form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">
        <h4><?php echo __("Affiliation","wimtvpro")  ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="liveStreamEnabled"><?php echo __("Are you affiliate to the following company?","wimtvpro")  ?></label></th>
                <td>
                  <input type="checkbox" id="edit-affiliate"
                         name="affiliate2" value="true"
                         <?php if (strtoupper($dati['affiliate'])=="TRUE") {
                                   echo ' checked="checked"';
                               } ?> />
                <td>
            </tr>
            <tr class="affiliateTr" <?php echo  $style ?> >
                <th><label for="companyName"><?php echo __("Company Name","wimtvpro")  ?></label></th>
                <td>
                    <input type="text" id="edit-companyName" name="companyName" value="<?php echo $companyName  ?>"  size="80" maxlength="20" />
                </td>
            </tr>
			<tr class="affiliateTr" <?php echo  $style ?> >
                <th><label for="affiliateConfirm"><?php echo __("Have you the legal right of acting as an affiliate to the preceeding company?","wimtvpro")  ?></label></th>
                <td>
                    <input type="checkbox" id="edit-affiliateConfirm"
                           name="affiliateConfirm2" value="true"
                           <?php if (strtoupper($dati['affiliateConfirm'])=="TRUE") {
                                     echo ' checked="checked"';
                                 } ?> />
                <td>
            </tr>

        </table>
        <input type="hidden" id="edit-affiliateHidden" name="affiliate" value="<?php echo $dati['affiliate']  ?>">
        <input type="hidden" id="edit-affiliateConfirmHidden" name="affiliateConfirm" value="<?php echo $dati['affiliateConfirm']  ?>">
        <h4><?php echo __("PayPal")  ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="paypalEmail"><?php echo __("Paypal Email","wimtvpro")  ?></label></th>
                <td><input type="text" id="edit-paypalEmail" name="paypalEmail" value="<?php echo $paypalEmail  ?>" size="100" maxlength="100"/></td>
            </tr>
        </table>
        <h4><?php echo __("Tax Info","wimtvpro")  ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="vatCode"><?php echo __("Tax Code","wimtvpro")  ?></label></th>
                <td><input type="text" id="edit-taxCode" name="taxCode" value="<?php echo $taxCode  ?>" size="80" maxlength="20"/></td>
            </tr>
            <tr>
                <th><label for="vatCode"><?php echo __("VAT Code","wimtvpro")  ?></label></th>
                <td><input type="text" id="edit-vatCode" name="vatCode" value="<?php echo $vatCode ?>" size="80" maxlength="20"/></td>
            </tr>
        </table>
        <h4><?php echo __("Billing address","wimtvpro")  ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="billingAddress[street]"><?php echo __("Street","wimtvpro")  ?></label></th>
                <td><input type="text" id="edit-billingAddressStreet" name="billingAddress[street]" value="<?php echo $street  ?>" size="100" maxlength="100"/></td>
            </tr>

            <tr>
                <th><label for="billingAddress[city]"><?php echo __("City","wimtvpro")  ?></label></th>
                <td><input type="text" id="edit-billingAddressCity" name="billingAddress[city]" value="<?php echo $city  ?>" size="100" maxlength="100"/></td>
            </tr>

            <tr>
                <th><label for="billingAddress[state]"><?php echo __("State","wimtvpro")  ?></label></th>
                <td><input type="text" id="edit-billingAddressCity" name="billingAddress[state]" value="<?php echo $state ?>" size="100" maxlength="100"/></td>
            </tr>

            <tr>
                <th><label for="billingAddress[zipCode]"><?php echo __("Zip/Postal Code","wimtvpro")  ?></label></th>
                <td><input type="text" id="edit-billingAddressCity" name="billingAddress[zipCode]" value="<?php echo $zipCode ?>" size="100" maxlength="100"/></td>
            </tr>
        </table>
        <input type="hidden" name="wimtvpro_update" value="Y" />
        <?php echo submit_button(__("Update","wimtvpro")) ?>
    </form>

<?php
}
?>
