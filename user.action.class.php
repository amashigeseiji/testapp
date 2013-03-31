<?php
include_once('action.class.php');
class UserAction
{
  public
    $user,
    $baseuserobj,
    $usernames = array(),
    $cookie = null,
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
    $this->usernames = array();
    $this->user = null;
    $this->cookie = null;


    $this->baseuserobj = new BaseUser;
    $this->setUserNames();

    //$logout = $this->getGetValue('logout');
    $this->cookie = $this->getCookie('token');

    //cookie の破棄が先!!
    if ( null != $this->getPostValue('logout') )
    {
      $this->logout($this->getPostValue('logout'));
    }

    $this->login($this->cookie);
  }

  public function login($cookie)
  {
    if ( null != $cookie )
    {
      //cookieがtokenと一致すれば自動ログイン
      $name = $this->getNameByToken($cookie);
      $id = $this->baseuserobj->getUserIdByName($name);
      $this->user = $this->createUser($id);
      $action = new Action;
    }
    elseif ( $this->isWebRequest('name',$_POST) && $this->isWebRequest('password',$_POST) )
    {
      if ( true == $this->Authentication($_POST) )
      {
        header("Location: /");
        $action = new Action;
      }
      else
      {
        $this->callTemplate('template/auth.php');
      }
    }
    else
    {
      $this->user = null;
      $this->callTemplate('template/auth.php');
    }
  }

  public function logout($cookie)
  {
    //ログアウト処理ではクッキーを削除
    $this->deleteToken($cookie);
    $this->user = null;
    $this->cookie = $this->getCookie('token');
    //ログインページを表示する
    header("Location: /");
    $this->callTemplate('template/auth.php');
    $action = null;
  }

  public function getBaseUserObj()
  {
    return new BaseUser;
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

      if ( null != $this->getTokenByName($_POST['name']) )
      {
        unlink("data/token/$name");
      }

      if ( $this->auth($post['name'],$post['password']) == true )
      {
        $this->user = $this->createUser($id);
        return true;
      }
      else
      {
        echo $this->message['auth'];
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
    $usernames = $this->getUserNames();

    while ( false != ($filename = readdir($dir)) )
    {
      foreach ( $usernames as $name )
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

  public function getTokenByName($name)
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

  public function getUserNames()
  {
    $baseusers = $this->getBaseUserObj()->getBaseUsers();
    $usernames = array();
    foreach ( $baseusers as $key => $value )
    {
      $usernames[] .= $baseusers[$key][1];
    }
    return $usernames;
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
