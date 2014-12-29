<?php
/**
 * Chili login form
 */
elgg_load_library('elgg:chili');
$chili = new Chili();
$userdata = $chili->getUserData();
$chili_settings = elgg_get_plugin_from_id('chili');
$login_url = $chili_settings->getSetting('server_url') . 'login/';
$encoded_url = urlencode($chili_settings->getSetting('server_url'));
?>
<form method="post" action="<?php echo $login_url; ?>" name="submit_form" style="display:none;">
<fieldset>
<input name="back_url" type="hidden" value="<?php echo $encoded_url; ?>">
<div>
	<label><?php echo elgg_echo('chili:login'); ?></label>
	<?php echo elgg_view('input/text', array(
		'name' => 'username', 
		'value' => $userdata['user'],
		'class' => 'elgg-autofocus'
		));
	?>
</div>
<div>
	<label><?php echo elgg_echo('password'); ?></label>
	<?php echo elgg_view('input/password', array('name' => 'password', 
												'value' => $chili->passCreate())); ?>
</div>

<?php echo elgg_view('login/extend', $vars); ?>

<div class="elgg-foot">
	<?php echo elgg_view('input/submit', array('id' => 'submit_button', 
												'value' => elgg_echo('login'))); ?>
	
	<?php 
	if (isset($vars['returntoreferer'])) {
		echo elgg_view('input/hidden', array('name' => 'returntoreferer', 'value' => 'true'));
	}
	?>
</div>
<br>
</fieldset>
</form>


<script type="text/javascript">
$(function(){
	document.forms.submit_form.submit();
});
</script>
