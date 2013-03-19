<?php
class Action
{
  public $message = array();
  public $id = '';
  public $input;
  public $template = 'template/template.php';

  function __construct()
  {
    $this->initialize();
  }

  public function initialize()
  {
    $this->obj = "";
    $this->id = '';
    $this->input = null;
    $this->deleteid = null;

    $this->createInstance();
    $this->obj->initialize();

    $this->chkPost();
    if (null != $this->deleteid)
    {
      $this->delete($this->deleteid);
    }
    if (null != $this->input)
    {
      $this->writeData($this->input);
    //  return $this->callTemplate($this->template);
    }
    if(isset($this->message))
    {
      for( $i = 0; $i < count($this->message); $i++ )
      {
        echo $this->message[$i];
      }
    }

    $this->getId();
    if (isset($this->id))
    {
      return $this->callTemplate($this->template);
    }
  }

  public function createInstance()
  {
    include_once('BaseData.class.php');
    $this->obj = new BaseData;
  }

  public function getId()
  {
    if (array_key_exists("id",$_GET))
    {
      return $this->id = $_GET["id"];
    }
    else
    {
      return $this->id = '';
    }
  }

  public function chkPost()
  {
    if(array_key_exists("delete",$_POST))
    {
      $this->deleteid = $_POST["delete"];
    }
    elseif(array_key_exists("body",$_POST) && array_key_exists("title",$_POST))
    {
      if ($_POST['title'] == null)
      {
        $this->message[] .= 'タイトルを入力してください.';
      }
      elseif($_POST['body'] == null)
      {
        $this->message[] .= '本文を入力してください.';
      }
      else
      {
        $this->input = $_POST;
      }
    }
    else
    {
      $this->input = null;
      $this->deleteid = null;
    }
  }

  public function renderTitle($id)
  {
    return htmlspecialchars($this->obj->getTitleById($id));
  }

  public function renderBody($id)
  {
    $body = $this->obj->getBodyById($id);
    return $body = str_replace(array("\r\n","\r","\n"),'<br />',$body);
  }

  public function writeData($input)
  {
    $this->obj->writeTitle($input["title"]);
    $this->obj->writeBody($input["body"]);
  }

  public function showTitle()
  {
    $id = $this->getId();
    $title = 'test_title';
    if(!$id){
      echo $title;
    }else{
      echo $title = $this->renderTitle($id);
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
    include_once($a);
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
     echo $this->renderTitle($id);
     echo '</h3>';
     echo '<p>';
     echo $this->renderBody($id);
     echo '</p>';
     echo ' <input type="submit" name="delete" method="post" value="delete" action="#"/>';
    }
  }

  public function renderSideBar($num)
  {
    for ($i = $this->obj->getLastId(); $i > $this->obj->getLastId() - $num; $i-- )
    {
      echo "<li><a href=index.php".'?id='.$i.">";
      echo $this->renderTitle($i);
      echo '</a></li>';
    }
  }

  public function createObjects($num)
  {
    $objects = array();
    for ( $i = $this->obj->getLastId(); $i > $this->obj->getLastId() - $num; $i-- )
    {
      if($objects[$i] == null)
      {
        return $i--;
      }
      else{
        $objects[$i] = $this->obj->createData($i);
      }
    }

    return $objects;
  }

  public function renderObjects($num)
  {
    $objects = $this->createObjects($num);
    for ( $i = $this->obj->getLastId(); $i > $this->obj->getLastId() - $num; $i-- )
    {
      echo '<form action="#" method="post">';
      echo '<table class="objects">';
      echo '<tr class="title">';
      echo '<th class="title">title</th>';
      echo '<td>';
      echo $objects[$i]->getTitle();
      echo '</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td colspan="2">';
      echo str_replace(array("\r\n","\r","\n"),'<br />',$objects[$i]->getBody());
      echo '</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td class="delete" colspan="2">';
      echo '<input type="hidden" value="' . $i . '" name="delete" />';
      echo '<input type="submit" value="削除" />';
      echo '</td>';
      echo '</tr>';
      echo '</table>';
      echo '</form>';
    }
  }

  public function delete($id)
  {
    $this->obj->delete($id);
  }
}
