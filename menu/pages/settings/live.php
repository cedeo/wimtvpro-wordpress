<?php
/**
 * Written by walter at 31/10/13
 */
function settings_live($dati) {
    echo '<h2>' . __('Live configuration',"wimtvpro") . '</h2>';
    $view_page = wimtvpro_alert_reg();
    $submenu = wimtvpro_submenu($view_page);

    echo str_replace("live","current",$submenu);


    if (!isset($dati['liveStreamPwd'])) $dati['liveStreamPwd']= "";
    if ($dati['liveStreamPwd']=="null") $dati['liveStreamPwd']= "";

    echo '<div class="clear"></div>
			  <p>In ' . __('this section you can enable live streaming settings to better match your specific needs. Choose between "Live streaming" to stream your own events, or use the features reserved for Event Organisers and Event Resellers to play the role of organiser or distributor (on behalf of Event Organiser) of live events.',"wimtvpro"). '</p>';
    echo '

			  <script>

			  	jQuery(document).ready(function() {

			    	jQuery("#edit-liveStreamEnabled,#edit-eventResellerEnabled,#edit-eventOrganizerEnabled").click(

			    	function() {
			    		var name = jQuery(this).attr("name");
			    		if (jQuery(this).attr("checked")=="checked") {
			    			jQuery("." + name).remove();
			    		}
			    		else {

			    			jQuery("<input>").attr({
							    type: "hidden",
							    value: "false",
							    name: name ,
							    class: name ,
							}).appendTo(".hidden_value");

			    		}
			    	})

			    });

			  </script>

			  <form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">
				 <table class="form-table">


							<tr>
			              		<th><label for="liveStreamEnabled">' . __("Live streaming","wimtvpro") . '</label></th>
								<td>
								  <input type="checkbox" id="edit-liveStreamEnabled" name="liveStreamEnabled" value="true"
								  ';
    if (strtoupper($dati['liveStreamEnabled'])=="TRUE") {
        echo ' checked="checked"';
        update_option('wp_activeLive', "true");
    } else {
        update_option('wp_activeLive', "false");
    }
    echo  '
								  />
								  <div class="description">'  . __("Enables you to live stream your events with WimTV","wimtvpro")  . '</div>
								</td>
							</tr>

							<tr>
			              		<th><label for="liveStreamPwd">' . __("Password") . '</label></th>
								<td>
								  <input type="password" id="edit-liveStreamPwd" name="liveStreamPwd" value="' . $dati['liveStreamPwd'] .  '"/>
								  <div class="description">' . __("A password is required for live streaming (for authenticating yourself with the streaming server).","wimtvpro") .  '</div>
								</td>
							</tr>


							<tr>
			              		<th><label for="eventResellerEnabled">' . __("Live stream events resale","wimtvpro") . '</label></th>
								<td>
								  <input type="checkbox" id="edit-eventResellerEnabled" name="eventResellerEnabled" value="true"
								  ';
    if (strtoupper($dati['eventResellerEnabled'])=="TRUE") echo ' checked="checked"';
    echo '
								  />
								  <div class="description">' . __("Enables you to distribute live events organised by other parties (Event Organisers).","wimtvpro") . '</div>
								</td>
							</tr>

							<tr>
			              		<th><label for="eventOrganizerEnabled">' . __("Live stream events organisation","wimtvpro") . '</label></th>
								<td>
								  <input type="checkbox" id="edit-eventOrganizerEnabled" name="eventOrganizerEnabled" value="true"
								  ';
    if (strtoupper($dati['eventOrganizerEnabled'])=="TRUE") echo ' checked="checked"';
    echo '
								  />
								  <div class="description">' . __("Select if you want to organise live evants and collaborate with an Event Reseller for their distribution.","wimtvpro") . '</div>
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