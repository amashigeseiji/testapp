<?php
include_once('action.class.php');
class UserAction extends Action
{
  public
    $authusers = array(),
    $user,
    $baseuserobj,
    $usernames = array(),
    $logout = 0,
    $tetete = null,
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

    $this->baseuserobj = new BaseUser;
    $this->setUserNames();


    $post = $this->getPostValue('logout');
    $cookie = $this->getCookie('token');
    var_dump($cookie);

    //cookie の破棄が先!!
    //if (array_key_exists('logout',$_POST))
    if ( null != $post )
    {
      //ログアウト処理ではクッキーを削除
      $this->deleteToken($cookie);
      $this->user = null;
      //ログインページを表示する
      $this->callTemplate('template/auth.php');
      $action = null;
    }

    //if (array_key_exists('token',$_COOKIE) && $this->getNameByToken($_COOKIE['token']) != null )
    if ( null != $cookie )
    {
      var_dump($_COOKIE); echo 'initialize';
      //cookieがtokenと一致すれば自動ログイン
      //$name にbool値が入っている…
      $name = $this->getNameByToken($cookie);
      $id = $this->baseuserobj->getUserIdByName($name);
      $this->user = $this->createUser($id);
      $this->callTemplate('template/auth.php');
    }
    //elseif (array_key_exists('name',$_POST) && array_key_exists('password',$_POST))
    elseif ( $this->isWebRequest('name',$_POST) && $this->isWebRequest('password',$_POST) )
    {
      $this->Authentication($_POST);
      $this->callTemplate('template/auth.php');
    }
    else
    {
      $this->user = null;
      $this->callTemplate('template/auth.php');
    }

    //var_dump($this->user);
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
      }
      else
      {
        echo $this->message['auth'];
      }

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
    if (file_exists("data/token/$token"))
    {
      $name = file_get_contents("data/token/$token");

      foreach ( $this->usernames as $key => $val )
      {
        if ( $this->usernames[$key] == $name)
        {

          return $name;
        }
      }

      return null;
    }

    return null;
  }

  public function deleteToken($token)
  {
    if ( file_exists("data/token/$token") )
    {
      unlink("data/token/$token");
    }
    if ( $_COOKIE['token'] )
    {
      setcookie("token", '', time() -1800, '/');
      $_COOKIE = array();
      var_dump($_COOKIE); echo 'deleteToken';
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
    if ( isset($_COOKIE['token']) )
    {
      setcookie("token", '', time() - 1800, '/');
    }

    header("Location:".$_SERVER['PHP_SELF']);
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
