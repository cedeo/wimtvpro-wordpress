<?php
  /**
  * @file
  * This file is use for the view list video (page Wimbox and WimVod).
  *
  */
$view_page = wimtvpro_alert_reg();
	
if ($view_page==TRUE){
	switch ($paged) {
		
		case "wimbox":
	
			echo ' <script type="text/javascript"> jQuery(document).ready(function(){
			
			jQuery("a.viewThumb").click( function(){
			var url = jQuery(this).attr("id");
			jQuery(this).colorbox({href:url});
			});
			
			jQuery("a.wimtv-thumbnail").click( function(){
			if( jQuery(this).parent().children(".headerBox").children(".icon").children("a.viewThumb").length  ) {
				var url = jQuery(this).parent().children(".headerBox").children(".icon").children("a.viewThumb").attr("id");
				jQuery(this).colorbox({href:url});
			}
			});
			
			}); 
			
			
			</script>';
	
		
			if ($_POST['titleVideo']!="") $sql_where  = " AND title LIKE '%" . $_POST['titleVideo'] . "%' ";
	
			if ($_POST['ordertitleVideo']!="") {
				$sql_order  = " title " . $_POST['ordertitleVideo'];
				$_POST['orderdateVideo'] = "";
			}
			
			if ($_POST['orderdateVideo']!="") {
				$_POST['ordertitleVideo'] = "";
				$sql_order  .= " mytimestamp " . $_POST['orderdateVideo'];
			}
			
			echo " <div class='wrap'><h2>WimBox</h2>";
			echo "<p>" . __("Here you find all videos you have uploaded. If you wish to post one of these videos on your site, move it to WimVod by clicking the corresponding icon","wimtvpro") . "</p>";
			$title = "<div class='action'><span class='icon_sync0 button-primary' title='Synchronize'>" . __("Synchronize","wimtvpro") . "</span></div>";
			echo $title;
	
			$videos= wimtvpro_getThumbs(FALSE,TRUE,FALSE,'',$sql_where, $sql_order);
			
			if ($videos!="") $display= "style:display:none;";
			
			echo '<form ' . $display  . ' id="formVideo" method="post" action="#">';
			echo '<b>' . __("Search") . '</b><label for="title">' . "  " . __("video title","wimtvpro") . ":</label><input type='text' value='" . $_POST['titleVideo'] . "' name='titleVideo' />";
			//echo ' - <label for="title">Search to DATE: </label><input type="text" value="' . $_POST['titleVideo'] . '" name="titleVideo" />';
			echo '<input type="submit" class="button button-primary" value="' . __("Search") . '">';
	
			echo '<br/><br/><b>' . __("Order","wimtvpro")  . '</b><label for="title">' . "  " . __("by title","wimtvpro") . ':</label><select name="ordertitleVideo">
			<option value=""';
			if ($_POST['ordertitleVideo']=="") echo ' selected="selected" ';
				echo '	>---</option><option value="ASC"';
	
			if ($_POST['ordertitleVideo']=="ASC") echo ' selected="selected" ';
				echo '>ASC</option>
						<option value="DESC"';
				if ($_POST['ordertitleVideo']=="DESC") echo ' selected="selected" ';
				echo ">" . __("DESC","wimtvpro") . "</option>
				</select>";

			echo '<input type="submit" class="button button-primary" value="' . __("Order","wimtvpro") . '">';
			echo '</form>';
		
			 echo "<table  " . $display  . " id='FALSE' class='items wp-list-table widefat fixed pages'>";
			echo "<thead><tr style='width:100%'><th  style='width:30%'>Video</th><th style='width:30%'>" . __("Posted","wimtvpro") . "</th><th style='width:30%'>Download</th><th style='width:20%'>Preview</th><th style='width:20%'>" . __("Remove") . "</th></tr></thead>";
			echo "<tbody>";
	
			echo $videos;
			echo "</tbody></table>";

		
			echo "<div class='loaderTable'></div></div>";
			
			echo '<script>
				
				jQuery(".box_search").click(function(){
					jQuery(".search2").fadeToggle();
					
					if (jQuery(".search2").css("opacity") == 0) jQuery(".box_search").html("' . __("Close") . '");
					else jQuery(".box_search").html("' . __("Search") . '");
		
				});
			
			</script>';
		
		break;
	
		case "wimvod":
		
			echo ' <script type="text/javascript">
				jQuery(document).ready(function(){ 
	
				  /*SORTABLE*/      						
				  jQuery( ".items tbody" ).sortable({
					  placeholder: "ui-state-highlight",
					  handle : ".icon_moveThumbs",	
					  
					  out: function( event, ui ) {
							var ordina =	jQuery(".items tbody").sortable("toArray") ;
							
							jQuery.ajax({
								context: this,
								url:  url_pathPlugin + "scripts.php",
								type: "GET",
								dataType: "html",
								data: "namefunction=ReSortable&ordina=" + ordina, 
								error: function(request,error) {
									alert(request.responseText); 
								}	
							});
					  }
				
				  });
			  });	
				jQuery(document).ready(function(){
						   
				   jQuery("a.viewThumb").click( function(){
					var url = jQuery(this).attr("id");
					jQuery(this).colorbox({href:url});
				   });
				   jQuery("a.wimtv-thumbnail").click( function(){
					  if( jQuery(this).parent().children(".headerBox").children(".icon").children("a.viewThumb").length  ) {
						var url = jQuery(this).parent().children(".headerBox").children(".icon").children("a.viewThumb").attr("id");
						jQuery(this).colorbox({href:url});
					  }
					});
				 }); 
	    
			</script>';
		
			echo "<div class='wrap'><h2>WimVod</h2>";
			echo "<p>" . __("Here you can","wimtvpro") . " " . __("Manage the videos you want to publish, both in posts and widgets","wimtvpro") . "</p>";
			$title = "<div class='action'>
			<span class='icon_sync0 button-primary' title='Synchronize'>" . __("Synchronize","wimtvpro")  . "</span>";
			$user = wp_get_current_user();
			$idUser = $user->ID;
			$userRole = $user->roles[0];
			/*if ($user->roles[0] == "administrator"){
			  $title .= "<span class='icon_save' id='save'>" . __("Save") . "</span>";
			}*/
			echo   $title . "</div>
				<div id='post-body' class='metabox-holder columns-2'>
					<div id='post-body-content'>"; 
						
			
			
			echo "<table  id='TRUE' class='items wp-list-table widefat fixed pages'>";
				echo "<thead><tr style='width:100%'><th  style='width:20%'>Video</th><th style='width:15%'>" . __("Posted","wimtvpro") . "</th><th style='width:20%'>" . __("Change position","wimtvpro") . "</th><th style='width:20%'>Privacy</th><th style='width:20%'>Download</th><th style='width:15%'>" . __("Preview") . "</th><th></th></tr></thead>";
				echo "<tbody>";
	
				echo wimtvpro_getVideos(TRUE);
				echo "</tbody></table><div class='loaderTable'></div>";
			
			echo "</div>
				</div>";
		
		break;

	}
}