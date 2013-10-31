<?php
/**
 * Written by walter at 31/10/13
 */
function settings_features($dati) {
    echo '
		        <script type="text/javascript">
		  		jQuery(document).ready(function(){
		  		  jQuery( "#edit-hidePublicShowtimeVideos" ).change( function(){

		          	if  (jQuery(this).val()=="false") {
				      	jQuery("#viewPage").fadeIn();
				      }else{
				      	jQuery("#viewPage").fadeOut();

				      }

		          });

		  		});
		  		</script>
		     	';
    $view_page = wimtvpro_alert_reg();
    $submenu = wimtvpro_submenu($view_page);
    echo "<h2>" . __("Features","wimtvpro") . "</h2>";
    echo str_replace("other","current",$submenu);


    echo '<div class="clear"></div>
			  <form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">

				<table class="form-table">
					<tr>
						<th><label for="edit-name">' . __("Index and show public videos on WimTV","wimtvpro") . ' (<a href="http://www.wim.tv" target="new">www.wim.tv</a>)</label></th>
						<td>
							<select id="edit-hidePublicShowtimeVideos" name="hidePublicShowtimeVideos" class="form-select">
								<option value="false"';
    if ( $dati['hidePublicShowtimeVideos']=="false") echo 'selected="selected"';
    echo '>' . __("Yes") . '</option>
								<option value="true"';
    if ( $dati['hidePublicShowtimeVideos']=="true") echo 'selected="selected"';
    echo '>No</option>
							</select>

						</td>


					</tr>


				</table>';

    $page_name = "";
    if (isset($dati['pageName'])) {
        $page_name = $dati['pageName'];
    }

    $page_description = "";
    if (isset($dati['pageDescription'])) {
        $page_description = $dati['pageDescription'];
    }

    echo '


				 <table id="viewPage"';

    if ( $dati['hidePublicShowtimeVideos']=="true") echo ' style="display:none; "';

    echo ' class="form-table">

					<tr><td colspan="2"><h4>' . __("WimTV Page","wimtvpro") . '</h4></td></tr>

						<tr>
							<th><label for="pageName">' . __("Page Name","wimtvpro") . '</label></th>
							<td>
								<input  type="text"  id="edit-pageName" name="pageName" value="' . $page_name . '" size="100" maxlength="100">
							</td>
						</tr>



						<tr>

						<th><label for="pageDescription">' . __("Page Description","wimtvpro") . '</label></th>
							<td>
								<textarea  type="text" style="width:260px; height:90px;" id="edit-pageDescription" name="pageDescription">' . $page_description . '</textarea>
							</td>


						</tr>


				  </table>';
    echo '<div class="hidden_value"></div>';
    echo '<input type="hidden" name="wimtvpro_update" value="Y" />';
    submit_button(__("Update","wimtvpro"));


    echo '</form>';


    //"liveStreamPwd": "-- pwd per il live di wim.tv --",
    //"liveStreamEnabled": "-- abilita live true|false --"
    //eventResellerEnabled": "-- abilita event reselling true|false --",
    //"eventOrganizerEnabled": "-- abilita event organizing true|false --",

}