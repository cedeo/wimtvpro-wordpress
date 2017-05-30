<?php
include_once "server_config.php";
/**
 * Login Function for wimtv
 * @return bool
 */
function wimtv_login() {
	global $server_host;
	// building request data
	$data = array();
	$data['username'] = get_option("wimtvpro_username");
	$data['password'] = get_option("wimtvpro_password");
	$data['grant_type'] = "password";


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
		$decoded = json_decode($result);
		update_option("wimtvpro_private_token", $decoded->access_token);
		update_option("wimtvpro_refresh_token", $decoded->refresh_token);
		return true;
	} else {
		return false;
	}
}
