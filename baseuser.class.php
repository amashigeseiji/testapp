<?php
class BaseUser
{
  private
    $path = 'data/user.txt';

  public
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
        return str_replace(array("\r\n","\n","\r"),'',$baseusers[$key][2]);
      }
    }

    return null;
  }

  public function getIds()
  {
    $baseusers = $this->getBaseUsers();
    $ids = array();
    foreach ( $baseusers as $key => $value )
    {
      $ids[] .= $baseusers[$key][0];
    }

    return $ids;
  }

  public function getLastId()
  {
    $ids = $this->getIds();
    return max($ids);
  }

  public function registerUser($name,$password)
  {
    $usernames = $this->getUserNames();
    foreach( $usernames as $val )
    {
      if ( $name == $val )
      {
        return false;
      }
    }

    $newid = $this->getLastId() + 1;
    $fp = fopen($this->path,"a");
    fwrite($fp,$newid . ',' . $name . ',' . $password . "\n");
    fclose($fp);

    return true;
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
