<?php
// make sure only logged in users can see this page 
gatekeeper();

elgg_load_library('elgg:chili');

//create ChiliProject user
ob_start();
$chili = new Chili();
$chili->userCreate();
$chili->userUpdate();
ob_end_clean();

$title = elgg_echo('chili:title');
 
// start building the main column of the page
$content = elgg_view_title($title);
 
// add the form to this section
$content .= elgg_view("forms/chili/login");
 
// optionally, add the content for the sidebar
$sidebar = elgg_view("page/elements/sidebar");
 
// layout the page
$body = elgg_view_layout('one_sidebar', array(
   'content' => $content,
   'sidebar' => $sidebar
));
 
// draw the page
echo elgg_view_page($title, $body);
