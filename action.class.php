<?php
class Action
{
  private
    $message = array(),
    $pagetitle,
    $defaulttitle = 'testpage',
    $template = 'template/template.php',
    $object,
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
    //TODO メッセージ出力をもうちょい細かく制御する
    //現状だと削除時に削除メッセージと同時に
    //idが存在しない旨表示されてしまう
    $this->message = array();
    $this->obj = null;
    $this->pagetitle = '';
    $this->object = null;
    $this->objects = null;
    $this->objectsnum = 0;
    $this->submited = array(
      'title'   => '',
      'body'    => '',
      'delete'  => '',
    );
    $this->pageid = '';


    $this->createInstance();

    $this->setSubmited();
    //データの書き込み処理
    if ( null != $this->submited['delete'] )
    {
      $this->delete($this->getId());
      $this->setObjects($this->objectsnum);
    }
    if ( null != $this->submited['title'] && null != $this->submited['body'] )
    {
      $this->writeData($this->submited);
      $this->setObjects($this->objectsnum);
    }

    $this->setPageId();
    $this->setPageTitle();
    $this->callTemplate($this->template);
  }

  private function createInstance()
  {
    include_once('BaseData.class.php');
    $this->obj = new BaseData;
    $this->obj->initialize();
    $this->setObjects($this->objectsnum);
  }

  /* setObjectNumで0を渡した場合、
   * 必ず$this->objectnumには0が設定される
   * createObjectsにも0が渡されるので
   * 全件出力になる
   */
  private function setObjects($num)
  {
    $this->objects = null;
    $this->obj->setObjectsNum($num);
    $this->obj->createObjects($this->objectsnum);
    $this->objects = $this->obj->getObjects();
  }

  private function createObjects($num)
  {
    $this->objects = null;
    $ids = $this->obj->getIds();
    //$numに0が入っていれば全件出力
    //次のfor文でのforの終了条件を0にする
    if ( $num == 0 )
    {
      $num = count($ids);
    }
    //TODO 計算条件ほんとにあってるのか?確認する(lastidで初期化していいのか?)
    //$numが0以外であれば$num件出力
    //setObjectNumでidsの検査はしているので
    //データが無い旨のメッセージ出力は不要だが
    //データがなくてもエラーとは言えないためidsが空でここにくることはある
    //
    if ( !empty($ids) );
    {
      for ( $i = $this->obj->getLastId(); $i > count($ids) - $num; $i-- )
      {
        if($this->obj->isData($i) != false)
        {
          $this->objects[$i] = $this->obj->createData($i);
        }
        else
        {
          $num + 1;
        }
      }
    }
  }

  /*
   * $numに0を渡した場合、
   * 配列要素($ids = データ)があるなら必ず最終条件にくるので、
   * 0が渡されたらかならず0を返す
   * setObjectNum()に0以外の値を渡し、かつ配列要素が存在するときのみ、
   * $objectnumに値が代入される
   */
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

  public function getId()
  {
    //getの処理が甘いみたいなので修正する
    if (array_key_exists("id",$_GET))
    {
      if ( $this->obj->isData($_GET['id']))
      {
        return $_GET["id"];
      }
      else
      {
        $this->message['id'] = 'id' . $_GET["id"] . 'は存在しないデータです。';
        return null;
      }
    }
    else
    {
      return null;
    }
  }

  public function setSubmited()
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
       $this->submited['title'] = '';
       $this->submited['body'] = '';
       $this->submited['delete'] = '';
    }
  }

  public function escape($text)
  {
    return htmlspecialchars($text);
  }

  public function renderBody($text)
  {
    return str_replace(array("\r\n","\r","\n"),'<br />',$this->escape($text));
  }

  public function writeData($input)
  {
    if ( $this->obj->writeTitle($input["title"]) == true )
    {
      $this->obj->writeBody($input["body"]);
      $this->obj->writeCreatedAt();
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

  public function callHtml($file)
  {
    $template = file($file);
    for ( $i = 0; $i < count($template); $i++ )
    {
      print $template[$i];
    }
  }

  public function callTemplate($file)
  {
    include_once($file);
  }

  /*
   * objectpageに一覧を表示
   */
  public function renderObjects($n)
  {
    if ( $this->objects != null )
    {
      for ( $i = $this->obj->getLastId(); $i > count($this->objects) - $n; $i-- )
      {
        if ( $this->isObject($i) )
        {
          //echo '<form action="#" method="post">';
          echo '<table class="objects">';
          echo '<tr class="title">';
          echo '<th class="title">';
          echo $this->object->getId();
          echo '</th>';
          echo '<td>';
          echo '<a href="index.php'.'?id=';
          echo $this->object->getId();
          echo '">';
          echo $this->object->getTitle();
          echo '</a>';
          echo '</td>';
          echo '</tr>';
          echo '<tr>';
          echo '<td colspan="2">';
          echo '<p>';
          echo $this->renderBody($this->object->getBody());
          echo '</p>';
          echo '</td>';
          echo '</tr>';
          //echo '<tr>';
          //echo '<td class="delete" colspan="2">';
          //echo '<input type="hidden" value="' . $key . '" name="delete" />';
          //echo '<input type="submit" value="削除" />';
          //echo '</td>';
          //echo '</tr>';
          echo '</table>';
          //echo '</form>';
        }
        else
        {
          $n + 1;
        }
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
      //エラーとして出力したいものを配列に入れる
      $error = array(
        'title',
        'body',
        'delete',
        'id',
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
    if ( $this->isObject($id) == true )
    {
      $object = $this->object;
    }
    //$title = $this->obj->getTitleById($id);
    $title = $object->getTitle();
    $this->obj->delete($id);
    $this->deleteid = null;
    $this->message['delete'] = '[ ' . $title . ']を削除しました。';
  }

  /* 現在オブジェクトが生成されているかどうか
   * 生成されていればobject変数に該当するobjectを代入し、
   * trueを返す。いなければfalseを返す
   * ポインタみたいなものとして利用する想定
   */
  public function isObject($id)
  {
    foreach ( $this->objects as $key => $value )
    {
      if ($this->objects[$key]->getId() == $id )
      {
        $this->object = $this->objects[$key];
        return true;
      }
    }

    return false;
  }

}
