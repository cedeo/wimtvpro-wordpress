<?php

/**
 * Written by Netsense srl at 2016
 */

function user_settings_configuration($directory) {

//    $elencoSkin = array();
//    $uploads_info = wp_upload_dir();
//
//    if (!is_dir($directory)) {
//        $directory_create = mkdir($uploads_info["basedir"] . "/skinWim");
//    }
//    $elencoSkin[""] = "-- Base Skin --";
//    if (is_dir($directory)) {
//        if ($directory_handle = opendir($directory)) {
//            //Read directory for skin JWPLAYER
//            while (($file = readdir($directory_handle)) !== FALSE) {
//                if ((is_dir($directory . DIRECTORY_SEPARATOR . $file) && ($file != ".") && ($file != ".."))) {
//                    $elencoSkin[$file] = $file;
//                }
//
//            }
//            closedir($directory_handle);
//        }
//    }
//    //Create option select form Skin
//    $createSelect = "";
//    foreach ($elencoSkin as $key => $value) {
//        $createSelect .= "<option value='" . $key . "'";
//        if ($value == get_option("wp_nameSkin"))
//            $createSelect .= " selected='selected' ";
//        $createSelect .= ">" . $value . "</option>";
//    }
//
//
//    $uploads_info = wp_upload_dir();
   ?>
        
    <div class="wrap">
        <?php echo wimtvpro_link_help(); ?>

        <h2><?php _e('SETTINGS_pageTitle', "wimtvpro"); //_e("Configuration", "wimtvpro");     ?></h2>

        <?php
        $view_page = wimtvpro_alert_reg();
        $submenu = wimtvpro_submenu($view_page);
        echo $submenu;
        ?>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <div>
            <div class="empty"></div>
            <h4><?php _e("Connect to your account on WimTV", "wimtvpro"); ?></h4>

            <form enctype="multipart/form-data" action="<?php echo add_query_arg($_GET)?>" method="post" id="configwimtvpro-group" accept-charset="UTF-8">



	<div class="panel-group" id="accordion">
		<!-- FIRST PANEL -->
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title ">
					<a data-toggle="collapse" data-parent="#accordion" href="#panel-feeds-settings"><?php echo _e("Monetization","wimtvpro")?></a>
	                	</h3>
			</div>
			<div id="panel-feeds-settings" class="panel-collapse collapse">
		                <div class="panel-body"><?php settings_monetization($directory); ?></div>
            		</div>
	        </div>
		<!-- SECOND PANEL -->
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title ">
					<a data-toggle="collapse" data-parent="#accordion" href="#panel-feeds-settings1">Live</a>
	                	</h3>
			</div>
			<div id="panel-feeds-settings1" class="panel-collapse collapse">
		                <div class="panel-body"><?php settings_live($directory);?></div>
            		</div>
	        </div>
		<!-- THIRD PANEL -->
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title ">
					<a data-toggle="collapse" data-parent="#accordion" href="#panel-feeds-settings2"><?php _e("Personal Info","wimtvpro");?></a>
	                	</h3>
			</div>
			<div id="panel-feeds-settings2" class="panel-collapse collapse">
		                <div class="panel-body"><?php settings_personal($directory);?></div>
            		</div>
	        </div>
            <!-- FOURTH PANEL -->
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title ">
					<a data-toggle="collapse" data-parent="#accordion" href="#panel-feeds-settings3"><?php _e("Features","wimtvpro");?></a>
	                	</h3>
			</div>
			<div id="panel-feeds-settings3" class="panel-collapse collapse">
		                <div class="panel-body"><?php settings_features($directory);?></div>
            		</div>
	        </div>
	</div>

                <?php submit_button(__("Update", "wimtvpro")); ?>
            </form>
        </div>
<!--</div>-->
    </div>
    <?php
}
?>
