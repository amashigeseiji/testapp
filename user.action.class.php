<?php
include_once('action.class.php');
class UserAction
{
  public
    $authusers = array(),
    $user,
    $baseuserobj,
    $logout = 0,
    $tetete = null,
    $tokens = array(),
    $usernames = array(),
    $message = array();


  function __construct()
  {
    $this->initialize();
  }

  public function initialize()
  {
    include_once('baseuser.class.php');
    $this->baseuserobj = null;
    $this->action = null;
    $this->authusers = array();
    $this->usernames = array();
    $this->user = null;
    $post = null;
    $tokens = array();

    $this->baseuserobj = new BaseUser;
    $this->setUserNames();


    $post = $this->getPostValue('logout');
    $cookie = $this->getCookie('token');

    //cookie の破棄が先!!
    if ( null != $post )
    {
      //ログアウト処理ではクッキーを削除
      $this->deleteToken($cookie);
      $this->user = null;
      $cookie = $this->getCookie('token');
      //ログインページを表示する
      $this->callTemplate('template/auth.php');
      $action = null;
    }

    if ( null != $cookie )
    {
      //cookieがtokenと一致すれば自動ログイン
      $name = $this->getNameByToken($cookie);
      $id = $this->baseuserobj->getUserIdByName($name);
      $this->user = $this->createUser($id);
      $this->callTemplate('template/auth.php');
      $action = new Action;
    }
    elseif ( $this->isWebRequest('name',$_POST) && $this->isWebRequest('password',$_POST) )
    {
      if ( true == $this->Authentication($_POST) )
      {
        $action = new Action;
      }
    }
    else
    {
      $this->user = null;
      $this->callTemplate('template/auth.php');
    }

  }

  public function isWebRequest($parameter,$request)
  {
    if ( array_key_exists($parameter,$request) )
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  public function getGetValue($parameter)
  {
    if ( true == $this->isWebRequest($parameter,$_GET) )
    {
      return $_GET[$parameter];
    }
    else
    {
      return null;
    }
  }

  public function getPostValue($parameter)
  {
    if ( true == $this->isWebRequest($parameter,$_POST) )
    {
      if ( '' != $_POST[$parameter] )
      {
        return $_POST[$parameter];
      }
      else
      {
        return null;
      }
    }
    else
    {
      return null;
    }
  }

  public function getCookie($parameter)
  {
    if ( true == $this->isWebRequest($parameter,$_COOKIE) )
    {
      return $_COOKIE[$parameter];
    }
    else
    {
      return null;
    }
  }

  public function Authentication($post)
  {
    if ( isset($post['name']) && isset($post['password']) )
    {
      $name = $post['name'];
      $id = $this->baseuserobj->getUserIdByName($name);
      if ( $this->auth($post['name'],$post['password']) == true )
      {
        $this->user = $this->createUser($id);
        $this->callTemplate('template/auth.php');
        return true;
      }
      else
      {
        echo $this->message['auth'];
        $this->callTemplate('template/auth.php');
        return false;
      }
    }
    return false;
  }

  public function auth($name,$password)
  {
    if ( empty($name) )
    {
      $this->message['auth'] = '名前を入力してください。';
      return false;
    }
    if ( empty($password) )
    {
      $this->message['auth'] = 'パスワードを入力してください。';
      return false;
    }

    if ( $id = $this->baseuserobj->getUserIdByName($name) );
    {
      if ( $this->baseuserobj->getPasswordById($id) == $password )
      {
        $this->authusers[] .= $id;
        return true;
      }

      $this->message['auth'] = 'パスワードが違います。';
      return false;
    }

    $this->message['auth'] = 'ユーザーが存在しません.';
    return false;
  }

  public function getNameByToken($token)
  {
    $dir = opendir('data/token');
    while ( false != ($filename = readdir($dir)) )
    {
      foreach ( $this->usernames as $name )
      {
        if ( $filename == $name )
        {
          if ( file_get_contents("data/token/$filename") == $token )
          {
            closedir($dir);
            return $name;
          }
        }
      }
    }
    closedir($dir);

    return null;
  }

  public function deleteToken($token)
  {
    if ( null != ($name = $this->getNameByToken($token)) )
    //if ( file_exists("data/token/$token") )
    {
      unlink("data/token/$name");
    }
    if ( $_COOKIE['token'] )
    {
      setcookie("token", '', time() -1800, '/');
      $_COOKIE = array();
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
    $baseusers = $this->baseuserobj->getBaseUsers();
    foreach ( $baseusers as $key => $value )
    {
      $this->usernames[] .= $baseusers[$key][1];
    }
  }

  public function setAuthUsers()
  {
    $filenames = glob("data/token/*");
    foreach ( $filenames as $val )
    {
      $this->authusers[] .= file_get_contents($filenames[$val]);
    }
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

  public function logout($token)
  {
    $_SESSION = array();
    $this->deleteToken($token);
    if ( isset($_COOKIE['token']) )
    {
      setcookie("token", '', time() - 1800, '/');
    }

    header("Location:".$_SERVER['PHP_SELF']);
  }

  public function callTemplate($file)
  {
    include_once($file);
  }

  public function createUser($id)
  {
    if ( $this->baseuserobj->isUser($id) == true )
    {
      include_once('user.class.php');
      $user = new User;
      $user->setUserId($id);
      $user->setName($id);
      $user->setToken();

      return $user;
    }
  }
}
