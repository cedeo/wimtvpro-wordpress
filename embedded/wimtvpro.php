<?php
/*
  Plugin Name: WimTVPro for WP
  Plugin URI: http://wimtvpro.tv
  Description: WimTVPro is the video plugin that adds several features to manage and publish video on demand, video playlists and stream live events on your website.
  Version: 4.2.1
  Author: WimLabs
  Author URI: http://www.wimlabs.com
  License: GPLv2 or later
 */

/*  Copyright 2013-2016  WimLabs  (email : riccardo@wimlabs.com)

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
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
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

// NS:
include_once("functions/smartSync.php");

include_once("functions/detailShowtime.php");
include_once("embedded/embeddedPlayList.php");
include_once("embedded/embeddedProgramming.php");

define('WIMTV_BASEPATH', plugin_dir_path(__FILE__));

// NS: MOVED TO "wimtvpro_fix_missing_langs()"
//load_plugin_textdomain('wimtvpro', false, dirname(plugin_basename(__FILE__)) . '/languages/');
//Aggiunta shortcodes
add_shortcode('streamingWimtv', 'wimtvpro_shortcode_streaming');
add_shortcode('playlistWimtv', 'wimtvpro_shortcode_playlist');
add_shortcode('wimvod', 'wimtvpro_shortcode_wimvod');
add_shortcode('wimlive', 'wimtvpro_shortcode_wimlive');
add_shortcode('wimprog', 'wimtvpro_shortcode_programming');

// What to do when the plugin is activated
register_activation_hook(__FILE__, 'wimtvpro_install');

// What to do when the plugin is deactivated
register_deactivation_hook(__FILE__, 'wimtvpro_remove');


/* THIS FUNCTION CHECK WHETHER THE PLUGIN LANGUAGE FILE EXISTS
 * IN CASE IT DOESN'T EXIST: CREATE A COPY OF THE ENGLISH VERSION AND RENAME IT 
 * AS LOCALIZED FILE.
 * F.E.: If the CMS is set in french, the plugin will search for
 * "wimtvpro-fr_FR.mo" file. If it doesn't exist it creates a new file
 * named "wimtvpro-fr_FR.mo" by copying the content of "wimtvpro-en_US.mo"
 */
add_action('init', 'wimtvpro_fix_missing_langs');

function wimtvpro_install() {
    /* Create a new database field */
    global $wpdb;

    if (!function_exists('curl_init')) {
        die('cURL non disponibile!');
    }

    createTables();
    wimtvpro_fix_missing_langs();
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
    register_setting('configwimtvpro-group', 'wp_client_id');
    register_setting('configwimtvpro-group', 'wp_secret_key');
    register_setting('configwimtvpro-group', 'wp_access_token');
    register_setting('configwimtvpro-group', 'wp_refresh_token');
    register_setting('configwimtvpro-group', 'wp_public_access_token');

    add_option('wp_registration', 'FALSE');
    add_option('wp_userwimtv', 'username');
    add_option('wp_passwimtv', 'password');
    add_option('wp_nameSkin', '');
    add_option('wp_uploadSkin', '');
    add_option('wp_heightPreview', '280');
    add_option('wp_widthPreview', '500');
    add_option('wp_name', 'si');
    add_option('wp_logo', 'si');
    add_option('wp_date', '');
    add_option('wp_email', '');
    add_option('wp_social', 'si');
    add_option('wp_sandbox', 'No');
    add_option('wp_publicPage', 'No');
    add_option('wp_shareVideo', 'No');
    add_option('wp_activePayment', 'false');
    add_option('wp_activeLive', 'false');
    add_option('wp_wimtvPluginPath', plugin_dir_url(__FILE__));
    add_option('wp_supportLink', 'http://support.wim.tv/?cat=5');
    add_option('wp_supportPage', 'http://support.wim.tv/?p=');
    add_option('wp_client_id', 'wp');
    add_option('wp_secret_key', 'f6fd7549-5d2a-43e0-85bd-add81613dcd2');
    add_option('wp_access_token', '');
    add_option('wp_refresh_token', '');
    add_option('wp_public_access_token', '');
    // NS: WE SET HERE DEFAULT API BASE URL
    // IF WE CANNOT DETECT THE API BASE URL, JUST SET IT AS THE DEFAULT WIM.TV
    $wp_basePathWimtv = (__('API_URL', "wimtvpro") != 'API_URL') ? __('API_URL', "wimtvpro") : 'https://www.wim.tv/wimtv-webapp/rest/';
    update_option('wp_basePathWimtv', $wp_basePathWimtv);
    //    update_option('wp_basePathWimtv', 'https://www.wim.tv/wimtv-webapp/rest/');
    //    update_option('wp_basePathWimtv', __('API_URL', "wimtvpro"));
}

//Executes the wimtvpro_setting function on plugin activation
add_action('admin_init', 'wimtvpro_setting');

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
    delete_option('wp_urlVideosWimtv');
    delete_option('wp_urlVideosDetailWimtv');
    delete_option('wp_urlThumbsWimtv');
    delete_option('wp_urlEmbeddedPlayerWimtv');
    delete_option('wp_urlPostPublicWimtv');
    delete_option('wp_urlPostPublicAcquiWimtv');
    delete_option('wp_urlSTWimtv');
    delete_option('wp_urlShowTimeWimtv');
    delete_option('wp_urlShowTimeDetailWimtv');
    delete_option('wp_urlUserProfileWimtv');
    delete_option('wp_replaceContentWimtv');
    delete_option('wp_replaceUserWimtv');
    delete_option('wp_replaceacquiredIdentifier');
    delete_option('wp_replaceshowtimeIdentifier');
    delete_option('wp_name');
    delete_option('wp_logo');
    delete_option('wp_date');
    delete_option('wp_email');
    delete_option('wp_social');
    delete_option('wp_publicPage');
    delete_option('wp_supportLink');
    delete_option('wp_supportPage');
    delete_option('wp_wimtvPluginPath');
    delete_option('wp_client_id');
    delete_option('wp_secret_key');
    delete_option('wp_access_token');
    delete_option('wp_refresh_token');
    delete_option('wp_public_access_token');

    deleteWimTVPosts();
}

function wimtvpro_admin_notice() {
    if (isConnectedToTestServer()) {
        PRINT "<div class='isTestServer'>WARNING: WIMTV IS CONNECTED TO TEST SERVER!</div>";
    }
}

//menu admin
function wimtvpro_menu() {
    // SHOW ADMINISTRATOR NOTICES
    add_action('admin_notices', 'wimtvpro_admin_notice');
//    $user = wp_get_current_user();
    if (current_user_can("edit_others_posts")) {
        // ADMINISTRATOR AND EDITOR
        $menu_slug = __('SETTINGS_urlLink', "wimtvpro");
        add_menu_page(__('SETTINGS_menuLink', "wimtvpro"), __('APP_NAME', "wimtvpro"), "edit_others_posts", $menu_slug, 'wimtvpro_configure', plugins_url('images/iconMenu.png', __FILE__), 6);
        wimtvpro_menu_by_capability($menu_slug, "edit_others_posts");
    } else if (current_user_can("edit_posts")) {
        // AUTHOR AND CONTRIBUTOR
        $menu_slug = __('UPLOAD_urlLink', "wimtvpro");
        add_menu_page(__('UPLOAD_menuLink', "wimtvpro"), __('APP_NAME', "wimtvpro"), "edit_posts", $menu_slug, 'wimtvpro_upload', plugins_url('images/iconMenu.png', __FILE__), 6);
        wimtvpro_menu_by_capability($menu_slug, "edit_posts");
    }

    return;
    /*   // OLD 
      //For Admin
      if (current_user_can("administrator")) {
      //    if ($user->roles[0] == "administrator") {
      add_menu_page(__('SETTINGS_menuLink', "wimtvpro"), __('APP_NAME', "wimtvpro"), 'administrator', __('SETTINGS_urlLink', "wimtvpro"), 'wimtvpro_configure', plugins_url('images/iconMenu.png', __FILE__), 6);

      add_submenu_page(__('SETTINGS_urlLink', "wimtvpro"), __('SETTINGS_menuLink', "wimtvpro"), __('SETTINGS_menuLink', "wimtvpro"), 'administrator', __('SETTINGS_urlLink', "wimtvpro"), 'wimtvpro_configure');

      if ((get_option("wp_registration") == FALSE) || ((get_option("wp_userwimtv") == "username") && get_option("wp_passwimtv") == "password")) {
      $registrationHidden = "";
      add_submenu_page($registrationHidden, __('WimTV Registration', "wimtvpro"), __('WimTV Registration', "wimtvpro"), 'administrator', 'WimTvPro_Registration', 'wimtvpro_registration');
      }
      add_submenu_page(__('SETTINGS_urlLink', "wimtvpro"), __('UPLOAD_menuLink', "wimtvpro"), __('UPLOAD_menuLink', "wimtvpro"), 'administrator', __('UPLOAD_urlLink', "wimtvpro"), 'wimtvpro_upload');
      add_submenu_page(__('SETTINGS_urlLink', "wimtvpro"), __('WIMBOX_menuLink', "wimtvpro"), __('WIMBOX_menuLink', "wimtvpro"), 'administrator', __('WIMBOX_urlLink', "wimtvpro"), 'wimtvpro_wimbox');
      add_submenu_page(__('SETTINGS_urlLink', "wimtvpro"), __('WIMVOD_menuLink', "wimtvpro"), __('WIMVOD_menuLink', "wimtvpro"), 'administrator', __('WIMVOD_urlLink', "wimtvpro"), 'wimtvpro_mystreaming');

      add_submenu_page(__('SETTINGS_urlLink', "wimtvpro"), __('PLAYLIST_menuLink', "wimtvpro"), __('PLAYLIST_menuLink', "wimtvpro"), 'administrator', __('PLAYLIST_urlLink', "wimtvpro"), 'wimtvpro_playlist');
      add_submenu_page(__('SETTINGS_urlLink', "wimtvpro"), __('WIMLIVE_menuLink', "wimtvpro"), __('WIMLIVE_menuLink', "wimtvpro"), 'administrator', __('WIMLIVE_urlLink', "wimtvpro"), 'wimtvpro_live');


      add_submenu_page(__('SETTINGS_urlLink', "wimtvpro"), __('SCHEDULES_menuLink', "wimtvpro"), __('SCHEDULES_menuLink', "wimtvpro"), 'administrator', __('SCHEDULES_urlLink', "wimtvpro"), 'wimtvpro_programming');
      add_submenu_page(__('SETTINGS_urlLink', "wimtvpro"), __('ANALYTICS_menuLink', "wimtvpro"), __('ANALYTICS_menuLink', "wimtvpro"), 'administrator', __('ANALYTICS_urlLink', "wimtvpro"), 'wimtvpro_Report');
      } else if (current_user_can("editor")) {
      //    if ($user->roles[0] == "editor") {
      add_menu_page(__('WIMVOD_menuLink', "wimtvpro"), __('WIMVOD_menuLink', "wimtvpro"), 'editor', __('WIMVOD_urlLink', "wimtvpro"), 'wimtvpro_mystreaming', plugins_url('images/iconMenu.png', __FILE__), 6);
      }
      else if (current_user_can("author")) {
      //    if ($user->roles[0] == "author") {
      add_menu_page(__('WIMVOD_menuLink', "wimtvpro"), __('WIMVOD_menuLink', "wimtvpro"), 'author', __('WIMVOD_urlLink', "wimtvpro"), 'wimtvpro_mystreaming', plugins_url('images/iconMenu.png', __FILE__), 6);
      }
     */
}

function wimtvpro_menu_by_capability($parent_slug, $capability) {
    add_submenu_page($parent_slug, __('SETTINGS_menuLink', "wimtvpro"), __('SETTINGS_menuLink', "wimtvpro"), 'edit_others_posts', __('SETTINGS_urlLink', "wimtvpro"), 'wimtvpro_configure');
    if ((get_option("wp_registration") == FALSE) || ((get_option("wp_userwimtv") == "username") && get_option("wp_passwimtv") == "password")) {
        $registrationHidden = "";
//        add_submenu_page($registrationHidden, __('WimTV Registration', "wimtvpro"), __('WimTV Registration', "wimtvpro"), 'edit_others_posts', 'WimTvPro_Registration', 'wimtvpro_registration');
        add_submenu_page($registrationHidden, __('REGISTER_menuLink', "wimtvpro"), __('REGISTER_menuLink', "wimtvpro"), 'edit_others_posts', __('REGISTER_urlLink', "wimtvpro"), 'wimtvpro_registration');
    }
    add_submenu_page($parent_slug, __('UPLOAD_menuLink', "wimtvpro"), __('UPLOAD_menuLink', "wimtvpro"), $capability, __('UPLOAD_urlLink', "wimtvpro"), 'wimtvpro_upload');
    add_submenu_page($parent_slug, __('WIMBOX_menuLink', "wimtvpro"), __('WIMBOX_menuLink', "wimtvpro"), $capability, __('WIMBOX_urlLink', "wimtvpro"), 'wimtvpro_wimbox');
    add_submenu_page($parent_slug, __('WIMVOD_menuLink', "wimtvpro"), __('WIMVOD_menuLink', "wimtvpro"), $capability, __('WIMVOD_urlLink', "wimtvpro"), 'wimtvpro_mystreaming');

    add_submenu_page($parent_slug, __('PLAYLIST_menuLink', "wimtvpro"), __('PLAYLIST_menuLink', "wimtvpro"), $capability, __('PLAYLIST_urlLink', "wimtvpro"), 'wimtvpro_playlist');
    add_submenu_page($parent_slug, __('WIMLIVE_menuLink', "wimtvpro"), __('WIMLIVE_menuLink', "wimtvpro"), $capability, __('WIMLIVE_urlLink', "wimtvpro"), 'wimtvpro_live');

    add_submenu_page($parent_slug, __('SCHEDULES_menuLink', "wimtvpro"), __('SCHEDULES_menuLink', "wimtvpro"), $capability, __('SCHEDULES_urlLink', "wimtvpro"), 'wimtvpro_programming');
    add_submenu_page($parent_slug, __('ANALYTICS_menuLink', "wimtvpro"), __('ANALYTICS_menuLink', "wimtvpro"), $capability, __('ANALYTICS_urlLink', "wimtvpro"), 'wimtvpro_Report');



//    add_submenu_page(__('SETTINGS_urlLink', "wimtvpro"), __('SETTINGS_menuLink', "wimtvpro"), __('SETTINGS_menuLink', "wimtvpro"), "edit_others_posts", __('SETTINGS_urlLink', "wimtvpro"), 'wimtvpro_configure');
//    if ((get_option("wp_registration") == FALSE) || ((get_option("wp_userwimtv") == "username") && get_option("wp_passwimtv") == "password")) {
//        $registrationHidden = "";
//        add_submenu_page($registrationHidden, __('WimTV Registration', "wimtvpro"), __('WimTV Registration', "wimtvpro"), $capability, 'WimTvPro_Registration', 'wimtvpro_registration');
//    }
//    add_submenu_page(__('SETTINGS_urlLink', "wimtvpro"), __('UPLOAD_menuLink', "wimtvpro"), __('UPLOAD_menuLink', "wimtvpro"), $capability, __('UPLOAD_urlLink', "wimtvpro"), 'wimtvpro_upload');
//    add_submenu_page(__('SETTINGS_urlLink', "wimtvpro"), __('WIMBOX_menuLink', "wimtvpro"), __('WIMBOX_menuLink', "wimtvpro"), $capability, __('WIMBOX_urlLink', "wimtvpro"), 'wimtvpro_wimbox');
//    add_submenu_page(__('SETTINGS_urlLink', "wimtvpro"), __('WIMVOD_menuLink', "wimtvpro"), __('WIMVOD_menuLink', "wimtvpro"), $capability, __('WIMVOD_urlLink', "wimtvpro"), 'wimtvpro_mystreaming');
//
//    add_submenu_page(__('SETTINGS_urlLink', "wimtvpro"), __('PLAYLIST_menuLink', "wimtvpro"), __('PLAYLIST_menuLink', "wimtvpro"), $capability, __('PLAYLIST_urlLink', "wimtvpro"), 'wimtvpro_playlist');
//    add_submenu_page(__('SETTINGS_urlLink', "wimtvpro"), __('WIMLIVE_menuLink', "wimtvpro"), __('WIMLIVE_menuLink', "wimtvpro"), $capability, __('WIMLIVE_urlLink', "wimtvpro"), 'wimtvpro_live');
//
//
//    add_submenu_page(__('SETTINGS_urlLink', "wimtvpro"), __('SCHEDULES_menuLink', "wimtvpro"), __('SCHEDULES_menuLink', "wimtvpro"), $capability, __('SCHEDULES_urlLink', "wimtvpro"), 'wimtvpro_programming');
//    add_submenu_page(__('SETTINGS_urlLink', "wimtvpro"), __('ANALYTICS_menuLink', "wimtvpro"), __('ANALYTICS_menuLink', "wimtvpro"), $capability, __('ANALYTICS_urlLink', "wimtvpro"), 'wimtvpro_Report');
}

/* OLD menu admin
  //function wimtvpro_menu() {
  //    // SHOW ADMINISTRATOR NOTICES
  //    add_action('admin_notices', 'wimtvpro_admin_notice');
  //
  //    $user = wp_get_current_user();
  //    //For Admin
  //    if ($user->roles[0] == "administrator") {
  //
  //        add_menu_page('WimTvPro', 'WimTvPro', 'administrator', 'WimTvPro', 'wimtvpro_configure', plugins_url('images/iconMenu.png', __FILE__), 6);
  //
  //        add_submenu_page('WimTvPro', __('Settings', "wimtvpro"), __('Settings', "wimtvpro"), 'administrator', 'WimTvPro', 'wimtvpro_configure');
  //
  //        if ((get_option("wp_registration") == FALSE) || ((get_option("wp_userwimtv") == "username") && get_option("wp_passwimtv") == "password")) {
  //            $registrationHidden = "";
  //            add_submenu_page($registrationHidden, __('WimTV Registration', "wimtvpro"), __('WimTV Registration', "wimtvpro"), 'administrator', 'WimTvPro_Registration', 'wimtvpro_registration');
  //        }
  //        add_submenu_page('WimTvPro', __('Upload Video', "wimtvpro"), __('Upload Video', "wimtvpro"), 'administrator', 'WimTV_Upload', 'wimtvpro_upload');
  //        add_submenu_page('WimTvPro', 'WimBox', 'WimBox', 'administrator', 'WimBox', 'wimtvpro_wimbox');
  //        add_submenu_page('WimTvPro', 'WimVod', 'WimVod', 'administrator', 'WimVod', 'wimtvpro_mystreaming');
  //
  //        add_submenu_page('WimTvPro', __('Playlist', "wimtvpro"), __('Playlist', "wimtvpro"), 'administrator', 'WimTV_Playlist', 'wimtvpro_playlist');
  //        add_submenu_page('WimTvPro', 'WimLive', 'WimLive', 'administrator', 'WimLive', 'wimtvpro_live');
  //
  //        // NS: WE TEMPORARY HIDE THE PROGRAMMINGS SECTION
  //        add_submenu_page('WimTvPro', __('Programmings', "wimtvpro"), __('Programmings', "wimtvpro"), 'administrator', 'WimVideoPro_Programming', 'wimtvpro_programming');
  //        add_submenu_page('WimTvPro', __('Analytics'), __('Analytics'), 'administrator', 'WimTVPro_Report', 'wimtvpro_Report');
  //    }
  //
  //    if ($user->roles[0] == "author") {
  //        add_menu_page('WimTvPro', 'WimVod', 'author', 'WimVideo', 'wimtvpro_mystreaming', plugins_url('images/iconMenu.png', __FILE__), 6);
  //    }
  //    if ($user->roles[0] == "editor") {
  //        add_menu_page('WimVod', 'WimVod', 'editor', 'WimVideo', 'wimtvpro_mystreaming', plugins_url('images/iconMenu.png', __FILE__), 6);
  //    }
  //}
 */
// Adds the admin menu of the plugin
add_action('admin_menu', 'wimtvpro_menu');

// Adds the tab that allows users to insert videos and playlists in posts.
function wimtvpro_media_menu($tabs) {
    $newtab = array('wimtvpro' => __('WimVod/Playlist', 'wimtvpro_insert'),
            //'wimtvproLive' => __('Live', 'wimtvpro_insertLive')
    );
    return array_merge($tabs, $newtab);
}

add_filter('media_upload_tabs', 'wimtvpro_media_menu');


//Jquery and Css
add_action('init', 'wimtvpro_install_jquery');

function wimtvpro_install_jquery() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('jwplayer', plugins_url('script/jwplayer/jwplayer.js', __FILE__));
    // wp_enqueue_script('jwplayerHtml5', plugins_url('script/jwplayer/jwplayer.html5.js', __FILE__));
    wp_enqueue_script('timepicker', plugins_url('script/timepicker/jquery.ui.timepicker.js', __FILE__));
    wp_register_style('colorboxCss', plugins_url('script/colorbox/css/colorbox.css', __FILE__));
    wp_enqueue_script('colorbox', plugins_url('script/colorbox/js/jquery.colorbox.js', __FILE__));
    wp_register_style('colorboxCss', plugins_url('script/colorbox/css/colorbox.css', __FILE__));
    wp_enqueue_style('colorboxCss');
    wp_enqueue_script('jquery-ui-core');
    if (!is_admin()) {
        wp_register_style('wimtvproCss', plugins_url('css/wimtvpro_public.css', __FILE__));
        wp_enqueue_style('wimtvproCss');
    } else {
        wp_enqueue_script('swfObject', plugins_url('script/swfObject/swfobject.js', __FILE__));

        wp_register_style('wimtvproCss', plugins_url('css/wimtvpro.css', __FILE__));
        wp_enqueue_style('wimtvproCss');
        wp_register_style('wimtvproCssCore', plugins_url('script/css/redmond/jquery-ui-1.8.21.custom.css', __FILE__));
        wp_enqueue_style('wimtvproCssCore');
    }

    wp_enqueue_script('jstzScript', plugins_url('script/jstz-1.0.4.min.js', __FILE__));

//    if (isset($_GET['page']) && $_GET['page'] != "WimVideoPro_Programming") {
    if (isset($_GET['page']) && $_GET['page'] != __('SCHEDULES_urlLink', "wimtvpro")) {
        // Register the script first.
        wp_register_script('wimtvproScript', plugins_url('script/wimtvpro.js', __FILE__));
        // HERE
        wimtvpro_jsTranslations('wimtvproScript');
        wp_enqueue_script('wimtvproScript', plugins_url('script/wimtvpro.js', __FILE__));
    }

//    if (isset($_GET['page']) && $_GET['page'] == "WimTV_Upload") {
//    die(__('UPLOAD_urlLink', "wimtvpro"));
    if (isset($_GET['page']) && $_GET['page'] == __('UPLOAD_urlLink', "wimtvpro")) {
        wp_enqueue_script('wimtvproScriptUpload', plugins_url('script/upload.js', __FILE__));
    }
}

function my_custom_js() {
    echo '<script type="text/javascript">
	var url_pathPlugin ="' . plugin_dir_url(__FILE__) . '";
	var titlePlaylistJs = "' . addslashes(__("First, You must selected a playlist", "wimtvpro")) . '";
	var titlePlaylistSelectJs = "' . addslashes(__("The video is insert into playlist selected!", "wimtvpro")) . '";
	var updateSuc = "' . addslashes(__("Update successful", "wimtvpro")) . '";
	var refreshpage = "' . addslashes(__("Refresh page for view video", "wimtvpro")) . '";
	var passwordReq = "' . addslashes(__("The password is required", "wimtvpro")) . '";
	var selectCat = "' . addslashes(__("You selected", "wimtvpro")) . '";
	var nonePayment = "' . addslashes(__('You need compile fiscal information for your account, for enabling pay per view posting. Please provide it in Monetisation section of your Settings', 'wimtvpro')) . '";
	var gratuito = "' . addslashes(__('Do you want to publish your videos for free?', 'wimtvpro')) . '";
	var messageSave = "' . addslashes(__('Publish', "wimtvpro")) . '";
	var update = "' . addslashes(__('Update', "wimtvpro")) . '";
	var videoproblem = "' . addslashes(__('you can not download', "wimtvpro")) . '";
        var videodownloadno = "' . addslashes(__('This video has not yet been processed, wait a few minutes and try to synchronize', "wimtvpro")) . '";
	var videoPrivacy = new Array();
	 videoPrivacy[0] = "' . addslashes(__('Select who can see the video', "wimtvpro")) . '";
	 videoPrivacy[1] = "' . addslashes(__('Everybody', "wimtvpro")) . '";
	 videoPrivacy[2] = "' . addslashes(__('Nobody (Administrators Only)', "wimtvpro")) . '";
	 videoPrivacy[3] = "' . addslashes(__('Where can anonymous viewers see the video (if you selected Everybody)?', "wimtvpro")) . '";
	 videoPrivacy[4] = "' . addslashes(__('Nowhere', "wimtvpro")) . '";
	 videoPrivacy[5] = "' . addslashes(__('Widget', "wimtvpro")) . '";
	 videoPrivacy[6] = "' . addslashes(__('Pages', "wimtvpro")) . '";
	 videoPrivacy[7] = "' . addslashes(__('Widget and Pages', "wimtvpro")) . '";
	 videoPrivacy[8] = "' . addslashes(__('Roles', "wimtvpro")) . '";
	 videoPrivacy[9] = "' . addslashes(__('Users', "wimtvpro")) . '";
	 var erroreFile = new Array();
	 erroreFile[0] = "' . addslashes(__('Please only upload files that end in types:', "wimtvpro")) . '";
	 erroreFile[1] = "' . addslashes(__('Please select a new file and try again.', "wimtvpro")) . '";
	';

    $language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    if (substr($language, 0, 2) == 'it') {
        echo "var point =','";
    } else {
        echo "var point ='.'";
    }
    echo '
	</script>';
    /* echo '<script type="text/javascript">
      ProgUtils.endpoint="' . plugin_dir_url(__FILE__) . 'rest";
      ProgUtils.extension=".php";
      </script>'; */
}

// Add hook for admin <head></head>
add_action('admin_head', 'my_custom_js');

//End Jquery and Css
// NS: DISABLING WIDGETS
//Widgets
class myStreaming extends WP_Widget {

    function myStreaming() {
        parent::__construct(false, 'Wimtv: WimVod');
    }

    function widget($args, $instance) {
        wp_enqueue_script('wimtvproScript', plugins_url('script/wimtvpro.js', __FILE__));
        extract($args);
        echo $before_widget;
        $title = apply_filters('WimVod', $instance['title']);
        echo $before_widget;
        if (!empty($title))
            echo $before_title . $title . $after_title;

        echo "<table class='itemsPublic'>" . wimtvpro_getVideos(TRUE, FALSE, FALSE, "block") . "</table>";

        echo $after_widget;
    }

    function update($new_instance, $old_instance) {
        $instance['title'] = strip_tags($new_instance['title']);

        return $new_instance;
    }

    function form($instance) {
        _e("Title");
        $title = apply_filters('WimVod', $instance['title']);
        ?>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />

        <?php
    }

}

class myPersonalDate extends WP_Widget {

    function myPersonalDate() {
        parent::__construct(false, 'Wimtv:' . __("Profile"));
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('WimTV' . __("Profile"), $instance['title']);
        echo $before_widget;
        if (!empty($title))
            echo $before_title . $title . $after_title;
        // This example is adapted from node.module.

        $response = apiGetProfile();
        $arrayjsuser = json_decode($response);
        $profileuser = "";
        $namepage = "";
        if (get_option("wp_logo") == "si")
            $profileuser .= "<img src='" . $arrayjsuser->imageLogoPath . "'>";
        if (get_option("wp_name") == "si") {
            if (isset($arrayjsuser->pageName))
                $namepage .= "<p><b>" . $arrayjsuser->pageName . "</b><br/>" . $arrayjsuser->pageDescription . "</p>";
            else
                $namepage .= "<p><b>" . $arrayjsuser->username . "</b></p>";
        }
        $profileuser .= $namepage;
        if (get_option("wp_date") == "si")
            $profileuser .= "<p><br/>" . $arrayjsuser->name . " " . $arrayjsuser->surname . "<br/>" . $arrayjsuser->dateOfBirth . "<br/>" . $arrayjsuser->sex . "<br/>" . "</p>";
        if (get_option("wp_email") == "si")
            $profileuser .= "<p><b>" . __("Contact") . "</b><br/>" . $arrayjsuser->email . "<br/>";
        if (get_option("wp_social") == "si") {
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

    function update($new_instance, $old_instance) {
        var_dump($old_instance);
        $instance['title'] = strip_tags($new_instance['title']);
        update_option('wp_logo', $_POST['ImageLogoProfile']);
        update_option('wp_name', $_POST['pageNameProfile']);
        update_option('wp_date', $_POST['personalDateProfile']);
        update_option('wp_email', $_POST['EmailProfile']);
        update_option('wp_social', $_POST['SocialProfile']);
        return $new_instance;
    }

    function form($instance) {

        _e("Title");
        $title = apply_filters('WimTV' . __("Profile"), $instance['title']);
        ?>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        <p>
            <input type="checkbox" id="edit-imagelogoprofile" name="ImageLogoProfile" value="si" <?php if (get_option("wp_logo") == "si") echo 'checked="checked"'; ?> class="form-checkbox" />  <label class="option" for="edit-imagelogoprofile">Logo</label><br/>
            <input type="checkbox" id="edit-pagenameprofile" name="pageNameProfile" value="si" <?php if (get_option("wp_name") == "si") echo 'checked="checked"'; ?> class="form-checkbox" />  <label class="option" for="edit-pagenameprofile"><?php _e("Page Name", "wimtvpro"); ?></label><br/>
            <input type="checkbox" id="edit-personaldateprofile" name="personalDateProfile" value="si"  <?php if (get_option("wp_date") == "si") echo 'checked="checked"'; ?> class="form-checkbox" />  <label class="option" for="edit-personaldateprofile"><?php _e("Personal Info", "wimtvpro"); ?></label><br/>
            <input type="checkbox" id="edit-emailprofile" name="EmailProfile" value="si"  <?php if (get_option("wp_email") == "si") echo 'checked="checked"'; ?> class="form-checkbox" /> <label class="option" for="edit-emailprofile">Email</label><br/>
            <input type="checkbox" id="edit-socialprofile" name="SocialProfile" value="si"  <?php if (get_option("wp_social") == "si") echo 'checked="checked"'; ?> checked="checked" class="form-checkbox" />  <label class="option" for="edit-socialprofile">Link Social</label>
        </p>

        <?php
    }

}

function register_personal_date() {
    register_widget("myPersonalDate");
}

function register_my_streaming() {
    register_widget("myStreaming");
}


function wimtvpro_post_localStorage($value,$id) {

    echo '
        <script type="text/javascript">

        jQuery(document).ready(function() {
    ';


    echo 'var url_pathPlugin ="' . plugin_dir_url(__FILE__) . '";';

    echo '
var value = localStorage.getItem("'.$value.'")
   if (localStorage.getItem("infiniteScrollEnabled") === null) {
  //...
}
    ';
   
    echo '
var ourLocation = document.URL;';
	
    echo '
   
	//window.location.assign(window.location + "&timezone="+timezone);';
    // NS: We POST the param "cliTimezoneName" to let CMS server know the client timezone.
    echo '

	jQuery.ajax({
			context: this,
			url:  url_pathPlugin + "embedded/embedded_shortcode_streaming.php",
			type: "POST",
			dataType: "html",
			async: false,
			data: "a=1",
			success: function(response) {
';

 

    echo '
			},
	});
});
</script>
';
die;
  

    echo '
			},
	});
});
</script>
';
}
function wimtvpro_shortcode_streaming($atts) {

    if (cms_getWimtvUser() == "username") {
        return;
    }

  
   



    global $wp, $wpdb, $user;
    $uploads_info = wp_upload_dir();



    $user = wp_get_current_user();
    $idUser = $user->ID;
    $userRole = sizeof($user->roles) > 0 ? $user->roles[0] : "";
    $id = "";
    $width = "";
    $height = "";

    extract(shortcode_atts(array('id' => $id, 'width' => $width, 'height' => $height), $atts));

   
//    $details_video = apiGetDetailsShowtime($id);
    $details_video = apiGetDetailsShowtimePublic($id);
   
    $json_details = json_decode($details_video);

    $licenseType = $json_details->licenseType;
    $thumbnailId = $json_details->thumbnailId;
    $title = $json_details->title;
    $pricePerView = null;
    if(isset($json_details->pricePerView)){
        $pricePerView = $json_details->pricePerView;
    }
    $current_url = home_url(add_query_arg(array(), $wp->request));

    $response_jw = "";


    if ($licenseType == "FREE" || $licenseType == "CREATIVE_COMMONS") {
     
        $response = apiPlayWimVodItemPublic($id);
      return   $response_jw = configurePlayerJSByJson(json_decode($response), $width, $height,$id,$thumbnailId);
        
    }
 

        if ($licenseType == "PAY_PER_VIEW") {
// $response_jw = configurePlayerJSByJson(json_decode($response), $width, $height,$id,$thumbnailId);
// return  $response_jw;

              $res =   '<div id="pay_video'.$id.'" style="display:none;margin:0px 0px 10px 0px;" ><div id="videoPAYVod'.$id.'"  style="width:'.$width.'px;height:'.$height.'px;">'
             . '<img id="icon_play_vod'.$id.'" src="'.site_url().'/wp-content/plugins/wimtvpro/images/play.png" style="max-width:10%;z-index: 10;display: block;position: relative;top: 55%;left: 45%;" />'
            . '<img id="icon_thumb_play_vod'.$id.'" src="'.__("API_URL","wimtvpro").'asset/thumbnail/'.$thumbnailId.'" style="width:'.$width.'px;height:'.$height.'px;z-index: -10;" />'
           
            . '</div>'
                      . '</div>';

    
           $res .=  " <script> "
              
            ." if (localStorage.getItem('".$id."') === null) {"
                  
                   .'var url_pathPlugin ="' . plugin_dir_url(__FILE__) . '";'
                   ."var ourLocation = document.URL;"
               ."    jQuery.ajax({
			context: this,
			url:  url_pathPlugin + 'embedded'+'/embedded_shortcode_streaming.php',
			type: 'POST',
			dataType: 'html',
			async: false,
                        data : 'name_action=PAY&current_url='+ourLocation+'&id='+'".$id."&price=".$pricePerView."',"
                  .  "success: function(response) {"
                   . "var json = jQuery.parseJSON(response);"
                   . "var url = json.url;"
                   . "var trackingId = json.trackingId;"
                   ."localStorage.setItem('".$id."',trackingId);"
                   . "jQuery('div#pay_video".$id."').css('display','block');"
                   . "jQuery('img#icon_play_vod".$id."').click(function(){"
                   . "jQuery.colorbox({width: '400px',
                            height:'100px',
                             onComplete: function() {
                             jQuery(this).colorbox.resize();   
                              jQuery('a#paga_".$id."').attr('href',url);
                              },
                                onLoad: function() {
                                    jQuery('#cboxClose').remove();
                                   
                                },
                               
                            html:'<h2>" .__('Event fee','wimtvpro') .'</br>'. str_replace("'","\'",__("The event has a cost of","wimtvpro")).$pricePerView."€ </br>". "</h2><h2><a id=\"paga_".$id."\">Paga</a> | <a onClick=\"jQuery(this).colorbox.close();\" href=\"#\">" . "Cancel" . "</a></h2>'
                          
                             
                         });"
                 
                   . "});"
                  
                  
                        . "}"
                   ."});"
                   ."}else{"
                     ."var track = localStorage.getItem('".$id."');"
                     .'var url_pathPlugin ="' . plugin_dir_url(__FILE__) . '";'
                   ."var ourLocation = document.URL;"
               ."    jQuery.ajax({
			context: this,
			url: url_pathPlugin + 'embedded'+'/embedded_shortcode_streaming.php',
			type: 'POST',
			dataType: 'html',
			async: false,
                        data : 'name_action=PLAY&current_url='+ourLocation+'&thumbnailId=".$thumbnailId."&price=".$pricePerView."&height='+'".$height."&width='+'".$width."&id='+'".$id."&trackingId='+track".","
                  .  "success: function(response) {"
                                . "var json = jQuery.parseJSON(response);"
                                
                      .  "if(json.result === 'PLAY'){"
                                . "var res = json.res_html;"        
                      .  "jQuery('div#play_".$id."').html(res);"
                                . "}else{"
                                . ""
                                . ""
                                . "localStorage.removeItem('".$id."');"
                                ."localStorage.setItem('".$id."',json.trackingId);"
                                .  "jQuery('div#pay_video".$id."').css('display','block');"
                                 . "var url = json.url;"
                                 . "jQuery('img#icon_play_vod".$id."').click(function(){"
                   . "jQuery.colorbox({width: '400px',
                            height:'100px',
                             onComplete: function() {
                             jQuery(this).colorbox.resize();   
                              jQuery('a#paga_".$id."').attr('href',url);
                              },
                                onLoad: function() {
                                    jQuery('#cboxClose').remove();
                                   
                                },
                               
                            html:'<h2>" .__('Event fee','wimtvpro') .'</br>'. str_replace("'","\'",__("The event has a cost of","wimtvpro")).$pricePerView."€ </br>".  "</h2><h2><a id=\"paga_".$id."\">Paga</a> | <a onClick=\"jQuery(this).colorbox.close();\" href=\"#\">" . "Cancel" . "</a></h2>'
                           
                         });"
                   ." "
                   . "});"
                  
                     . "}"
                    . "}"
                   ."});"
                   . "}"
                   . '</script>';
                    
               return $res;         

    }
}

function wimtvpro_shortcode_playlist($atts) {
    if (cms_getWimtvUser() == "username") {
        return;
    }
    $shortcode_attributes = shortcode_atts(array('id' => 'all', 'width' => get_option('wp_widthPreview'), 'height' => get_option('wp_heightPreview')), $atts);
    $id = $shortcode_attributes['id'];
    $width = $shortcode_attributes['width'];
    $height = $shortcode_attributes['height'];
    return includePlaylist($id, $width, $height);
}

function wimtvpro_shortcode_wimvod($atts) {
    if (cms_getWimtvUser() == "username") {
        return;
    }
    return "<table class='itemsPublic'>" . wimtvpro_getVideos(TRUE, FALSE, FALSE) . "</table><div class='clear'></div>";
}

function wimtvpro_shortcode_wimlive($atts) {

    if (cms_getWimtvUser() == "username") {
        return;
    }

    
    global $wp;
    $uploads_info = wp_upload_dir();
    $directoryCookie = $uploads_info["basedir"] . "/cookieWim";

    if (!is_dir($directoryCookie)) {
        $directory_create = mkdir($uploads_info["basedir"] . "/cookieWim");
    }
    $id_random = null;
    if(isset($_GET['return_success'])){
        $id_random = $_GET['return_success'];
       
    }else{
         $id_random = rand();
    }
    
  
    $fileCookie = "cookies_" . get_option("wp_userWimtv") . "_" . "WimLive" .$id_random. ".txt";


    $params = "";
    $pageLive = "";
    // WE ARE GETTING A SHORTAG LIKE: [wimlive id='urn:wim:tv:livestream:c9309ad5-6cce-4f20-b9aa-552efe858fe4' zone='3600000']
//    if (isset($atts['id']) && isset($atts['zone'])) {
    if (isset($atts['id'])) {
        $identifier = $atts["id"];
//        $timezone s= $atts["zone"];
        $width = $atts["width"];
        $height = $atts["height"];
        $timezone = $atts["timezone"];
//        $skin = "";
//        if (get_option('wp_nameSkin') != "") {
//            $uploads_info = wp_upload_dir();
//            $directory = $uploads_info["baseurl"] . "/skinWim";
//
//            $nomeFilexml = wimtvpro_searchFile($uploads_info["basedir"] . "/skinWim/" . get_option('wp_nameSkin') . "/wimtv/", "xml");
//            $skin = "&skin=" . $directory . "/" . get_option('wp_nameSkin') . "/wimtv/" . $nomeFilexml;
//        }
//        $params = "timezone=" . $timezone;
//        if ($skin != "") {
//            $params.="&amp;skin=" . $skin;
//        }


        $insecureMode = "&insecureMode=on";
        $skin = "";
        $logo = "";
        // A SKIN HAS BEEN ADDED: OVERRIDE DEFAULT SKIN PATH
        $skinData = wimtvpro_get_skin_data();
        if ($skinData['styleUrl'] != "") {
            $skin = "&skin=" . htmlentities($skinData['styleUrl']);
        }

        if ($skinData['logoUrl'] != "") {
            $logo = "&logo=" . htmlentities($skinData['logoUrl']);
        }


//        if (isset($_GET['return_success'])) {
//
//           $f = fopen($directoryCookie . "/" . $fileCookie, "r");
//
//               $trackingId = fread($f,36);
//             
//               fclose($f);
////              $params = array( 
////          'trackingId' => $trackingId
////            );
//              
//            $pageLive = configurePlayerJSForLive($identifier, null, $width, $height,$trackingId);
//        }

//        if (isset($_GET['return_error'])) {
//
//            $pageLive = __("Error payement", "wimtvpro");
//        }
//        if (isset($_GET['payement_deny'])) {
//
//            $pageLive = __("Impossible to see the video","wimtvpro");
//        }


//        $params .="&width=$width&height=$height&timezone=" . $timezone . $insecureMode . $skin . $logo;

//        if (!isset($_GET['return_success']) && !isset($_GET['return_error'])) {
            $params = array(
                "channelId" => $identifier,
                "pageSize" => "20",
                "pageIndex" => "0"
            );


//            $response = apiSearchLiveEventsPublic($params, $timezone);
//            
//            $json_events = json_decode($response);
//            var_dump("QUAAA2",$json_events);
//            $thumbnailId = $json_events->thumbnailId;
//            $eventId = null;
//            $paymentMode = null;
//            foreach ($json_events->items as $event) {
////        var_dump($event->paymentMode,$event->onAir,$event->eventId);
//      
//                if ($event->onAir) {
//                    $eventId = $event->eventId;
//                    $paymentMode = $event->paymentMode;
//                }
//            }
//            if (isset($eventId)) {
            
        $response = apiPlayOnAirLiveEventInChannels($identifier);
        $array_json = json_decode($response);
//       
        $eventId = $array_json->resource->eventId;
    
        if($array_json->result == "PAYMENT_REQUIRED"){
           
//              $current_url = home_url(add_query_arg(array(), $wp->request));
//
//                    $params_pay = array(
//                        "embedded" => false,
//                        "mobile" => false,
//                        "returnUrl" => $current_url . "/?return_success=".$id_random,
//                        "cancelUrl" => $current_url . "/?return_error=false"
//                    );
//              
//                    $response_pay = apiPayForPlayLiveEventPublic($eventId, $params_pay);
//
//                    $response_json_pay = json_decode($response_pay);
//              
            $pricePerView = $array_json->resource->pricePerView;
    echo "<div id='play_".$identifier."'></div>";
              echo   '<div id="pay_video'.$eventId.'" style="display:none;"><div id="videoPAYVod"  style="width:'.$width.'px;height:'.$height.'px;">'
                 . '<div id="pay_video'.$eventId.'"  style="width:'.$width.'px;height:'.$height.'px;">'
             . '<img id="icon_play'.$eventId.'" src="'.site_url().'/wp-content/plugins/wimtvpro/images/play.png" style="max-width:10%;z-index: 10;display: block;position: relative;top: 55%;left: 45%;" />'
            . '<img id="icon_thumb_play'.$identifier.'" src="'.site_url().'/wp-content/plugins/wimtvpro/images/background.jpg" style="width:'.$width.'px;height:'.$height.'px;z-index: -10;" />'
           
            . '</div>';
//            wimtvpro_post_localStorage($id,$id);die;
           echo "</div>";
           echo" <script> "
              
            ." if (localStorage.getItem('".$eventId."') === null) {"
                  
                   .'var url_pathPlugin ="' . plugin_dir_url(__FILE__) . '";'
                   ."var ourLocation = document.URL;"
               ."    jQuery.ajax({
			context: this,
			url:  url_pathPlugin + 'embedded'+'/embedded_shortcode_live.php',
			type: 'POST',
			dataType: 'html',
			async: false,
                        data : 'name_action=PAY&current_url='+ourLocation+'&id='+'".$eventId."&price=".$pricePerView."',"
                  .  "success: function(response) {"
                   . "var json = jQuery.parseJSON(response);"
                   . "var url = json.url;"
                   . "var trackingId = json.trackingId;"
                   ."localStorage.setItem('".$eventId."',trackingId);"
                   . "jQuery('div#pay_video".$eventId."').css('display','inline-block');"
                   . "jQuery('img#icon_play".$eventId."').click(function(){"
                   . "jQuery.colorbox({width: '400px',
                            height:'100px',
                             onComplete: function() {
                             jQuery(this).colorbox.resize();   
                              jQuery('a#paga_".$eventId."').attr('href',url);
                              },
                                onLoad: function() {
                                    jQuery('#cboxClose').remove();
                                   
                                },
                               
                            html:'<h2>" .__('Event fee','wimtvpro') .'</br>'. str_replace("'","\'",__("The event has a cost of","wimtvpro")).$pricePerView."€ </br>". "</h2><h2><a id=\"paga_".$eventId."\">".__("Pay to Paypal","wimtvpro")."</a> | <a onClick=\"jQuery(this).colorbox.close();\" href=\"#\">" . __("Cancel","wimtvpro") . "</a></h2>'
                          
                             
                         });"
                   ." "
                   . "});"
                   . ""
                  
          ;
//              
                        echo "}"
                   ."});"
                   ."}else{"
                     ."var track = localStorage.getItem('".$eventId."');"
                     .'var url_pathPlugin ="' . plugin_dir_url(__FILE__) . '";'
                   ."var ourLocation = document.URL;"
               ."    jQuery.ajax({
			context: this,
			url: url_pathPlugin + 'embedded'+'/embedded_shortcode_live.php',
			type: 'POST',
			dataType: 'html',
			async: false,
                        data : 'name_action=PLAY&current_url='+ourLocation+'&timezone=".$timezone."&price=".$pricePerView."&height='+'".$height."&width='+'".$width."&channelId='+'".$identifier."&id='+'".$eventId."&trackingId='+track".","
                  .  "success: function(response) {"
                                . "var json = jQuery.parseJSON(response);"
                                
                      .  "if(json.result === 'PLAY'){"
                                . "var res = json.res_html;"        
                      .  "jQuery('div#play_".$identifier."').html(res);"
                                . "}else{"
                                . ""
                                . ""
                                . "localStorage.removeItem('".$eventId."');"
                                ."localStorage.setItem('".$eventId."',json.trackingId);"
                                .  "jQuery('div#pay_video".$eventId."').css('display','inline-block');"
                                 . "var url = json.url;"
                                 . "jQuery('img#icon_play".$eventId."').click(function(){"
                   . "jQuery.colorbox({width: '400px',
                            height:'100px',
                             onComplete: function() {
                             jQuery(this).colorbox.resize();   
                              jQuery('a#paga_".$eventId."').attr('href',url);
                              },
                                onLoad: function() {
                                    jQuery('#cboxClose').remove();
                                   
                                },
                               
                            html:'<h2>" .__('Event fee','wimtvpro') .'</br>'. str_replace("'","\'",__("The event has a cost of","wimtvpro")).$pricePerView."€ </br>" . "</h2><h2><a id=\"paga_".$eventId."\">".__("Pay to Paypal","wimtvpro")."</a> | <a onClick=\"jQuery(this).colorbox.close();\" href=\"#\">" . __("Cancel","wimtvpro") . "</a></h2>'
                          
                             
                         });"
                   ." "
                   . "});"
                  
                                . "}"
                           
                  
         
            
                  
                   . ""
                  
          ;
                
                        echo "}"
                   ."});"
                   . "}"
                   . '</script>';
        


                } else if($array_json->result == "PLAY"){
                
     
                    $pageLive = configurePlayerJSForLive($identifier,$array_json, $width, $height);
                }

    }

    return $pageLive;
}

function wimtvpro_shortcode_programming($atts) {
    if (cms_getWimtvUser() == "username") {
        return;
    }
    $id = "";
    $width = "";
    $height = "";
    extract(shortcode_atts(array('id' => $id, 'width' => $width, 'height' => $height), $atts));

    $skinData = wimtvpro_get_skin_data();
    $skinStyle = "";
    $skinLogo = "";
    if ($skinData['styleUrl'] != "") {
        $skinStyle = $skinData["styleUrl"];
    }

    if ($skinData['logoUrl'] != "") {
        $skinLogo = $skinData['logoUrl'];
    }

    $height = ($height != null) ? $height : get_option("wp_heightPreview") + 100;
    $width = ($width != null) ? $width : get_option("wp_widthPreview");

    $parameters = "";
    $parameters.="width=" . $width;
    $parameters.="&height=" . $height;
    $parameters.="&insecureMode=on";
    $parameters.="&skin=" . $skinStyle;
    $parameters.="&logo=" . $skinLogo;

    return $iframe = apiProgrammingPlayer($id, $parameters);
}

// JS scripts for specific pages
function wimtvpro_registration_script() {
    //PROGRAMMING SCRIPT
    $basePath = get_option("wp_basePathWimtv");
    $baseRoot = str_replace("rest/", "", $basePath);
    wp_enqueue_style('calendarWimtv', $baseRoot . 'css/fullcalendar.css');
    wp_enqueue_style('programmingWimtv', $baseRoot . 'css/programming.css');
    wp_enqueue_style('jQueryWimtv', $baseRoot . 'css/jquery-ui/jquery-ui.custom.min.css');
    wp_enqueue_style('fancyboxWimtv', $baseRoot . 'css/jquery.fancybox.css');

    wp_enqueue_script('jquery.minWimtv', 'https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js');
    wp_enqueue_script('jquery.customWimtv', $baseRoot . 'script/jquery-ui.custom.min.js');
    wp_enqueue_script('jquery.fancyboxWimtv', $baseRoot . 'script/jquery.fancybox.min.js');
    wp_enqueue_script('jquery.mousewheelWimtv', $baseRoot . 'script/jquery.mousewheel.min.js');
    wp_enqueue_script('fullcalendarWimtv', $baseRoot . 'script/fullcalendar/fullcalendar.min.js');
    wp_enqueue_script('utilsWimtv', $baseRoot . 'script/utils.js');
//    wp_enqueue_script('programmingWimtv', $baseRoot . 'script/programming/programming.js');
//    wp_enqueue_script('calendarWimtv', $baseRoot . 'script/programming/calendar.js');
//    wp_enqueue_script('programmingApi', plugins_url('script/programming-api.js', __FILE__));
}

if (isset($_GET['page']) && $_GET['page'] == "WimVideoPro_Programming") {
    add_action('admin_init', 'wimtvpro_registration_script');
}

if (isset($_GET['page']) && $_GET['page'] == "WimVideoPro_UploadVideo") {
    add_action('admin_footer', 'wimtvpro_uploadScript');
}

function wimtvpro_uploadScript() {
    wp_enqueue_script('jquery.preloadWidget', plugins_url('script/progressbar/jquery.ajax-progress.js', __FILE__));
}

function wimtvpro_jsTranslations($jsHandle) {
    // Now we can localize the script with our data.
    $translation_array = array(
        'remove_video_confirm_message' => __("You are removing video with title:\n\n\t__TITLE__\n\nAre you sure ?"),
    );
    wp_localize_script($jsHandle, 'WP_TRANSLATE', $translation_array);
}

/**
 * Returns current plugin version.
 * 
 * @return string Plugin version
 */
function wimtvpro_get_info() {
    if (!function_exists('get_plugins')) {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }
    $plugin_folder = get_plugins('/' . plugin_basename(dirname(__FILE__)));
    $plugin_file = basename(( __FILE__));
    return $plugin_folder[$plugin_file];
}

function wimtvpro_fix_missing_langs() {//
    $langFolder = dirname(__FILE__) . "/languages/";
    $locale = get_locale();
    $moFile = $langFolder . "wimtvpro-" . $locale . ".mo";
    if (!file_exists($moFile)) {
        $engMoFile = $langFolder . "wimtvpro-en_US.mo";
        //str_replace($locale, "en_US", $moFile);
        copy($engMoFile, $moFile);
    }
    load_plugin_textdomain('wimtvpro', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}