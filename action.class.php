<?php
class Action
{
  public $message = array();
  public $id;
  public $input;
  public $deleteid;
  public $template = 'template/template.php';
  public $objects;

  function __construct()
  {
    $this->initialize();
  }

  public function initialize()
  {
    $this->obj = null;
    $this->id = null;
    $this->input = null;
    $this->deleteid = null;
    $this->objects = null;
    $this->message = null;

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
    }
    if(isset($this->message))
    {
      for( $i = 0; $i < count($this->message); $i++ )
      {
        echo $this->message[$i] . "<br />";
      }
    }

//    $this->id = $this->getId();
//    if (null != $this->id)
//    {
//      return $this->callTemplate($this->template);
//    }

    $this->createObjects(20);
    $this->callTemplate($this->template);
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
      return $_GET["id"];
    }
    else
    {
      return null;
    }
  }

  public function chkPost()
  {
    switch(true)
    {
      case (array_key_exists("delete",$_POST) == true):
       $this->deleteid = $_POST["delete"];
       break;

      case (array_key_exists("body",$_POST) == true && array_key_exists("title",$_POST) == true):
       if ($_POST['title'] == null)
       {
         $this->message[] .= 'タイトルを入力してください.';
       }
       if($_POST['body'] == null)
       {
         $this->message[] .= '本文を入力してください.';
       }
       if ($_POST['title'] != null && $_POST['body'] != null)
       {
         $this->input = $_POST;
       }
       break;

      default:
       $this->input = null;
       $this->deleteid = null;
    }

//    if(array_key_exists("delete",$_POST))
//    {
//      $this->deleteid = $_POST["delete"];
//    }
//    elseif(array_key_exists("body",$_POST) && array_key_exists("title",$_POST))
//    {
//      if ($_POST['title'] == null)
//      {
//        $this->message[] .= 'タイトルを入力してください.';
//      }
//      elseif($_POST['body'] == null)
//      {
//        $this->message[] .= '本文を入力してください.';
//      }
//      else
//      {
//        $this->input = $_POST;
//      }
//    }
//    else
//    {
//      $this->input = null;
//      $this->deleteid = null;
//    }
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
      echo '<li><a href=index.php'.'?id='.$i.'>';
      echo $this->renderTitle($i);
      echo '</a></li>';
    }
  }

  public function createObjects($num)
  {
    $this->objects = null;
    $ids = $this->obj->getIds();
    for ( $i = $this->obj->getLastId(); $i > count($ids) - $num; $i-- )
    {
      if($this->obj->isData($i) != false)
      {
        $this->objects[$i] = $this->obj->createData($i);
      }
    }
  }

  public function renderObjects()
  {
    $ids = $this->obj->getIds();
   // for ( $i = $this->obj->getLastId(); $i > count($ids) - $num; $i-- )
    foreach ($this->objects as $key => $value)
    {
     // if($this->obj->isData($i) == false)
     // {
     //   $i--;
     // }
     // else
     // {
        echo '<form action="#" method="post">';
        echo '<table class="objects">';
        echo '<tr class="title">';
        echo '<th class="title">';
        echo $this->objects[$key]->getId();
        echo '</th>';
        echo '<td>';
        echo $this->objects[$key]->getTitle();
        echo '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td colspan="2">';
        echo str_replace(array("\r\n","\r","\n"),'<br />',$this->objects[$key]->getBody());
        echo '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="delete" colspan="2">';
        echo '<input type="hidden" value="' . $key . '" name="delete" />';
        echo '<input type="submit" value="削除" />';
        echo '</td>';
        echo '</tr>';
        echo '</table>';
        echo '</form>';
     // }
    }
  }

  public function delete($id)
  {
    $this->obj->delete($id);
    $this->deleteid = null;
  }
}
