<?php
/*
Plugin Name: Wim Tv Pro
Plugin URI: http://wimtvpro.tv
Description: WimTVPro is the video plugin that adds several features to manage and publish video on demand, video playlists and stream live events on your website.
Version: 3.1.0
Author: WIMLABS
Author URI: http://www.wimlabs.com
License: GPLv2 or later
*/

/*  Copyright 2013  wimlabs  (email : riccardo@cedeo.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.
.j
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Create a term metadata table where $type = metadata type



include_once("database/db.php");
include_once("log/log.php");
include_once("hooks.php");
include_once("utils.php");
include_once("menu/pages/registration.php");
include_once("menu/pages/analytics.php");
include_once("menu/pages/playlist.php");
include_once("menu/pages/settings.php");
include_once("menu/pages/upload_video.php");
include_once("menu/pages/wimbox.php");
include_once("menu/pages/wimlive.php");
include_once("menu/pages/wimvod.php");
include_once("menu/pages/programming.php");
include_once("functions/registrationAlert.php");
include_once("functions/jwPlayer.php");
include_once("functions/updateWimVod.php");
include_once("functions/listDownload.php");
include_once("functions/optionCategories.php");
include_once("functions/detailShowtime.php");
include_once("embedded/embeddedPlayList.php");
include_once("embedded/embeddedProgramming.php");

load_plugin_textdomain( 'wimtvpro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

add_shortcode( 'streamingWimtv', 'wimtvpro_shortcode_streaming' );
add_shortcode( 'playlistWimtv', 'wimtvpro_shortcode_playlist' );
add_shortcode( 'wimvod', 'wimtvpro_shortcode_wimvod' );
add_shortcode( 'wimlive', 'wimtvpro_shortcode_wimlive' );
add_shortcode( 'wimprog', 'wimtvpro_shortcode_programming' );
/* What to do when the plugin is activated? */
register_activation_hook(__FILE__,'wimtvpro_install');
/* What to do when the plugin is deactivated? */
register_deactivation_hook( __FILE__, 'wimtvpro_remove');

function wimtvpro_install() {
	/* Create a new database field */
  	global $wpdb;
	
	 if (!function_exists('curl_init')){
      die('cURL non disponibile!');
  } 
	
	createTables();
}

function wimtvpro_setting() {
  register_setting('configwimtvpro-group', 'wp_registration');
  register_setting('configwimtvpro-group', 'wp_userwimtv');
  register_setting('configwimtvpro-group', 'wp_passwimtv');
  register_setting('configwimtvpro-group', 'wp_nameSkin');
  register_setting('configwimtvpro-group', 'wp_uploadSkin');  
  register_setting('configwimtvpro-group', 'wp_heightPreview');
  register_setting('configwimtvpro-group', 'wp_widthPreview');
  register_setting('configwimtvpro-group', 'wp_basePathWimtv');
  register_setting('configwimtvpro-group', 'wp_urlVideosWimtv');
  register_setting('configwimtvpro-group', 'wp_urlVideosDetailWimtv');
  register_setting('configwimtvpro-group', 'wp_urlThumbsWimtv');				
  register_setting('configwimtvpro-group', 'wp_urlEmbeddedPlayerWimtv');
  register_setting('configwimtvpro-group', 'wp_urlPostPublicWimtv');
  register_setting('configwimtvpro-group', 'wp_urlPostPublicAcquiWimtv');			
  register_setting('configwimtvpro-group', 'wp_urlSTWimtv');
  register_setting('configwimtvpro-group', 'wp_urlShowTimeWimtv');	
  register_setting('configwimtvpro-group', 'wp_urlShowTimeDetailWimtv');	
  register_setting('configwimtvpro-group', 'wp_urlUserProfileWimtv');
  register_setting('configwimtvpro-group', 'wp_replaceContentWimtv');
  register_setting('configwimtvpro-group', 'wp_replaceUserWimtv');
  register_setting('configwimtvpro-group', 'wp_replaceacquiredIdentifier');			
  register_setting('configwimtvpro-group', 'wp_replaceshowtimeIdentifier');
  register_setting('configwimtvpro-group', 'wp_sandbox');
  register_setting('configwimtvpro-group', 'wp_activeLive');
  register_setting('configwimtvpro-group', 'wp_activePayment');
  register_setting('configwimtvpro-group', 'wp_shareVideo');
  register_setting('profilewimtvpro-group', 'wp_name');
  register_setting('profilewimtvpro-group', 'wp_logo');
  register_setting('profilewimtvpro-group', 'wp_date');
  register_setting('profilewimtvpro-group', 'wp_email');
  register_setting('profilewimtvpro-group', 'wp_social');
  register_setting('configwimtvpro-group', 'wp_wimtvPluginPath');

  add_option( 'wp_registration','FALSE');
  add_option( 'wp_userwimtv','username');
  add_option( 'wp_passwimtv','password');
  add_option( 'wp_nameSkin','');
  add_option( 'wp_uploadSkin','');
  add_option( 'wp_heightPreview','280');
  add_option( 'wp_widthPreview','500');
  add_option( 'wp_name','si');
  add_option( 'wp_logo','si');
  add_option( 'wp_date','');
  add_option( 'wp_email','');
  add_option( 'wp_social','si');
  add_option( 'wp_sandbox','No');
  add_option( 'wp_publicPage','No');
  add_option( 'wp_shareVideo','No');
  add_option( 'wp_activePayment','false');
  add_option( 'wp_activeLive','false');
  add_option( 'wp_wimtvPluginPath', plugin_dir_url(__FILE__));
  add_option( 'wp_supportLink', 'http://support.wim.tv/?cat=5');
  add_option( 'wp_supportPage', 'http://support.wim.tv/?p=');
  
} 
add_action( 'admin_init', 'wimtvpro_setting');

function wimtvpro_remove() {
  dropTables();

  delete_option('wp_registration');
  delete_option('wp_userwimtv');
  delete_option('wp_passwimtv');
  delete_option('wp_nameSkin');
  delete_option('wp_uploadSkin');
  delete_option('wp_heightPreview');
  delete_option('wp_widthPreview');
  delete_option('wp_basePathWimtv');
  delete_option( 'wp_urlVideosWimtv');
  delete_option( 'wp_urlVideosDetailWimtv');
  delete_option( 'wp_urlThumbsWimtv');
  delete_option( 'wp_urlEmbeddedPlayerWimtv');
  delete_option( 'wp_urlPostPublicWimtv');
  delete_option( 'wp_urlPostPublicAcquiWimtv');
  delete_option( 'wp_urlSTWimtv');
  delete_option( 'wp_urlShowTimeWimtv');
  delete_option( 'wp_urlShowTimeDetailWimtv');
  delete_option( 'wp_urlUserProfileWimtv'); 
  delete_option( 'wp_replaceContentWimtv'); 
  delete_option( 'wp_replaceUserWimtv'); 
  delete_option( 'wp_replaceacquiredIdentifier');
  delete_option( 'wp_replaceshowtimeIdentifier'); 
  delete_option( 'wp_name');
  delete_option( 'wp_logo');
  delete_option( 'wp_date');
  delete_option( 'wp_email');
  delete_option( 'wp_social');
  delete_option( 'wp_publicPage');
  delete_option( 'wp_supportLink');
  delete_option( 'wp_supportPage');
  delete_option( 'wp_wimtvPluginPath');
  deleteWimTVPosts();
}

// End table for wimvideo pro


//menu admin
function wimtvpro_menu(){
    $user = wp_get_current_user();
    //For Admin
    if ($user->roles[0] == "administrator"){

      add_menu_page('WimTvPro', 'WimTvPro', 'administrator', 'WimTvPro', 'wimtvpro_configure', plugins_url('images/iconMenu.png', __FILE__), 6);      
		
      add_submenu_page('WimTvPro', __('Settings',"wimtvpro"),  __('Settings',"wimtvpro"), 'administrator', 'WimTvPro', 'wimtvpro_configure');
      
      if ((get_option("wp_registration")==FALSE) || ((get_option("wp_userwimtv")=="username") && get_option("wp_passwimtv")=="password")){
		  $registrationHidden = "";
      	add_submenu_page($registrationHidden, __('WimTV Registration',"wimtvpro"), __('WimTV Registration',"wimtvpro"), 'administrator', 'WimTvPro_Registration', 'wimtvpro_registration');
      }
	add_submenu_page('WimTvPro', __('Upload Video',"wimtvpro"), __('Upload Video',"wimtvpro"), 'administrator', 'WimTV_Upload', 'wimtvpro_upload');
      add_submenu_page('WimTvPro', 'WimBox', 'WimBox', 'administrator', 'WimBox', 'wimtvpro_wimbox');
      add_submenu_page('WimTvPro', 'WimVod', 'WimVod', 'administrator', 'WimVod', 'wimtvpro_mystreaming');
      
      add_submenu_page('WimTvPro', __('Playlist',"wimtvpro"), __('Playlist',"wimtvpro"), 'administrator', 'WimTV_Playlist', 'wimtvpro_playlist');
	  add_submenu_page('WimTvPro', 'WimLive', 'WimLive', 'administrator', 'WimLive', 'wimtvpro_live');
      add_submenu_page('WimTvPro', __('Analytics'), __('Analytics'), 'administrator', 'WimTVPro_Report', 'wimtvpro_Report');

      add_submenu_page('WimTvPro', __('Programmings',"wimtvpro"), __('Programmings',"wimtvpro"), 'administrator', 'WimVideoPro_Programming', 'wimtvpro_programming');
    }
    
    if ($user->roles[0]=="author") {
      add_menu_page('WimTvPro', 'WimVod', 'author', 'WimVideo', 'wimtvpro_mystreaming', plugins_url('images/iconMenu.png', __FILE__), 6);
    }
    if ($user->roles[0]=="editor") {
      add_menu_page('WimVod', 'WimVod', 'editor', 'WimVideo', 'wimtvpro_mystreaming', plugins_url('images/iconMenu.png', __FILE__), 6);
    }
}
add_action('admin_menu', 'wimtvpro_menu');
// END menu admin


// Attach video into post
function wimtvpro_media_menu($tabs) {
  $newtab = array('wimtvpro' => __('WimVod/Playlist', 'wimtvpro_insert'),
  					//'wimtvproLive' => __('Live', 'wimtvpro_insertLive')
				);
  return array_merge($tabs, $newtab);
}
add_filter('media_upload_tabs', 'wimtvpro_media_menu');
// End attach video into post


//Jquery and Css
add_action('init', 'wimtvpro_install_jquery');
function wimtvpro_install_jquery() {



  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-sortable');
  wp_enqueue_script('jquery-ui-datepicker');
  wp_enqueue_script('jwplayer', plugins_url('script/jwplayer/jwplayer.js', __FILE__));
 // wp_enqueue_script('jwplayerHtml5', plugins_url('script/jwplayer/jwplayer.html5.js', __FILE__));
  wp_enqueue_script('timepicker', plugins_url('script/timepicker/jquery.ui.timepicker.js', __FILE__));  
  wp_register_style( 'colorboxCss', plugins_url('script/colorbox/css/colorbox.css', __FILE__) );
  wp_enqueue_script('colorbox', plugins_url('script/colorbox/js/jquery.colorbox.js', __FILE__));
  wp_register_style( 'colorboxCss',plugins_url('script/colorbox/css/colorbox.css', __FILE__) );
  wp_enqueue_style('colorboxCss');
  wp_enqueue_script('jquery-ui-core');
  if (!is_admin()) {
    wp_register_style( 'wimtvproCss', plugins_url('css/wimtvpro_public.css', __FILE__) );
    wp_enqueue_style('wimtvproCss');
    
  } 
  else {
    wp_enqueue_script('swfObject', plugins_url('script/swfObject/swfobject.js', __FILE__));

    wp_register_style( 'wimtvproCss', plugins_url('css/wimtvpro.css', __FILE__) );
    wp_enqueue_style('wimtvproCss');
    wp_register_style('wimtvproCssCore',plugins_url('script/css/redmond/jquery-ui-1.8.21.custom.css', __FILE__));
    wp_enqueue_style('wimtvproCssCore');
 }


 if (isset($_GET['page']) && $_GET['page']!="WimVideoPro_Programming"){
 		wp_enqueue_script('wimtvproScript',plugins_url('script/wimtvpro.js', __FILE__));
 }

 if (isset($_GET['page']) && $_GET['page']=="WimTV_Upload"){
 	wp_enqueue_script('wimtvproScriptUpload',plugins_url('script/upload.js', __FILE__));
 }
}


function my_custom_js() {
    echo '<script type="text/javascript">
	
	var url_pathPlugin ="' . plugin_dir_url(__FILE__) . '";
	var titlePlaylistJs = "' . __("First, You must selected a playlist","wimtvpro") . '";
	var titlePlaylistSelectJs = "' . __("The video is insert into playlist selected!","wimtvpro") . '";
	var updateSuc = "' . __("Update successful","wimtvpro") . '";
	var refreshpage = "' . __("Refresh page for view video","wimtvpro") . '";
	var passwordReq = "' . __("The password is required","wimtvpro") . '";
	var selectCat = "' . __("You selected","wimtvpro") . '";
	var nonePayment = "' . __('You need compile fiscal information for your account, for enabling pay per view posting. Please provide it in Monetisation section of your Settings','wimtvpro') . '";
	var gratuito = "' . __('Do you want to publish your videos for free?','wimtvpro') . '";
	var messageSave = "' . __('Publish',"wimtvpro") . '";
	var update = "' . __('Update',"wimtvpro") . '";
	var videoproblem = "' . __('This video has not yet been processed, wait a few minutes and try to synchronize',"wimtvpro") . '";
	var videoPrivacy = new Array();
	 videoPrivacy[0] = "' . __('Select who can see the video',"wimtvpro") . '";
	 videoPrivacy[1] = "' . __('Everybody',"wimtvpro") . '";
	 videoPrivacy[2] = "' . __('Nobody (Administrators Only)',"wimtvpro") . '";
	 videoPrivacy[3] = "' . __('Where can anonymous viewers see the video (if you selected Everybody)?',"wimtvpro") . '";
	 videoPrivacy[4] = "' . __('Nowhere',"wimtvpro") . '";
	 videoPrivacy[5] = "' . __('Widget',"wimtvpro") . '";
	 videoPrivacy[6] = "' . __('Pages',"wimtvpro") . '";
	 videoPrivacy[7] = "' . __('Widget and Pages',"wimtvpro") . '";
	 videoPrivacy[8] = "' . __('Roles',"wimtvpro") . '";
	 videoPrivacy[9] = "' . __('Users',"wimtvpro") . '";
	 var erroreFile = new Array();
	 erroreFile[0] = "' . __('Please only upload files that end in types:',"wimtvpro") . '";
	 erroreFile[1] = "' . __('Please select a new file and try again.',"wimtvpro") . '";
	';
	 
	 $language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	 if (substr($language, 0, 2) == 'it') {
	   echo "var point =','";
	 } else {
	   echo "var point ='.'";
	 }
	 echo '
	</script>';
    /*echo '<script type="text/javascript">
    ProgUtils.endpoint="' . plugin_dir_url(__FILE__) . 'rest";
    ProgUtils.extension=".php";
    </script>';*/

}
// Add hook for admin <head></head>
add_action('admin_head', 'my_custom_js');
//End Jquery and Css


//Widget
class myStreaming extends WP_Widget {
function myStreaming() {
parent::__construct( false, 'Wimtv: WimVod' );
}
function widget( $args, $instance ) {
extract($args);
echo $before_widget;
$title = apply_filters( 'WimVod', $instance['title'] );
echo $before_widget;
if ( ! empty( $title ) )
		echo $before_title . $title . $after_title;

echo "<table class='itemsPublic'>" . wimtvpro_getVideos(TRUE, FALSE, FALSE, "block") . "</table>";
 
echo $after_widget;
}
function update( $new_instance, $old_instance ) {
$instance['title'] = strip_tags( $new_instance['title'] );

return $new_instance;
}
function form( $instance ) {
_e("Title");
$title = apply_filters( 'WimVod', $instance['title'] );
?>
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
  
<?php
}
}
class myPersonalDate extends WP_Widget {
function myPersonalDate () {
parent::__construct( false, 'Wimtv:' . __("Profile") );
}

function widget( $args, $instance ) {
    extract($args);
        $title = apply_filters( 'WimTV' . __("Profile"), $instance['title'] );
    echo $before_widget;
    if ( ! empty( $title ) )
        echo $before_title . $title . $after_title;
    // This example is adapted from node.module.

    $response = apiGetProfile();
    $arrayjsuser = json_decode($response);
    $profileuser= "";
    $namepage = "";
    if (get_option("wp_logo")=="si")
      $profileuser .= "<img src='" . $arrayjsuser ->imageLogoPath . "'>";
    if (get_option("wp_name")=="si"){
      if (isset($arrayjsuser->pageName)) $namepage .= "<p><b>" . $arrayjsuser->pageName . "</b><br/>" . $arrayjsuser->pageDescription . "</p>";
      else $namepage .= "<p><b>" . $arrayjsuser->username . "</b></p>";
    }
    $profileuser .= $namepage;
    if (get_option("wp_date")=="si")
      $profileuser .= "<p><br/>" . $arrayjsuser->name . " " . $arrayjsuser->surname . "<br/>" . $arrayjsuser->dateOfBirth . "<br/>" . $arrayjsuser->sex . "<br/>" . "</p>";
    if (get_option("wp_email")=="si")
      $profileuser .= "<p><b>" . __("Contact") . "</b><br/>" . $arrayjsuser->email . "<br/>";
    if (get_option("wp_social")=="si") {
      if (isset($arrayjsuser->linkedinURI))
        $profileuser .= "<a target='_new' href='" . $arrayjsuser->linkedinURI . "'><img src='" . plugins_url('images/linkedin.png', __FILE__) . "'></a>";
      if (isset($arrayjsuser->twitterURI))
        $profileuser .= "<a target='_new' href='" . $arrayjsuser->twitterURI . "'><img src='" . plugins_url('images/twitter.png', __FILE__) . "'></a>";
      if (isset($arrayjsuser->facebookURI))
        $profileuser .= "<a target='_new' href='" . $arrayjsuser->facebookURI . "'><img src='" . plugins_url('images/facebook.png', __FILE__) . "'></a>";
      $profileuser .= "</p>";
    }
    echo $profileuser;
    echo $after_widget;
}
function update( $new_instance, $old_instance ) {
var_dump($old_instance);
$instance['title'] = strip_tags( $new_instance['title'] );
update_option('wp_logo', $_POST['ImageLogoProfile']);
  update_option('wp_name', $_POST['pageNameProfile']);
  update_option('wp_date', $_POST['personalDateProfile']);
  update_option('wp_email', $_POST['EmailProfile']);
  update_option('wp_social', $_POST['SocialProfile']);
return $new_instance;
}

function form( $instance ) {

_e("Title");
$title = apply_filters( 'WimTV' . __("Profile"), $instance['title'] );
?>
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
  <p>
   <input type="checkbox" id="edit-imagelogoprofile" name="ImageLogoProfile" value="si" <?php if (get_option("wp_logo")=="si") echo 'checked="checked"'; ?> class="form-checkbox" />  <label class="option" for="edit-imagelogoprofile">Logo</label><br/>
   <input type="checkbox" id="edit-pagenameprofile" name="pageNameProfile" value="si" <?php if (get_option("wp_name")=="si") echo 'checked="checked"'; ?> class="form-checkbox" />  <label class="option" for="edit-pagenameprofile"><?php  _e("Page Name","wimtvpro"); ?></label><br/>
   <input type="checkbox" id="edit-personaldateprofile" name="personalDateProfile" value="si"  <?php if (get_option("wp_date")=="si") echo 'checked="checked"'; ?> class="form-checkbox" />  <label class="option" for="edit-personaldateprofile"><?php _e("Personal Info","wimtvpro"); ?></label><br/>
   <input type="checkbox" id="edit-emailprofile" name="EmailProfile" value="si"  <?php if (get_option("wp_email")=="si") echo 'checked="checked"'; ?> class="form-checkbox" /> <label class="option" for="edit-emailprofile">Email</label><br/>
   <input type="checkbox" id="edit-socialprofile" name="SocialProfile" value="si"  <?php if (get_option("wp_social")=="si") echo 'checked="checked"'; ?> checked="checked" class="form-checkbox" />  <label class="option" for="edit-socialprofile">Link Social</label>
   </p>
	   
<?php
}
}
function my_register_widgets() {
register_widget( "myPersonalDate" );
}

function my_register_widgets2() {
register_widget( "myStreaming" );
}

add_action( 'widgets_init', 'my_register_widgets' );
add_action( 'widgets_init', 'my_register_widgets2' );
//End Widget

//ShortCode player
function wimtvpro_shortcode_streaming($atts) {

  global $wpdb,$user;
  $user = wp_get_current_user();
  $idUser = $user->ID;
  $userRole = $user->roles[0];

  extract( shortcode_atts( array('id','width','height'), $atts ) );

  $arrayPlay = dbGetVideo($id);

  $view_video_state = $arrayPlay[0]->viewVideoModule;
  $stateView = explode ("|",$view_video_state);

  $array =  explode (",",$stateView[1]);
  $typeUser["U"] = array();
  $typeUser["R"] = array();
  $viewPublicVideo = FALSE;
  foreach ($array as $key=>$value) {
	$var = explode ("-",$value);
	if ($var[0]=="U") {
		array_push($typeUser["U"], $var[1]);
	}
	elseif ($var[0]=="R") {
		array_push($typeUser["R"], $var[1]);
	}
	else
		$typeUser[$var[0]] = "";
	if (($var[0]=="All") || ($var[0]=="")) {
		$viewPublicVideo = TRUE;
	}	
  }

  //Check if user is authorized to see video

  if ((($userRole=="administrator") || (in_array($idUser,$typeUser["U"])) || (in_array($userRole,$typeUser["R"])) || (array_key_exists("All",$typeUser)) || (array_key_exists ("",$typeUser)))){
	extract( shortcode_atts( array('id'=>$id,'width'=>$width,'height'=>$height), $atts ) );
	$credential = get_option("wp_userwimtv") . ":" . get_option("wp_passwimtv");

	if (get_option('wp_nameSkin')!="") {
	$uploads_info = wp_upload_dir();
	$directory =  $uploads_info["baseurl"] .  "/skinWim";

	$skin = "&skin=" . $directory  . "/" . get_option('wp_nameSkin') . ".zip";      
      }
      else
	$skin = "";

      $params = "get=1&width=" . $width . "&height=" . $height . $skin;

      $response = apiGetPlayerShowtime($id, $params);
	  wp_reset_query();
	  return "<div style='text-align:center'>" . $response . "</div>";
  } else {
	return "<p>You don't have permission to see the video</p>";
  }  
}

function wimtvpro_shortcode_playlist($atts) {
  $id = shortcode_atts(array('id'=>'all'), $atts);
  $id = $id['id'];
  return includePlaylist($id);
}

function wimtvpro_shortcode_wimvod($atts) {
	return "<table class='itemsPublic'>" . wimtvpro_getVideos(TRUE, FALSE, FALSE) . "</table><div class='clear'></div>";
}
function wimtvpro_shortcode_wimlive($atts) {
	$embeddedLive =  plugins_url('embedded/embeddedLive.php', __FILE__);
	$pageLive = "<script>jQuery(document).ready(function(){
    jQuery.ajax({
				context: this,
				url:  '" . $embeddedLive . "',
				type: 'GET',
				dataType: 'html',
				async: false,
				success: function(response) {
						jQuery('.entry-content').append(response);
				},
                });
    });
	</script>";
	return $pageLive;
}
function wimtvpro_shortcode_programming($atts) {
	$id = shortcode_atts(array('id'=>0), $atts);
  	$progId = $id['id'];
	return wimtvpro_programming_embedded($progId);
}


function wimtvpro_registration_script() {
	//PROGRAMMING SCRIPT
	$basePath = get_option("wp_basePathWimtv");
	$baseRoot = str_replace("rest/","",$basePath);
	wp_enqueue_style( 'calendarWimtv', $baseRoot . 'css/fullcalendar.css' );
	wp_enqueue_style( 'programmingWimtv', $baseRoot . 'css/programming.css' );
	wp_enqueue_style( 'jQueryWimtv', $baseRoot . 'css/jquery-ui/jquery-ui.custom.min.css' );
	wp_enqueue_style( 'fancyboxWimtv', $baseRoot . 'css/jquery.fancybox.css' );
	
	wp_enqueue_script('jquery.minWimtv', 'https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js');
	wp_enqueue_script('jquery.customWimtv', $baseRoot . 'script/jquery-ui.custom.min.js');
	wp_enqueue_script('jquery.fancyboxWimtv', $baseRoot . 'script/jquery.fancybox.min.js');
	wp_enqueue_script('jquery.mousewheelWimtv', $baseRoot . 'script/jquery.mousewheel.min.js');
	wp_enqueue_script('fullcalendarWimtv', $baseRoot . 'script/fullcalendar/fullcalendar.min.js');
	wp_enqueue_script('utilsWimtv', $baseRoot . 'script/utils.js');
	wp_enqueue_script('programmingWimtv', $baseRoot . 'script/programming/programming.js');
	wp_enqueue_script('calendarWimtv', $baseRoot . 'script/programming/calendar.js');
	wp_enqueue_script('programmingApi', plugins_url('script/programming-api.js', __FILE__));
}

if (isset($_GET['page']) && $_GET['page']=="WimVideoPro_Programming"){
	add_action( 'admin_init','wimtvpro_registration_script');
}

if (isset($_GET['page']) && $_GET['page']=="WimVideoPro_UploadVideo"){
	add_action( 'admin_footer','wimtvpro_uploadScript');
}



function wimtvpro_uploadScript() {
	wp_enqueue_script('jquery.preloadWidget', plugins_url('script/progressbar/jquery.ajax-progress.js', __FILE__));
	


}