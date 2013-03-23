<?php
class User
{
  private
    $userid,
    $name,
    //$password,
    $is_authenticated = false;

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName($name)
  {
    return $this->name;
  }

  public function setUserId($id)
  {
    $this->userid = $id;
  }

  public function getUserId()
  {
    return $this->userid;
  }
}

