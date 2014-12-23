<?php
/**
 * Chili functions
 */

/*generate random string * $length is desired string length */
function chili_randString($length){
	$chars = 'abcdefghijklmnopqrstuvwxyz23456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$numChars = strlen($chars);
	$string = "";
	for ($i = 0; $i < $length; $i++) {
		$string .= $chars[mt_rand(0, $numChars - 1)];
	}
return $string;
}

//get secutiry salt
function chili_getSalt(){
	$chili = elgg_get_plugin_from_id('chili');
	$salt = $chili->getSetting('security_salt');
	
	if($salt == ''){
		$salt = chili_randString(15);
		//$vars['entity']->security_salt = $salt;
	}
	return $salt;
}

//create ChiliProject password for current user
function chili_passCreate(){
	$chili = elgg_get_plugin_from_id('chili');
	$salt = $chili->getSetting('security_salt');
	$current_email = get_loggedin_user()->email;
	$pass = md5($current_email . $salt);
	
	return $pass;
}

function chili_request($path, $method = 'GET', $data) {
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $path); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'X-ChiliProject-API-Key: ' . $apikey
            ));
	
    $result = curl_exec($ch);
	$result_array = json_decode($result, true);
	curl_close($ch);
}

function chili_userChange(){
    
}

/**
 * check if the currently logged in user exists in ChiliProject
 * if does, updates ChiliProject user to set new password
 * if not, creates new ChiliProject user with the login matched the login of currently logged in user
 */
function chili_userCreate(){
	$chili = elgg_get_plugin_from_id('chili');
	$salt = $chili->getSetting('security_salt');
	$server_url = $chili->getSetting('server_url');
	$apikey = $chili->getSetting('apikey');
	$current_user = get_loggedin_user()->username;
	$current_name = get_loggedin_user()->name;
	$current_email = get_loggedin_user()->email;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $server_url . 'users.json?name=' . $current_user); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'X-ChiliProject-API-Key: ' . $apikey
            ));
	$result = curl_exec($ch);
	$result_array = json_decode($result, true);
	curl_close($ch);
	
	if((in_array($current_user, $result_array['users']) == false) || ($result_array['total_count'] == 0)){
		$user_data = array('user' => array('login' => $current_user, 
										  'firstname' => strstr($current_name, " ", true), 
										  'lastname' => ltrim(strstr($current_name, " ")), 
										  'mail' => $current_email, 
										  'password' => chili_passCreate()));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $server_url . 'users.json');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'X-ChiliProject-API-Key: ' . $apikey
			));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($user_data));
		curl_exec($ch);
		curl_close($ch);
	}
}