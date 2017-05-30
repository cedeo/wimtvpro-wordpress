<?php
/*
Plugin Name: WimTVPro
Plugin URI: http://wimtvpro.tv
Description: WimTVPro is the video plugin that adds several features to manage and publish video on demand, video schedules and stream live events on your website.
Version: 10.1.0
Author: WimLabs
Author URI: http://www.wimlabs.com
License: GPLv2 or later
*/
/*  Copyright 2013-2017  WimLabs  (email : riccardo@wimlabs.com)

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

add_action('admin_menu', 'wimtvpro_menu');
add_action('admin_post_wimtvpro_config', 'wimtvpro_config' );

include_once "wimtv/login.php";

function wimtvpro_menu() {
	add_menu_page('WimTVPro Administration', 'WimTVPro', 'manage_options', 'wimtv-pro','wimtv_pro', plugin_dir_url(__FILE__) . 'wimtv/private/assets/img/favicon.ico');
	add_submenu_page('wimtv-pro', 'WimTVPro', 'Dashboard', 'manage_options', 'wimtv-pro', 'wimtv_pro');
	add_submenu_page('wimtv-pro', 'Configuration settings', 'Settings', 'manage_options', 'wimtvpro_settings', 'wimtvpro_settings');
}


function wimtv_pro() {
	if(fail_version_check()) {
		fail_version();
	} else {
		include_once "wimtv/index.php";
	}
}

function wimtvpro_settings () {
	if(fail_version_check()) {
		fail_version();
	} else {
		include_once "wimtv/config.php";
	}
}

function wimtvpro_config() {
	update_option("wimtvpro_username", $_REQUEST['username']);
	update_option("wimtvpro_password", $_REQUEST['password']);

	$login_success = wimtv_login();
	if($login_success) {
		wp_redirect(admin_url('admin.php?page=wimtv-pro'));
		exit;
	} else {
		wp_redirect(admin_url('admin.php?page=wimtvpro_settings'));
		exit;
	}
}

function fail_version_check() {
	// version builder
	if (!defined('PHP_VERSION_ID')) {
		$version = explode('.', PHP_VERSION);
		define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
	}

	return PHP_VERSION_ID < 50500;
}

function fail_version(){
	echo "<h1>WimTV Pro requires PHP 5.5 or major.</h1>";
}