<?php
require_once 'includes/controller_setup.php';

class LoginController implements Controller {
  public function execute($databaseConnection, $config, &$requestData) {
  
    if( user_logged() ){
      redirect("index.php");
      exit("alraedy logged in!");
    }
  
    $requestData['content'] = CONTENT_LOGIN;
    
      $requestData['errors'] = array();
      $db_con = &$databaseConnection;
      
      $p = $_POST;
      if( isset($p['uinfo']) and is_array($p['uinfo']) ){
        $uinfo = $p['uinfo'];
        $hashed_pass = hash_string($uinfo['pass']);
        
        try{
          $res = $db_con->sendQuery("SELECT `id`, `email` FROM `users`
          WHERE `name`='".$db_con->escapeString($uinfo['name'])."'
          AND `password`='".$hashed_pass."' ;");
          
          if( !$row = $db_con->fetchRow($res) ){
            $requestData['errors'][] = "Wrond username or password";
          }
          else {
            $_SESSION['user_id'] = $row['id'];
            $location = isset($_SESSION['back_to']) ? $_SESSION['back_to'] : "index.php" ;
            unset($_SESSION['back_to']);
            redirect($location);
            exit();
          }
          
        } catch(SQLException $e) {
          $requestData['errors'][] = $e->getMessage();
        }
      }
  }
}

$controller = new LoginController();

include 'includes/ControllerExecutor.php';

?>