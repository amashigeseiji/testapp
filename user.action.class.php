<?php
include_once('action.class.php');
class UserAction extends Action
{
  public
    $authusers = array();
  public
    $baseusers,
    $logout = 0;


  function __construct()
  {
    $this->initialize();
  }

  public function initialize()
  {
    if ( array_key_exists('logout',$_GET) && $_GET['logout'] == 1 )
    {
      $this->logout();
    }
    else
    {
      session_start();
      include_once('baseuser.class.php');
      $this->baseusers = new BaseUser;
      var_dump($_SESSION);
      if ( isset($_SESSION['id']) )
      {
        $this->authusers[] .= $_SESSION['id'];
        $action = new Action;
      }
      else
      {
        $this->callTemplate('template/auth.php');
      }
    }
  }

  public function Authentication()
  {
    if ( isset($_POST['name']) && isset($_POST['password']) )
    {
      if ( $this->baseusers->Authentication($_POST['name'],$_POST['password']) )
      {
        $_SESSION['id'] = $this->baseusers->getUserIdByName($_POST['name']);
        $this->authusers[] .= $this->baseusers->getUserIdByName($_POST['name']);
        $this->initialize();
      }
      else
      {
        echo $this->baseusers->message['auth'];
      }

    }
    else
    {
    }
  }

  public function setAuthUsers()
  {
    fopen("data/$username",w);
  }

  public function getAuthUsers()
  {
  }

  public function isAuthenticated()
  {
  }

  public function logout()
  {
    $_SESSION = array();
    if (isset($_COOKIE["PHPSESSID"]))
    {
      setcookie("PHPSESSID", '', time() - 1800, '/');
    }

    //var_dump($_SESSION);
    //session_destroy();
    header("Location: http://test2.local");
  }
}


$auth = new UserAction;
if ( isset($_POST['name']) && isset($_POST['password']) )
{
  $auth->Authentication();
  var_dump($_SESSION);
}
