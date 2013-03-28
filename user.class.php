<?php
class User extends BaseUser
{
  private
    $userid = null,
    $name = null,
    //$password,
    $token = null;

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
      if (!file_exists("data/token/$this->name"))
      {
        $token = md5(uniqid());
        setcookie('token',$token);

        touch("data/token/$this->name");
        $fp = fopen("data/token/$this->name","w");
        fwrite($fp,$token);
        fclose($fp);
        $this->token = $token;
      }
      elseif (file_exists("data/token/$this->name"))
      {
        $token = file_get_contents("data/token/$this->name");
        $this->token = $token;
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
