<?php  
  if( count( $requestData['errors'] ) ){
?>

<div id="reg-errors">
  <?php foreach( $requestData['errors'] as $error ){ ?>
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