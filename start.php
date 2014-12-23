<?php
/**
 * chili plugin init
 */
function chili_init(){
	//add a tab in site menu
	elgg_register_menu_item('site', new ElggMenuItem('chili', elgg_echo('chili:menuitem'), '/chili'));
	
	//register library for chili
	elgg_register_library('elgg:chili', elgg_get_plugins_path() . 'chili/lib/chili.php');
	
	//page handler for main page of Chili plugin
	function chili_page_handler(){
		include elgg_get_plugins_path() . 'chili/pages/chili/main.php';
	}
	
	// Register a page handler, so we can have nice URLs
	elgg_register_page_handler('chili','chili_page_handler');
}

elgg_register_event_handler('init', 'system', 'chili_init');