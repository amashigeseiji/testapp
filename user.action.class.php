<?php
include_once('action.class.php');
class UserAction extends Action
{
  public
    $authusers = array();
  public
    $baseusers;

  function __construct()
  {
    session_start();
    include_once('baseuser.class.php');
        var_dump($this->authusers);
  var_dump($_SESSION);
    $this->baseusers = new BaseUser;
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

  public function Authentication()
  {
    if ( isset($_POST['name']) && isset($_POST['password']) )
    {
      if ( $this->baseusers->Authentication($_POST['name'],$_POST['password']) )
      {
        $_SESSION['id'] = $this->baseusers->getUserIdByName($_POST['name']);
        $this->authusers[] .= $this->baseusers->getUserIdByName($_POST['name']);
        $action = new Action;
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

var_dump($_SESSION);
    session_destroy();
  }
}
if ($_GET['logout'] == 1)
{
    $_SESSION = array();

    if (isset($_COOKIE["PHPSESSID"]))
    {
        setcookie("PHPSESSID", '', time() - 1800, '/');
    }

var_dump($_SESSION);
    session_destroy();
  var_dump($_SESSION);
  var_dump($_COOKIE);
}

$auth = new UserAction;
if ( isset($_POST['name']) && isset($_POST['password']) )
{
  $auth->Authentication();
  var_dump($_SESSION);
}
