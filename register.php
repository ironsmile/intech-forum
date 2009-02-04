<?php
require_once 'includes/controller_setup.php';


class RegisterController implements Controller {
  public function execute($databaseConnection, $config, &$requestData) {
    $requestData['content'] = CONTENT_REGISTER;
    
      $p = $_POST;
      $requestData['errors'] = array();
      
      if( isset($p['reg_user']) and is_array($p['reg_user']) ){
        
        if( $p['anti_bot_user_input'] != anti_bot_str($p['anti_bot_seed']) )
          $requestData['errors'][] = "You have to match the text on the picture!";
        
        $uinput = $p['reg_user'];
        if( empty($uinput['password']) or $uinput['password'] != $uinput['repeat password'] )
          $requestData['errors'][] = "Password vairfication did not match!";
        
        if( !count( $requestData['errors'] ) ){
        
          $usr = User::create($databaseConnection,$uinput["name"],hash_string($uinput["password"]),$uinput["email"],USER_NORMAL);

          if( !$usr->save() )
            $requestData['errors'][] = "Fill all fields!";
        }
        
        if( !count( $requestData['errors'] ) ){
          redirect("index.php");
        }
      }
  }
}

$controller = new RegisterController();



include 'includes/ControllerExecutor.php';
?>
