<?php
class BaseUser
{
  private
    $path = 'data/user.txt';

  public
    $authusers = array(),
    $message   = array(
        'auth' => '',
      ),
    $user;


  public function getBaseUsers()
  {
    $baseusers = array();
    $lines = file($this->path);
    foreach ( $lines as $key => $value )
    {
      $baseusers[$key] = explode(",",$lines[$key]);
    }
    return $baseusers;
  }

  public function getUserById($id)
  {
    $baseusers = $this->getBaseUsers();
    foreach ( $baseusers as $key => $value )
    {
      if ( $baseusers[$key][0] == $id )
      {
        return $baseusers[$key];
      }
    }

    return null;
  }

  public function isUser($id)
  {
    $baseusers = $this->getBaseUsers();
    foreach ( $baseusers as $key => $value )
    {
      if ( $baseusers[$key][0] == $id )
      {
        return true;
      }
    }

    return false;
  }

  public function getUserNameById($id)
  {
    $baseusers = $this->getBaseUsers();
    foreach ( $baseusers as $key => $value )
    {
      if ( $baseusers[$key][0] == $id )
      {
        return $baseusers[$key][1];
      }
    }

    return null;
  }

  public function getUserIdByName($name)
  {
    $baseusers = $this->getBaseUsers();
    foreach ( $baseusers as $key => $value )
    {
      if ( $baseusers[$key][1] == $name )
      {
        return $baseusers[$key][0];
      }
    }

    return null;
  }

  public function getPasswordById($id)
  {
    $baseusers = $this->getBaseUsers();
    foreach ( $baseusers as $key => $value )
    {
      if ( $baseusers[$key][0] == $id )
      {
        return $baseusers[$key][2];
      }
    }

    return null;
  }

  public function getNumberOfUsers()
  {
  }

  public function createUser($id)
  {
    if ( $this->isUser($id) == true )
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
