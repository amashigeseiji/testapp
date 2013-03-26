<?php
class User extends BaseUser
{
  private
    $userid = null,
    $name = null,
    //$password,
    $token = null,
    $is_authenticated = false;

  function __construct()
  {
  }

  public function setUserId($id)
  {
    $this->userid = $id;
  }

  public function setName($id)
  {
    $this->name = $this->getUserNameById($id);
  }

  public function getName()
  {
    return $this->name;
  }

  public function getUserId()
  {
    return $this->userid;
  }

  public function setToken()
  {
    if ( $this->name != null)
    {
      $token = md5(uniqid());
      setcookie('token',$token);
      if (!file_exists("data/token/$token"))
      {
        touch("data/token/$token");
        $fp = fopen("data/token/$token","w");
        fwrite($fp,$this->name);
        fclose($fp);
      }
    }
  }

  public function getToken()
  {
    if ( $this->token == null )
    {
      return null;
    }
    else
    {
      return $this->token;
    }
  }

}
