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

  public function getUserNames()
  {
    $baseusers = $this->getBaseUsers();
    $usernames = array();
    foreach ( $baseusers as $key => $value )
    {
      $usernames[] .= $baseusers[$key][1];
    }

    return $usernames;
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

  public function getUserNameByToken($token)
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
