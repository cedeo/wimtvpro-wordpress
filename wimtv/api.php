<?php

// load wp without theme
define('WP_USE_THEMES', false);
require_once('../../../../wp-load.php');
require_once('login.php');
/*
 * Main api.php, where all calls get redirect to.
 */

/**
 * Entry point function, where the magic begins
 */
function init() {
	$response = getRequest();
	if(FALSE === $response) {
		status_header(301);
		echo admin_url('admin.php?page=wimtvpro_settings');
//		header('Location: '.admin_url('admin.php?page=wimtvpro_settings'));
//		wp_redirect(admin_url('admin.php?page=wimtvpro_settings'));
	} else {
		echo $response;
	}
}

/**
 * Elaborates the POST parameters. Check encoding and calls the CALLAPI method
 */
function getRequest() {
	$post = file_get_contents('php://input');
	if(FALSE === $post) {
		die;
	}
	$json = json_decode($post);

	if($_FILES) {
		// file upload
		return callAPI($_POST['method'], $_POST['url'], $_POST['headers'], $_POST['data'], $_FILES);
	} else {
		// Everything Else
		return callAPI($json->method, $json->url, $json->headers, $json->data);
	}
}

/**
 * Support function for building the post data. Used for all calls.
 * @param $method
 * @param $url
 * @param $headers
 * @param bool $data
 * @param bool $file
 *
 * @return array
 */
function buildCallData($method, $url, $headers, $data = false, $file = false) {
	global $server_host;
	$array = Array();

	// standard request
	switch ($method) {
		case "POST":
			$array[CURLOPT_POST] = 1;
			if ($data) {
				$array[CURLOPT_POSTFIELDS] = json_encode($data);
			}
			if ($file) {
				$post_data = array(
					'file' =>  new CURLFILE($_FILES['data']['tmp_name']['file']), //$file_path,
					'title' => $data['title'],
					'description' => $data['description']
				);
				set_time_limit(600);
				$array[CURLOPT_POSTFIELDS] = $post_data;
			}

			break;
		case "PUT":
			$array[CURLOPT_PUT] = 1;
			break;
		case "DELETE":
			$array[CURLOPT_CUSTOMREQUEST] = "DELETE";
			break;
		default:
			if ($data) {
				$url = sprintf("%s?%s", $url, http_build_query($data));
			}
	}

	// headers
	$new_headers = array();
	foreach ($headers as $key => $value) {
		if($key === "Authorization") {
			array_push($new_headers, $key . ": Bearer " . get_option("wimtvpro_private_token"));
		} else {
			array_push($new_headers, $key . ": " . $value);
		}
	}
	$headers = $new_headers;

	$array[CURLOPT_URL] = $server_host . parse_url($url)['path'];
	$array[CURLOPT_HTTPHEADER] = $headers;
	$array[CURLOPT_RETURNTRANSFER] = 1;
	$array[CURLOPT_SSLVERSION] = CURL_SSLVERSION_TLSv1;


	$array[CURLOPT_SSL_VERIFYPEER] = false;

	return $array;
}

/**
 * Full Refresh Token functionality. Called whenever needed.
 * @param $originalMethod
 * @param $originalUrl
 * @param $originalHeaders
 * @param bool $originalData
 * @param bool $originalFile
 *
 * @return bool|mixed|string
 */
function refreshToken($originalMethod, $originalUrl, $originalHeaders, $originalData = false, $originalFile = false) {
	global $server_host;
	// building request data
	$data = array();
	$data['refresh_token'] = get_option("wimtvpro_refresh_token");
	$data['grant_type'] = "refresh_token";


	$array[CURLOPT_URL] = $server_host . '/wimtv-server/oauth/token';
	$array[CURLOPT_RETURNTRANSFER] = true;
	$array[CURLOPT_ENCODING] = "";
	$array[CURLOPT_CUSTOMREQUEST] = "POST";
	$array[CURLOPT_POSTFIELDS] = http_build_query($data);
	$array[CURLOPT_HTTPHEADER] = array(
		"Authorization: Basic d3d3Og==",
		"content-type: application/x-www-form-urlencoded",
	);
	$array[CURLOPT_SSL_VERIFYPEER] = false;

	// Executing call
	$curl = curl_init();

	curl_setopt_array($curl, $array);

	$result = curl_exec($curl);
	if(FALSE === $result) {
		print_r(curl_error($curl));
	}
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);

	// Post-call logic
	if(200 === $status) {
		// success
		update_option("wimtvpro_private_token", $result->access_token);
		update_option("wimtvpro_refresh_token", $result->refresh_token);
		return callAPI($originalMethod, $originalUrl, $originalHeaders, $originalData, $originalFile);
	} else if(401 === $status || 400 === $status){
		$login_successful = wimtv_login();
		if($login_successful) {
			return callAPI($originalMethod, $originalUrl, $originalHeaders, $originalData, $originalFile);
		} else {
			return false;
		}
	}
}

/**
 * Execute ALL CALLS to WIMTVSERVER.
 * @param $method
 * @param $url
 * @param $headers
 * @param $data
 * @param $file
 *
 * @return mixed|string
 */
function callAPI($method, $url, $headers, $data = false, $file = false) {
	$curl = curl_init();

	curl_setopt_array($curl, buildCallData($method, $url, $headers, $data, $file));

	$result = curl_exec($curl);
	if(FALSE === $result) {
		print_r(curl_error($curl));
	}
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);

	switch ($status) {
		case 401:
		case 403:
			return refreshToken($method, $url, $headers, $data, $file);
			break;
	}
	if(function_exists('http_response_code')) {
		http_response_code($status);
	} else {
		status_header($status);
	}
	return $result   ;
}
echo init();
