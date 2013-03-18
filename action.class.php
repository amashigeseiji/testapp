<?php
class action
{
  public function initialize()
  {
    $this->obj = "";
    $this->createInstance();
    $this->chkPost();
    $this->callTemplate("template/template.php");
  }

  public function createInstance()
  {
    include('BaseData.class.php');
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
    $id = $this->getId();
    $title = 'test_title';
    if(!$id){
      echo $title;
    }else{
      echo $title = $this->showTitleById($id);
    }
  }

  public function callHtml($a)
  {
    $template = file($a);
    for ( $i = 0; $i < count($template); $i++ )
    {
      print $template[$i];
    }
  }

  public function callTemplate($a)
  {
    include($a);
  }

  public function renderContent()
  {
    $id = $this->getId();
    if ($id == '')
    {
      $this->callHtml("template/inputform.html");
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

  public function chkPost()
  {
    $message = array();
    if (isset($_POST))
    {
      if(array_key_exists("body",$_POST) && array_key_exists("title",$_POST))
      {
        if ($_POST['title'] == null)
        {
          $message[] .= 'タイトルを入力してください.';
        }
        elseif($_POST['body'] == null)
        {
          $message[] .= '本文を入力してください.';
        }
        else
        {
          $this->writeData($_POST);
        }
      }
      if(isset($message))
      {
        for( $i = 0; $i < count($message); $i++ )
        {
          echo $message[$i];
        }
      }
    }
  }

}
