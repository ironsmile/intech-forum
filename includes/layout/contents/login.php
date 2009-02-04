<?php
if( count( $requestData['errors'] ) ){
?>

<div id="errors-login">
  <?php foreach($requestData['errors'] as $error ){ ?>
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
