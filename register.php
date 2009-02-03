<?php
include_once("includes/common.include.php");

	$p = $_POST;
	$errors = array();
	
	if( isset($p['reg_user']) and is_array($p['reg_user']) ){
		
		if( $p['anti_bot_user_input'] != anti_bot_str($p['anti_bot_seed']) )
			$errors[] = "You have to match the text on the picture!";
		
		$uinput = $p['reg_user'];
		if( empty($uinput['password']) or $uinput['password'] != $uinput['repeat password'] )
			$errors[] = "Password vairfication did not match!";
		
		if( !count( $errors ) ){
			$usr = new User;
			foreach( array("email","name","password") as $property )
				$usr->$property = $uinput[$property];
			$usr->type = USER_NORMAL;
			$usr->password = hash_string($usr->password);
			if( !$usr->save() )
				$errors[] = "Fill all fields!";
		}
		
		if( !count( $errors ) ){
			header("Location: index.php");
			exit("successfuly logged");
		}
	}
	
	if( count( $errors ) ){
?>

<div id="reg-errors">
	<?php foreach( $errors as $error ){ ?>
	<p><?= $error ?></p>
	<?php } ?>
</div>

<? } ?>

<?php $img_seed = hash_string(time()); ?>
<form method="post">
	<?php foreach( array("email","name") as $field ){ ?>
	<p><?= ucwords($field) ?>:</p>
	<p><input type="text" name="reg_user[<?= $field ?>]" value="" /></p>
	<?php } ?>
	<?php foreach( array("password","repeat password") as $field ){ ?>
	<p><?= ucwords($field) ?>:</p>
	<p><input type="password" name="reg_user[<?= $field ?>]" value="" /></p>
	<?php } ?>
	<img src="antibotimg.php?str=<?= $img_seed ?>" alt="anti-bot-image" />
	<input type="hidden" value="<?= $img_seed ?>" name="anti_bot_seed" />
	<p>Prove you're human by repeating the text on the image above!</p>
	<p><input type="text" name="anti_bot_user_input" value="" /></p>
	<p><input type="submit" value="Register" /></p>
</form>
