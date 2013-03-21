<?php
class BaseUser
{
  private $users;
  private $path = 'data/user.txt';

  public function setUsers()
  {
    $users = array();
    $lines = file("$this->path");
    foreach ( $lines as $key => $value )
    {
      $users[$key] = explode(",",$lines[$key]);
    }
    $this->users = $users;
  }

  public function getUserById($id)
  {
    foreach ( $this->users as $key => $value )
    {
      if ( $this->users[$key][0] == $id )
      {
        return $this->users[$key];
      }
    }

    return null;
  }

  public function getUserName()
  {
  }

  public function getPassword()
  {
  }

  public function getNumberOfUsers()
  {
  }

  public function Authentication($name,$password)
  {
    if ( $this->getUserName() == $name )
    {
      if ( $this->getPassword() == $password )
      {
        return true;
      }

      return false;
    }

    return false;
  }

  public function createUser()
  {
  }

}
$a = new BaseUser;
$a->setUsers();
var_dump($a->getUserById(3));
