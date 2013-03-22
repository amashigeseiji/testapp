<?php
class BaseData
{
  private
    $basedata = array(),
    $path,
    $defaultpath = 'data/data.txt',
    $errormessage = array(),
    $errorlog = 'data/errolog',
    $objects,
    $objectsnum,
    $message = array(
      'nodata'=>'NO DATA',
      'deleted'=>'',
      'write'=>'');



  public function initialize()
  {
    $this->errormessage = array();

    $this->path = $this->defaultpath;
    //if (null == $this->path)
    //{
    // $this->setPath($this->defaultpath);
    //}

    $this->load();
    //var_dump($this->basedata); exit;
  }

  public function load()
  {
    $this->basedata = array();
    $lines = array();
    $lines = file($this->path);

    for ($i = 0; $i <= count($lines) -1; $i++)
    {
      $this->basedata[$i] = explode(",", $lines[$i]);
    }
    //var_dump($this->basedata);exit;
  }

  public function setPath($file)
  {
    /*todo 外部からファイル設定できるようにメソッド作ってるけど…
    * 用途あるっけ
    * ファイルの存在チェックにはなるけど
    * 書き込むときとか逆に邪魔な気もする
    * ファイルが無いとdefaultpathで設定しちゃうので…
    * 作りがよくないのですな
    * ユーザが直にファイルをセッティングするとかは不要で、
    * 自動処理をするためにはこういう機構が必要
    * ファイルサイズが指定された値になったら
    * 自動で新規ファイルを生成するとかのがよっぽどよい
    */
    if (file_exists($file) && is_dir($file))
    {
      $this->errormessage = array();
      $this->errormessage[] .= '[' . __method__ . '] file:' . $file . " is directory.";
      $this->errorLog();
      $this->path = $this->defaultpath;

      return false;
    }
    elseif (!file_exists($file))
    {
      $this->errormessage = array();
      $this->errormessage[] .= '[' . __method__ . '] file:' . $file . " not exist.";
      $this->errorLog();
      $this->path = $this->defaultpath;

      return false;
    }
    else
    {
      $this->path = $file;

      return true;
    }
    //filepermissionの確認.775に設定する
    //if (substr(sprintf('%o',fileperms($this->path)),-4) != 0775)
    //{
    //  chmod($this->path,0775);
    //}
  }

  public function getIds()
  {
    $ids = array();
    if ( !empty($this->basedata) )
    {
      //TODO もっと綺麗に書けるはず…
      //初回書き込み時にはこの条件に合致するので必須
      //(タイトルの書き込みがあってから本文を書き込むので)
      if ( count($this->basedata) == 1 )
      {
        $ids[0] = $this->basedata[0][0];

        return $ids;
      }

      if ( $this->basedata[0][0] == $this->basedata[1][0])
      {
        $ids[0] = $this->basedata[0][0];
      }
      for ( $i = 1; $i <= count($this->basedata) -1; $i++)
      {
        if ( $this->basedata[$i][0] != null && $this->basedata[$i][0] != $this->basedata[$i-1][0] )
        {
          $ids[] .= $this->basedata[$i][0];
        }
      }
    }

    return $ids;
  }

  public function isData($id)
  {
    //データがあれば true, なければ falseを返す
    $ids = $this->getIds();
    foreach ( $ids as $key => $value )
    {
      if ( $ids[$key] == $id )
      {
        return true;
      }
    }

    return false;
  }

  public function getLastId()
  {
    //$idsに値が無い場合は0を返す
    $ids = $this->getIds();
    if ( !empty($ids) )
    {
      return max($ids);
    }
    else
    {
      return 0;
    }
  }

  public function getTitleById($id)
  {
    $title = '';
    for ($i = 0; $i < count($this->basedata); $i++)
    {
      if ( $this->basedata[$i][0] == $id && $this->basedata[$i][1] == 'title')
      {
        return $title = $this->basedata[$i][2];
      }
    }

    return null;
  }

  public function getBodyById($id)
  {
    $body = '';
    $tmp = array();
    if ( isset($this->basedata) )
    {
      for ($i = 0; $i <= count($this->basedata) - 1; $i++)
      {
        if ( $this->basedata[$i][0] == $id && $this->basedata[$i][1] == 'body')
        {
          $tmp[] = $this->basedata[$i][2];
        }
      }

      for ( $i = 0; $i <= count($tmp) -1; $i++)
      {
        $body .= $tmp[$i];
      }

      return $body;
    }
    else
    {
      return null;
    }
  }

  public function getCreatedAtById($id)
  {
    $created_at = '';
    for ($i = 0; $i < count($this->basedata); $i++)
    {
      if ( $this->basedata[$i][0] == $id && $this->basedata[$i][1] == 'created_at')
      {
        return $created_at = $this->basedata[$i][2];
      }
    }

    return null;
  }

  public function writeData($input)
  {
    if ( $this->writeTitle($input["title"]) == true )
    {
      $this->writeBody($input["body"]);
      $this->writeCreatedAt();
      $this->message['write'] = 'SUCCESS';
    }
    else
    {
      $this->errormessage = array();
      $this->errormessage[] .= '[' . __method__ . '] write failed.';
      $this->errorLog();
    }
  }

  //title データが body より先にくる想定
  public function writeTitle($input)
  {
    //書き込み成功時にはtrue,失敗時にはfalseを返す
    $lastid = $this->getLastId();
    if ((file_exists($this->path)) && ($fp = fopen($this->path, "a")))
    {
      fwrite($fp, $lastid + 1 . ',' . 'title,' . $input . "\n");
      fclose($fp);
      //データの更新
      $this->initialize();

      return true;
    }
    else
    {
      $this->errormessage = array();
      $this->errormessage[] .= '[' . __method__ . '] file:' . $this->path . ' can not open.';
      $this->errorLog();

      return false;
    }
  }

  //title のデータが存在することを前提にする(titleよりもbodyをさきに書き込むとid順がくずれる)
  public function writeBody($input)
  {
    $fp = fopen($this->path, "a");
    $tmp = explode("\n",$input);
    for( $i = 0; $i <= count($tmp) -1; $i++)
    {
      fwrite($fp, $this->getLastId() . ',' . 'body,' . $tmp[$i] . "\n");
    }
    fclose($fp);
    //データの更新
    $this->initialize();
  }

  public function writeCreatedAt()
  {
    $fp = fopen($this->path, "a");
    fwrite($fp, $this->getLastId() . ',' . 'created_at,' . date("Y-m-d H:i:s") . "\n");
    fclose($fp);
    //データの更新
    $this->initialize();
  }

  public function delete($id)
  {
    if ( $this->isData($id) == true )
    {
     $data = file($this->path);
     $deleted = preg_grep("/^$id,/",$data,PREG_GREP_INVERT);
     $fp = fopen($this->path,"w");
     foreach ($deleted as $key => $value)
     {
       fwrite($fp,$deleted[$key]);
     }
     fclose($fp);
    //データの更新
     $this->initialize();
    }
    else
    {
      $this->errormessage = array();
      $this->errormessage[] .= '[' . __method__ . '] id:' . $id . ' no data.';
      $this->errorLog();
    }
  }

  public function edit($id,$input)
  {
    $this->editTitle();
    $this->editBody();
    //処理を記述
  }
  public function editTitle($id,$input)
  {
    //処理を記述
    //データの更新
    $this->initialize();
  }

  public function editBody($id,$input)
  {
    //置換前が一行ならいけるけど複数行あるとだめ
    //まだ不正データが入るので使えない
    if ( isset($input['body']) && $this->isData($id) == true )
    {
      $body = $this->getBodyById($id);
      echo '置換前：'.$body;
      $tmp = file($this->path);
      $pattern = "/^$id,body,.*/";
      $replacement = $id.',body,'.$input['body'];

      $a = preg_replace($pattern,$replacement,$tmp);

      $fp = fopen($this->path,"w");
      foreach ($a as $key => $value)
      {
        fwrite($fp,$a[$key]);
      }
      fclose($fp);

     //データの更新
     $this->initialize();
      //テスト用
      $body = $this->getBodyById($id);
      echo '置換後：'.$body;

     return true;
    }
    else
    {

      return false;
    }
    //処理を記述
    //データの更新
    $this->initialize();
  }

  public function createData($id)
  {
    if($this->isData($id) == true)
    {
      include_once('Data.class.php');
      $obj = new Data;
      $obj->setId($id);
      $obj->setTitle($this->getTitleById($id));
      $obj->setBody($this->getBodyById($id));
      $obj->setCreatedAt($this->getCreatedAtById($id));

      return $obj;
    }
  }

  public function createAllData()
  {
    //順番に入るためcreateObjectsとは逆の挙動
    $objects = array();
    $ids = $this->getIds();
    foreach ( $ids as $key => $value)
    {
      $objects[$key] = $this->createData($value);
    }

    return $objects;
  }

  public function createObjects($num)
  {
    $this->objects = null;
    $ids = $this->getIds();
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
      for ( $i = $this->getLastId(); $i > count($ids) - $num; $i-- )
      {
        if($this->isData($i) != false)
        {
          $this->objects[$i] = $this->createData($i);
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
    $ids = $this->getIds();
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

  public function getObjects()
  {
    return $this->objects;
  }

  public function getMessage()
  {
    return $this->message;
  }

  public function errorLog()
  {
    if ( isset($this->errormessage) )
    {
      $fp = fopen($this->errorlog,"a");
      foreach ( $this->errormessage as $key => $value )
      {
        fwrite($fp,date("Y-m-d H:i:s") . " " . $this->errormessage[$key] . "\n");
      }
      fclose($fp);
    }
  }

}
//$a=new BaseData;
//$b=array('title'=>'tetete','body'=>'unko');
//$a->initialize();
//$input['body'] = "置換したよ!!\ntest";
//$a->editBody(35,$input);
