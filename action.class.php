<?php
class Action
{
  private
    $message = array(),
    $input,
    $pagetitle,
    $defaulttitle = 'testpage',
    $deleteid,
    $template = 'template/template.php',
    $objects,
    $objectsnum,
    $submited,
    $pageid;

  function __construct()
  {
    $this->initialize();
  }

  public function initialize()
  {
    $this->message = array();
    $this->obj = null;
    $this->input = null;
    $this->pagetitle = '';
    $this->deleteid = null;
    $this->objects = null;
    $this->objectsnum = 0;
    $this->submited = array(
      'title'   => '',
      'body'    => '',
      'delete'  => '',
    );
    $this->pageid = '';

    $this->createInstance();
    $this->obj->initialize();

    $this->chkPost();
    if ( null != $this->submited['delete'] )
    {
      $this->delete($this->submited['delete']);
    }
    if ( null != $this->submited['title'] && null != $this->submited['body'] )
    {
      $this->writeData($this->submited);
    }

    $this->setPageId();
    $this->setPageTitle();
    $this->setObjectsNum(20);
    if ($this->objectsnum != 0)
    {
      $this->createObjects($this->objectsnum);
    }

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
       $this->submited['delete'] = $_POST['delete'];
       break;

      case (array_key_exists("body",$_POST) == true && array_key_exists("title",$_POST) == true):
       if ($_POST['title'] == null)
       {
         $this->message['title'] = 'タイトルを入力してください.';
       }
       if($_POST['body'] == null)
       {
         $this->message['body'] = '本文を入力してください.';
       }
       if ($_POST['title'] != null && $_POST['body'] != null)
       {
         $this->submited['title'] = $_POST['title'];
         $this->submited['body'] = $_POST['body'];
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

  public function escapeText($text)
  {
    return htmlspecialchars($text);
  }

  public function renderBody($text)
  {
    return str_replace(array("\r\n","\r","\n"),'<br />',$text);
  }

  public function writeData($input)
  {
    if ( $this->obj->writeTitle($input["title"]) == true )
    {
      $this->obj->writeBody($input["body"]);
      $this->obj->writeDate();
    }
    else
    {
      $this->obj->errormessage = array();
      $this->obj->errormessage[] .= '[' . __method__ . '] write failed.';
      $this->obj->errorLog();
    }
  }

  public function setPageTitle()
  {
    $id = $this->getId();
    if (null == $id)
    {
      $this->pagetitle = $this->defaulttitle;
    }
    else
    {
      $this->pagetitle = $this->obj->getTitleById($id);
    }
  }

  public function setPageId()
  {
    $id = $this->getId();
    if ( null != $id )
    {
      if ( $this->obj->isData($id) == false )
      {
        $this->pageid = '';
      }
      else
      {
        $this->pageid = $id;
      }
    }
    else
    {
      $this->pageid = '';
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
    if ( $this->pageid == '' )
    {
      //$this->callHtml("template/inputform.html");
      $this->callTemplate('template/inputform.php');
    }
    else
    {
      echo '<form action="#" method="post">';
      echo '<h4>&nbsp;title : ';
      echo $this->escapeText($this->obj->getTitleById($this->pageid));
      echo '</h4>';
      echo '<p>';
      echo $this->renderBody($this->pageid);
      echo '</p>';
      echo '<input type="hidden" value="' . $this->pageid . '" name="delete" />';
      echo '<input type="submit" value="削除" />';
      echo '</form>';
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
    if ( !empty($ids) );
    {
      for ( $i = $this->obj->getLastId(); $i > count($ids) - $num; $i-- )
      {
        if($this->obj->isData($i) != false)
        {
          $this->objects[$i] = $this->obj->createData($i);
        }
      }
    }
  }

  public function setObjectsNum($num)
  {
    $this->objectsnum = 0;
    $ids = $this->obj->getIds();
    if ( empty($ids) )
    {
      $this->message['nodata'] = 'NO DATA';
      $this->objectsnum = 0;
    }
    elseif ( $num > count($ids) )
    {
      $this->objectsnum = count($ids);
    }
    else
    {
      $this->objectsnum = $num;
    }
  }

  public function renderObjects()
  {
    if ( $this->objects != null )
    {
      foreach ($this->objects as $key => $value)
      {
          echo '<form action="#" method="post">';
          echo '<table class="objects">';
          echo '<tr class="title">';
          echo '<th class="title">';
          echo $this->objects[$key]->getId();
          echo '</th>';
          echo '<td>';
          echo '<a href="index.php'.'?id=';
          echo $this->objects[$key]->getId();
          echo '">';
          echo $this->objects[$key]->getTitle();
          echo '</a>';
          echo '</td>';
          echo '</tr>';
          echo '<tr>';
          echo '<td colspan="2">';
          echo '<p>';
          echo str_replace(array("\r\n","\r","\n"),'<br />',$this->objects[$key]->getBody());
          echo '</p>';
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
      }
    }
    else
    {
       echo '<table class="objects">';
       echo '<tr class="title">';
       echo '<td>';
       echo $this->message['nodata'];
       echo '</td>';
       echo '</tr>';
       echo '</table>';
    }
  }

  public function renderError()
  {
    if( isset($this->message) )
    {
      $error = array(
        'title',
        'body',
        'delete',
      );
      echo '<tr>';
      echo '<td class="error" colspan=2>';
      for ( $i = 0; $i < count($error); $i++ )
      {
        if ( array_key_exists($error[$i],$this->message) )
        {
          echo $this->message[$error[$i]] . "<br />";
        }
      }
      echo '</td>';
      echo '</tr>';
    }
  }

  public function delete($id)
  {
    $title = $this->obj->getTitleById($id);
    $this->obj->delete($id);
    $this->deleteid = null;
    $this->message['delete'] = '[ ' . $title . ']を削除しました。';
  }

  public function searchObject($id)
  {
    $this->objects;
    foreach ( $this->objects as $key => $value )
    {
      if ($this->objects[$key]->getId() == $id )
      {
        return $this->objects[$key];
      }
    }
  }

}
