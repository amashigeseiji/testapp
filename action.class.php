<?php
include('BaseData.class.php');
class action
{
    public $id = '';
  function __construct()
  {
    $this->obj = "";
    $this->createInstance();
  }

  public function createInstance()
  {
    $this->obj = new BaseDataClass;
  }

  public function showDataById($id)
  {
    $this->showTitleById($id);
    $this->showBodyById($id);
  }

  public function showTitleById($id)
  {
    return $this->obj->getTitle($id);
  }

  public function renderBody($id)
  {
    $body = $this->obj->getBody($id);
    return $body = str_replace(array("\r\n","\r","\n"),'<br />',$body);
  }

  public function writeData($input)
  {
    $this->obj->writeTitle($input["title"]);
    $this->obj->writeBody($input["body"]);
  }

  public function getLastId()
  {
    return $this->obj->getLastId();
  }

  public function showTitle()
  {
    if(!$_GET){
      echo 'test';
    }else{
      echo $this->showTitleById($_GET["id"]);
    }
  }

  public function renderContent()
  {
    $id = $this->getId();
    $inputform = file("template/inputform.html");
    if ($id == '')
    {
      for ($i = 0; $i < count($inputform); $i++)
      {
        echo $inputform[$i];
      }
    }
    else
    {
     echo '<h3>title : ';
     echo $this->showTitleById($id);
     echo '</h3>';
     echo '<p>';
     echo $this->renderBody($id);
     echo '</p>';
     echo ' <input type="submit" name="delete" method="post" value="delete" action="#"/>';
    }
  }

  public function renderSideBar($num)
  {
    for ($i = $this->getLastId(); $i > $this->getLastId() - $num; $i--)
    {
      echo "<li><a href=index.php".'?id='.$i.">";
      echo $this->showTitleById($i);
      echo '</a></li>';
    }
  }

  public function getId()
  {
    if (array_key_exists("id",$_GET))
    {
      return $_GET["id"];
    }
    else
    {
      return;
    }
  }
}
