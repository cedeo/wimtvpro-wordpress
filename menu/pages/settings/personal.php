<?php
/**
 * Written by walter at 31/10/13
 */
/**
 * Mostra la pagina dei dati personali nei settings, la logica viene gestita nella prima parte, del codice,
 * il markup rimane sotto.
 * TODO: questa divisione tra markup e logica può essere migliorata prendendo spunto dai templates di Drupal.
 */
function settings_personal($dati,$type = 'hidden') {
  

    $facebookUri = isset($dati['profile']['facebookUrl']) ? $dati['profile']['facebookUrl'] : "";
    $twitterUri = isset($dati['profile']['twitterContact']) ? $dati['profile']['twitterContact'] : "";
    $linkedInUri = isset($dati['profile']['linkedinUrl']) ? $dati['profile']['linkedinUrl'] : "";
    $email = isset($dati['profile']['email']) ? $dati['profile']['email'] : "";
    $thumbnailId = isset($dati['profile']['thumbnailId']) ? $dati['profile']['thumbnailId'] : "";
    $lastName = isset($dati['profile']['lastName']) ? $dati['profile']['lastName'] : "";
    $firstName = isset($dati['profile']['firstName']) ? $dati['profile']['firstName'] : "";
    
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function(){
      jQuery( ".pickadate" ).datepicker({
        dateFormat: "dd/mm/y"
      });
    });
    </script>
	<?php  echo  wimtvpro_link_help();?>
    


    
    <div class="clear"></div><!--<form enctype="multipart/form-data" action="<?php //echo add_query_arg($_GET)?>" method="post" id="configwimtvpro-group" accept-charset="UTF-8">-->
        <h4><?php __("Personal Info","wimtvpro") ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="edit-name"><?php echo __("First Name","wimtvpro")  ?><span class="form-required" title="">*</span></label></th>
                <td><input type="text" id="edit-name" name="profile[firstName]" value="<?php echo $firstName  ?>" size="40" maxlength="200"/></td>
            </tr>
            <tr><th><label for="edit-Surname"><?php echo __("Last Name","wimtvpro")  ?><span class="form-required" title="">*</span></label></th>
                <td><input type="text" id="edit-Surname" name="profile[lastName]" value="<?php echo $lastName  ?>" size="40" maxlength="200"/></td>
            </tr>
            <tr>
                <th><label for="edit-Email">Email<span class="form-required" title="">*</span></label></th>
                <td><input type="text" id="edit-email" name="profile[email]" value="<?php echo $email  ?>" size="80" maxlength="200"/></td>
            </tr>

        </table>
        <h4><?php __("Social networks","wimtvpro")  ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="facebookUrl">Facebook http://</label></th>
                <td>
                    <input  type="text"  id="edit-facebookURI" name="profile[facebookUrl]" value="<?php echo $facebookUri  ?>" size="100" maxlength="100">
                </td>
            </tr>
            <tr>
                <th><label for="twitterContact">Twitter http://</label></th>
                <td>
                    <input  type="text"  id="edit-twitterURI" name="profile[twitterContact]" value="<?php echo $twitterUri ?>" size="100" maxlength="100">
                </td>
            </tr>
            <tr>
                <th>
                    <label for="linkedinUrl">LinkedIn http://</label>
                </th>
                <td>
                    <input  type="text"  id="edit-LinkedInUri" name="profile[linkedinUrl]" value="<?php echo $linkedInUri  ?>" size="100" maxlength="100">
                </td>
            </tr>
        </table>
        <div class="hidden_value"></div>
        <input type="hidden" name="wimtvpro_update" value="Y" />
 
<?php
}
?>