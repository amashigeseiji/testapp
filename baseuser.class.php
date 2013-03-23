<?php
class BaseUser
{
  private
    $baseusers,
    $path = 'data/user.txt';

  public
    $authusers = array(),
      $message = array(
        'auth' => '',
      );


  function __construct()
  {
    $this->setBaseUsers();
  }

  public function setBaseUsers()
  {
    $baseusers = array();
    $lines = file($this->path);
    foreach ( $lines as $key => $value )
    {
      $baseusers[$key] = explode(",",$lines[$key]);
    }
    $this->baseusers = $baseusers;
  }

  public function getUserById($id)
  {
    foreach ( $this->baseusers as $key => $value )
    {
      if ( $this->baseusers[$key][0] == $id )
      {
        return $this->baseusers[$key];
      }
    }

    return null;
  }

  public function isUser($id)
  {
    foreach ( $this->baseusers as $key => $value )
    {
      if ( $this->baseusers[$key][0] == $id )
      {
        return true;
      }
    }

    return false;
  }

  public function getUserNameById($id)
  {
    foreach ( $this->baseusers as $key => $value )
    {
      if ( $this->baseusers[$key][0] == $id )
      {
        return $this->baseusers[$key][1];
      }
    }

    return null;
  }

  public function getUserIdByName($name)
  {
    foreach ( $this->baseusers as $key => $value )
    {
      if ( $this->baseusers[$key][1] == $name )
      {
        return $this->baseusers[$key][0];
      }
    }

    return null;
  }

  public function getPasswordById($id)
  {
    foreach ( $this->baseusers as $key => $value )
    {
      if ( $this->baseusers[$key][0] == $id )
      {
        return $this->baseusers[$key][2];
      }
    }

    return null;
  }

  public function getNumberOfUsers()
  {
  }

  public function Authentication($name,$password)
  {
    if ( $id = $this->getUserIdByName($name) );
    {
      if ( $this->getPasswordById($id) == $password )
      {
        $this->authusers[$id] = $this->createUser($id);
        return true;
      }

      $this->message['auth'] = 'パスワードが違います。';
      return false;
    }

    $this->message['auth'] = 'ユーザーが存在しません.';
    return false;
  }

  public function createUser($id)
  {
    if ( $this->isUser($id) == true )
    {
      include_once('user.class.php');
      $user = new User;
      $user->setUserId($id);
      $user->setName($this->getUserNameById($id));

      return $user;
    }
  }

}
