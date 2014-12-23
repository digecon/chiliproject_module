<?php
/**
 * chili plugin settings
 */
elgg_load_library('elgg:chili');

$vars['entity']->security_salt = chili_getSalt();

$chili_url_label = elgg_echo('chili:server_url');
$chili_url_input = elgg_view('input/text', array(
		'name' => 'params[server_url]',
		'id' => 'chili_server_url',
		'value' => $vars['entity']->server_url
));

$chili_apikey_label = elgg_echo('chili:apikey');
$chili_apikey_input = elgg_view('input/text', array(
		'name' => 'params[apikey]',
		'id' => 'chili_apikey',
		'value' => $vars['entity']->apikey
));

$security_salt_label = elgg_echo('chili:security_salt');
$security_salt_input = elgg_view('input/text', array(
		'name' => 'params[security_salt]',
		'id' => 'chili_security_salt',
		'value' => $vars['entity']->security_salt
));


echo <<<___HTML


<div>
<label for="chili_server_url">$chili_url_label</label>
$chili_url_input
</div>

<div>
<label for="chili_apikey">$chili_apikey_label</label>
$chili_apikey_input
</div>

<div>
<label for="chili_security_salt">$security_salt_label</label>
$security_salt_input
</div>


___HTML;

