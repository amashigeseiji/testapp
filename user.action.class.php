<?php
class UserAction
{
  public function Authentication()
  {
    if ( isset($_POST['name']) && isset($_POST['password']) )
    {
      include_once('baseuser.class.php');
      $useraction = new BaseUser;
      if ( $useraction->Authentication($_POST['name'],$_POST['password']) )
      {
        include_once('action.class.php');
        $action = new Action;
      }
      else
      {
        echo $useraction->message['auth'];
      }

    }
  }
}
