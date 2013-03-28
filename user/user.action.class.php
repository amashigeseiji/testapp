<?php
//include_once('action.class.php');
class UserAction extends Action
{
  public
    $authusers = array(),
    $user,
    $baseusers,
    $baseuserobjct,
    $usernames = array(),
    $logout = 0,
    $message = array();


  function __construct()
  {
    $this->initialize();
  }

  public function initialize()
  {
    include_once('../baseuser.class.php');
    $this->baseuserobjct = new BaseUser;

    if (array_key_exists('token',$_COOKIE))
    {
      //cookieがtokenと一致すれば自動ログイン
      $this->getNameByToken($_COOKIE['token']);
      $user = $this->baseuserobjct->createUser();
      //$action = new Action;
    }
    else
    {
      $this->callTemplate('template/auth.php');
    }

    if (array_key_exists('logout',$_POST))
    {
      //ログアウト処理ではクッキーを削除
      $this->deleteToken($_COOKIE['token']);
      //ログインページを表示する
      $this->callTemplate('template/auth.php');
      //$action = null;
    }

    if (array_key_exists('name',$_POST) && array_key_exists('password',$_POST))
    {
      $this->Authentication($_POST);
      //$action = new Action;
    }

    var_dump($this->user);

  }

  public function Authentication($post)
  {
    if ( isset($post['name']) && isset($post['password']) )
    {
      $name = $post['name'];
      $id = $this->baseuserobjct->getUserIdByName($name);
      if ( $this->auth($post['name'],$post['password']) == true )
      {
        $this->user = $this->baseuserobjct->createUser($id);
        //$action = new Action;
      }
      else
      {
        echo $this->message['auth'];
      }

    }
    else
    {
    }
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

    if ( $id = $this->baseuserobjct->getUserIdByName($name) );
    {
      if ( $this->baseuserobjct->getPasswordById($id) == $password )
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
    if (file_exists("data/token/$token"))
    {
      $name = file_get_contents("data/token/$token");

      return $name;
    }
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
    var_dump(file_get_contents($filenames));
    foreach ( $filenames as $val )
    {
      $this->authusers[] .= file_get_contents($filenames[$val]);
    }
    var_dump($this->authusers);exit;
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
    if (isset($_COOKIE['token']))
    {
      setcookie("token", '', time() - 1800, '/');
    }

    //var_dump($_SESSION);
    //session_destroy();
    header("Location:".$_SERVER['PHP_SELF']);
  }
}
