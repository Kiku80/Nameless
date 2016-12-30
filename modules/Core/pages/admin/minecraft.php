<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-dev
 *
 *  License: MIT
 *
 *  Admin Minecraft page
 */

// Can the user view the AdminCP?
if($user->isLoggedIn()){
	if(!$user->canViewACP()){
		// No
		Redirect::to(URL::build('/'));
		die();
	} else {
		// Check the user has re-authenticated
		if(!$user->isAdmLoggedIn()){
			// They haven't, do so now
			Redirect::to(URL::build('/admin/auth'));
			die();
		}
	}
} else {
	// Not logged in
	Redirect::to(URL::build('/login'));
	die();
}
 
// Deal with input
if(Input::exists()){
	// Check token
	if(Token::check(Input::get('token'))){
		// Valid token
		// Process input
		if(isset($_POST['enable_minecraft'])){
			// Either enable or disable Minecraft integration
			$enable_minecraft_id = $queries->getWhere('settings', array('name', '=', 'mc_integration'));
			$enable_minecraft_id = $enable_minecraft_id[0]->id;
			
			$queries->update('settings', $enable_minecraft_id, array(
				'value' => Input::get('enable_minecraft')
			));
		} else {
			// Integration settings
			
		}
	} else {
		// Invalid token
		
	}
}
 
// Check if Minecraft integration is enabled
$minecraft_enabled = $queries->getWhere('settings', array('name', '=', 'mc_integration'));
$minecraft_enabled = $minecraft_enabled[0]->value;
 
$token = Token::generate();
 
$page = 'admin';
$admin_page = 'minecraft';

?>
<!DOCTYPE html>
<html lang="<?php echo (defined('HTML_LANG') ? HTML_LANG : 'en'); ?>">
  <head>
    <!-- Standard Meta -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	
	<?php 
	$title = $language->get('admin', 'admin_cp');
	require('core/templates/admin_header.php'); 
	?>
	
	<link rel="stylesheet" href="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/switchery/switchery.min.css">

  </head>
  <body>
    <?php require('modules/Core/pages/admin/navbar.php'); ?>
	<div class="container">
	  <div class="row">
	    <div class="col-md-3">
		  <?php require('modules/Core/pages/admin/sidebar.php'); ?>
		</div>
		<div class="col-md-9">
		  <div class="card">
		    <div class="card-block">
			  <h3><?php echo $language->get('admin', 'minecraft'); ?></h3>
			  <form id="enableMinecraft" action="" method="post">
			    <?php echo $language->get('admin', 'enable_minecraft_integration'); ?>
				<input type="hidden" name="enable_minecraft" value="0">
			    <input name="enable_minecraft" type="checkbox" class="js-switch js-check-change"<?php if($minecraft_enabled == '1'){ ?> checked<?php } ?> value="1" />
				<input type="hidden" name="token" value="<?php echo $token; ?>">
			  </form>
			  
			  <?php
			  if($minecraft_enabled == '1'){
			  ?>
			  <hr>
			  <form action="" method="post">
				<input type="hidden" name="token" value="<?php echo $token; ?>">
				<input type="submit" class="btn btn-primary" value="<?php echo $language->get('general', 'submit'); ?>">
			  </form>
			  <?php			  
			  }
			  ?>
		    </div>
		  </div>
		</div>
	  </div>

    </div>
	
	<?php 
	require('modules/Core/pages/admin/footer.php');
	require('modules/Core/pages/admin/scripts.php'); 
	?>
	
	<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/switchery/switchery.min.js"></script>
	
	<script>
	var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
	elems.forEach(function(html) {
	  var switchery = new Switchery(html);
	});
	
	/*
	 *  Submit form on clicking enable/disable registration
	 */
	var changeCheckbox = document.querySelector('.js-check-change');

	changeCheckbox.onchange = function() {
	  $('#enableMinecraft').submit();
	};
	
	</script>
	
  </body>
</html>