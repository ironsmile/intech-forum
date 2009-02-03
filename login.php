<?php
include_once("includes/common.include.php");
$errors = array();

$p = $_POST;
if( isset($p['uinfo']) and is_array($p['uinfo']) ){
	$uinfo = $p['uinfo'];
	$hashed_pass = hash_string($uinfo['pass']);
	
	try{
		$res = $db_con->sendQuery("SELECT `id`, `email` FROM `users`
		WHERE `name`='".$db_con->escapeString($uinfo['name'])."'
		AND `password`='".$hashed_pass."' ;");
		
		if( !$row = $db_con->fetchRow($res) ){
			$errors[] = "Wrond username or password";
		}
		else {
			$_SESSION['user_id'] = $row['id'];
// 			$_SESSION['user_identifier'] = md5($row['id'].$row['email'].$hashed_pass);
			header("Location: index.php");
			exit();
		}
		
	} catch(SQLException $e) {
		$errors[] = $e->getMessage();
	}
}

if( count($errors) ){
?>

<div id="errors-login">
	<?php foreach($errors as $error ){ ?>
		<p><?= $error ?></p>
	<?php } ?>
</div>

<?php } ?>

<form method="post">
	<p>Username:</p>
	<p><input type="text" name="uinfo[name]" value="" /></p>
	<p>Password:</p>
	<p><input type="text" name="uinfo[pass]" value="" /></p>
	<p><input type="submit" value="Login" /></p>
</form>
