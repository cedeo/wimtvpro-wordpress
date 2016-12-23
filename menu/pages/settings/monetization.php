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

//    $view_page = wimtvpro_alert_reg();
//    $submenu = wimtvpro_submenu($view_page);
   
$link = "admin.php?page=config&update1";
    $companyName = isset($dati['finance']['companyName']) ? $dati['finance']['companyName'] : "";
    $paypalEmail = isset($dati['finance']['paypalEmail']) ? $dati['finance']['paypalEmail'] : "";
    $taxCode = isset($dati['taxCode']) ? $dati['taxCode'] : "";
    $vatCode = isset($dati['finance']['vatNumber']) ? $dati['finance']['vatNumber'] : "";
    $street = isset($dati['finance']['billingAddress']['street']) ? $dati['finance']['billingAddress']['street'] : "";
    $state = isset($dati['finance']['billingAddress']['state']) ? $dati['finance']['billingAddress']['state'] : "";
    $city = isset($dati['finance']['billingAddress']['city']) ? $dati['finance']['billingAddress']['city'] : "";
    $zipCode = isset($dati['finance']['billingAddress']['zipCode']) ? $dati['finance']['billingAddress']['zipCode'] : "";

    
 
 if (!isset($companyName) && $companyName != "") {
        $style="style='display:none'";
    }
    
    
    /*
     * 
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

//    ?>
	<?php // echo  wimtvpro_link_help();?>
    <!--<h2>//<?php //echo __("Monetisation","wimtvpro")  ?></h2>-->
    <?php //echo str_replace("payment","current",$submenu) ?>
<!--    <div class="clear"></div>
	<p>-->
    <?php// _e("Please complete the following fields if you wish to make financial transactions on Wim.tv (e.g. buy or sell videos, post pay per view videos or bundles). You may wish to fill your data now or do it later by returning in this section of your Settings.","wimtvpro") ?>
    <!--</p>-->
    <script>
        jQuery(document).ready(function() {

if (!jQuery("#edit-affiliate").is(':checked')) {
                        jQuery(".affiliateTr").hide();
                        jQuery("#edit-affiliate").attr("value","false");
                        jQuery("#edit-companyName").attr("value","");
                    }
                    else { jQuery(".affiliateTr").show();
                        jQuery("#edit-affiliate").attr("value","true");
                    }
            jQuery("#edit-affiliate").click(function() {
                    var name = jQuery(this).attr("name");
                    if (jQuery(this).is(':checked')) {
                        jQuery(".affiliateTr").show();
                        jQuery("#edit-affiliate").attr("value","true");
                    }
                    else {
                        jQuery(".affiliateTr").hide();
                        jQuery("#edit-affiliate").attr("value","false");
                        jQuery("#edit-affiliateConfirm").attr("value","false");
                        jQuery("#edit-companyName").attr("value","");
                    }
                });

            jQuery("#edit-affiliateConfirm").click(function() {
                var name = jQuery(this).attr("name");
                    if (jQuery(this).is(':checked')) {
                        jQuery("#edit-affiliateConfirm").attr("value","true");
                    }
                    else {
                        jQuery("#edit-affiliateConfirm").attr("value","false");
                    }
            });
           
                   
                    
        });
    </script>
   
        <table class="form-table">
            <tr>
                <th><label for="liveStreamEnabled"><?php echo __("Are you affiliate to the following company?","wimtvpro")  ?></label></th>
                <td>
                  <input type="checkbox" id="edit-affiliate"
                         name="affiliate2" 
                         <?php if (isset($companyName) && $companyName != "") {
                                   echo ' checked value="true"';
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
                <th><label for="companyConfirm"><?php echo __("Have you the legal right of acting as an affiliate to the preceeding company?","wimtvpro")  ?></label></th>
                <td>
                    <input type="checkbox" id="edit-affiliateConfirm"
                           name="companyConfirm" 
                           <?php
                           if(isset($companyName) && $companyName != "")  {    
                           echo ' checked value="true"';
                                 } ?> />
                <td>
            </tr>

        </table>
        <h4><?php echo __("PayPal")  ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="paypalEmail"><?php echo __("Paypal Email","wimtvpro")  ?></label></th>
                <td><input type="text" id="edit-paypalEmail" name="finance[paypalEmail]" value="<?php echo $paypalEmail  ?>" size="100" maxlength="100"/></td>
            </tr>
        </table>
        <h4><?php echo __("Tax Info","wimtvpro")  ?></h4>
        <table class="form-table">

            <tr>
                <th><label for="finance[vatNumber]"><?php echo "VatNumber"// __("VAT Number","wimtvpro")  ?></label></th>
                <td><input type="text" id="" name="vatNumber"   value="<?php echo $vatCode ?>" size="11" minlength="0" maxlength="11"/></td>
            </tr>
        </table>
        <h4><?php echo __("Billing address","wimtvpro")  ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="finance[billingAddress][country]"><?php echo __("Country","wimtvpro")  ?></label></th>
                <td><input type="text" id="edit-billingAddressCity" name="finance[billingAddress][country]" value="<?php echo $city  ?>" size="100" maxlength="100"/></td>
            </tr>
            <tr>
                <th><label for="billingAddress[street]"><?php echo __("Street","wimtvpro")  ?></label></th>
                <td><input type="text" id="edit-billingAddressStreet" name="finance[billingAddress][street]" value="<?php echo $street  ?>" size="100" maxlength="100"/></td>
            </tr>

            <tr>
                <th><label for="billingAddress[city]"><?php echo __("City","wimtvpro")  ?></label></th>
                <td><input type="text" id="edit-billingAddressCity" name="finance[billingAddress][city]" value="<?php echo $city  ?>" size="100" maxlength="100"/></td>
            </tr>

            <tr>
                <th><label for="billingAddress[state]"><?php echo __("State","wimtvpro")  ?></label></th>
                <td><input type="text" id="edit-billingAddressCity" name="finance[billingAddress][state]" value="<?php echo $state ?>" size="100" maxlength="100"/></td>
            </tr>

            <tr>
                <th><label for="billingAddress[zipCode]"><?php echo __("Zip/Postal Code","wimtvpro")  ?></label></th>
                <td><input type="text" id="edit-billingAddressCity" name="finance[billingAddress][zipCode]" value="<?php echo $zipCode ?>" size="100" maxlength="100"/></td>
            </tr>
        </table>
        <input type="hidden" name="wimtvpro_update" value="Y" />  
        <input type="hidden" name="wimtvpro_update" value="Y" />
 

<?php
}
?>