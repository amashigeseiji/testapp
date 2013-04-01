<?php
class UserAction
{
  public
    $user,
    $usernames = array(),
    $cookie = null,
    $login = 0,
    $message = array();


  function __construct()
  {
    $this->initialize();
  }

  public function initialize()
  {
    include_once('baseuser.class.php');
    $this->action = null;
    $this->usernames = array();
    $this->user = null;
    $this->cookie = null;
    $this->login = 0;

    $this->setUserNames();

    //$logout = $this->getGetValue('logout');
    $this->cookie = $this->getCookie('token');

    //cookie の破棄が先!!
    if ( null != $this->getPostValue('logout') )
    {
      $this->logout($this->getPostValue('logout'));
    }

    if ( $this->isWebRequest('newname',$_POST) && $this->isWebRequest('newpassword',$_POST) )
    {
      $this->entry();
    }

    if ( 'SUCCESS' == $this->login($this->cookie) )
    {
      include_once('action.class.php');
      new Action;
    }
  }

  public function login($cookie)
  {
    //cookieを渡された場合
    if ( null != $cookie )
    {
      //cookieがtokenと一致すれば自動ログイン
      $name = $this->getNameByToken($cookie);
      //渡されたcookieから名前が見つからなければログアウト処理
      if ( null == $name )
      {
        $this->logout($cookie);
        return;
      }
      else
      {

        return 'SUCCESS';
      }
    }

    //cookieがnullの場合
    elseif ( $this->isWebRequest('name',$_POST) && $this->isWebRequest('password',$_POST) )
    {
      if ( true == $this->auth($_POST['name'],$_POST['password']) )
      {

        return 'SUCCESS';
      }
      else
      {
        $this->callTemplate('template/auth.php');
      }
    }

    else
    {
      $this->callTemplate('template/auth.php');
    }
  }

  public function logout($cookie)
  {
    //ログアウト処理ではクッキーを削除
    $this->deleteToken($cookie);
    $this->cookie = $this->getCookie('token');
    //ログインページを表示する
    header("Location: /");
    $this->callTemplate('template/auth.php');
  }

  public function entry()
  {
    if ( null == ($newname = $this->getPostValue('newname')) )
    {
      $this->message['register'] = 'ユーザー名は必須項目です.';
      return false;
    }
    elseif ( null == ($newpassword = $this->getPostValue('newpassword')) )
    {
      $this->message['register'] = 'パスワードは必須項目です.';
      return false;
    }
    else
    {
      if ( false ==  $this->getBaseUserObj()->registerUser($newname,$newpassword) )
      {
        $this->message['register'] = 'ユーザー名が重複しています.';
        return false;
      }
      else
      {
        $this->message['register'] = '新規ユーザーを登録しました.';
        $this->callTemplate('template/auth.php');
        return true;
      }
    }
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

  public function getUser($id)
  {
    return $this->getBaseUserObj()->createUser($id);
  }

  public function auth($name,$password)
  {
    //入力された名前から取得できるtokenが存在する場合は削除
    if ( null != $this->getTokenByName($name) )
    {
      unlink("data/token/$name");
    }

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

    if ( $id = $this->getBaseUserObj()->getUserIdByName($name) );
    {
      if ( $this->getBaseUserObj()->getPasswordById($id) == $password )
      {
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
    $baseusers = $this->getBaseUserObj()->getBaseUsers();
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

}
