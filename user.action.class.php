<?php
include_once('action.class.php');
class UserAction extends Action
{
  public
    $authusers = array(),
    $baseusers,
    $baseobj,
    $usernames = array(),
    $logout = 0,
    $message = array();


  function __construct()
  {
    $this->initialize();
  }

  public function initialize()
  {
    include_once('baseuser.class.php');
    $this->baseobj = new BaseUser;
    if (array_key_exists('token',$_COOKIE))
    {
      $this->getNameByToken($_COOKIE['token']);
      $action = new Action;
    }
    //var_dump($this->getToken('amashige'));
    //$this->callTemplate('template/auth.php');
    //}
    var_dump($_POST);
    if ($_POST['logout'])
    {
      $this->deleteToken($_COOKIE['token']);
      $this->callTemplate('template/auth.php');
    }
    //$this->Start();
    if ($_POST)
    {
      $this->Authentication($_POST);
    }


    //if ( isset($_SESSION[$id]) )
    //{
    //  $action = new Action;
    //}
    //else
    //{
    //  $this->callTemplate('template/auth.php');
    //}
  }

  public function Start()
  {
    if ($_SESSION['count'] < 1)
    {
      setcookie('name');
      session_start();
      $_SESSION['count'] = 0;
      session_id();
      var_dump($_COOKIE); echo "cookie";
      $this->callTemplate('template/auth.php');
    }
    else
    {
    }
    if (array_key_exists('login',$_SESSION) && $_SESSION['login'] == 1)
    {
      $action = new Action;
    }
    else
    {
      $this->callTemplate('template/auth.php');
    }
  }

  public function Close()
  {
    $_SESSION = array();
    if (isset($_COOKIE[session_name()]))
    {
      setcookie("PHPSESSID", '', time() - 1800, '/');
    }

    //session_destroy();
    $_GET['logout'] = 0;
    $this->initialize();
    //header("Location:" .$_SERVER['PHP_SELF']);
  }

  public function Authentication($post)
  {
    if ( isset($post['name']) && isset($post['password']) )
    {
      $name = $post['name'];
      $id = $this->baseobj->getUserIdByName($name);
      if ( $this->baseobj->Authentication($post['name'],$post['password']) )
      {
        $this->setToken($name);
        //$_SESSION['login'] = 1;
        //$this->initialize();
        $action = new Action;
      }
      else
      {
        echo $this->baseobj->message['auth'];
      }

    }
    else
    {
    }
  }

  public function setToken($name)
  {
    $token = md5(uniqid());
    setcookie('token',$token);
    if (!file_exists("data/token/$token"))
    {
      touch("data/token/$token");
      $fp = fopen("data/token/$token","w");
      fwrite($fp,$name);
      fclose($fp);

      return true;
    }
  }

  public function getNameByToken($token)
  {
    if (file_exists("data/token/$token"))
    {
      $name = file_get_contents("data/token/$token");

      return $name;
    }
  }

  public function saveToken($name)
  {
    $token = md5(uniqid());
    touch("data/token/$token");
    $fp = fopen("data/token/$token","w");
    fwrite($fp,$name);
    fclose($fp);

    return true;
  }

  public function deleteToken($token)
  {
    if ( file_exists("data/token/$token") )
    {
      unlink("data/token/$token");

      return true;
    }
  }

  public function getToken($name)
  {
    if ( file_exists("data/token/$name"))
    {
      $token = file_get_contents("data/token/$name");

      return $token;
    }
    else
    {

      return null;
    }
  }

  public function setUserNames()
  {
    foreach ( $this->baseusers as $key => $value )
    {
      $this->usernames[] .= $this->baseusers[$key][1];
    }
  }

  public function setAuthUsers()
  {
    $filenames = glob("data/token/*");
    for ($i = 0; $i < count($filenames);$i++ )
    {
      foreach ( $this->usernames as $key => $name )
      {
        if( ($filenames[$i] == 'data/token/'. $name) == true )
        {
          $this->authusers[] .= $name;
        }
      }
    }
  }

  public function getAuthUsers()
  {
  }

  public function isAuthenticated()
  {
  }

  public function logout($token)
  {
    $_SESSION = array();
    $this->deleteToken($token);
    if (isset($_COOKIE['token']))
    {
      setcookie("token", '', time() - 1800, '/');
    }

    //var_dump($_SESSION);
    //session_destroy();
    header("Location:".$_SERVER['PHP_SELF']);
  }
}


$auth = new UserAction;
$auth->saveToken('amashige');
//if ( isset($_POST['name']) && isset($_POST['password']) )
//{
//  $auth->Authentication();
//  var_dump($_SESSION);
//}
//var_dump($auth->baseusers);
//$auth->setUserNames();
//var_dump($auth->usernames);
//$auth->saveToken('tet');
//$auth->setAuthUsers();
//var_dump($auth->authusers);
//$auth->saveToken('amashige');
//$auth->saveToken('tete');
//$auth->setAuthUsers();
