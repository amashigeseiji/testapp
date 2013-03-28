<?php
class BaseData extends BaseUser
{
  private
    $basedata = array(),
    $linecount,
    $path,
    $defaultpath = 'data/data.txt',
    $errormessage = array(),
    $errorlog = 'data/errolog',
    $objectsnum = 0;
  public
    $message = array(
      'nodata'=>'NO DATA',
      'deleted'=>'',
      'write'=>''),
    $objects;


  public function __construct()
  {
    $this->initialize();
  }

  public function initialize()
  {
    $this->objects = null;
    $this->objectsnum = 0;
    $this->errormessage = array();
    $this->path = $this->defaultpath;
    //if (null == $this->path)
    //{
    // $this->setPath($this->defaultpath);
    //}

    $this->load();
    $this->setObjectsNum($this->objectsnum);
    $this->createObjects($this->objectsnum);
    //var_dump($this->basedata); exit;
  }

  public function load()
  {
    $lines = array();
    $this->linecount = 0;
    $this->basedata = array();

    $lines = file($this->path);
    $this->linecount = count($lines);

    for ($i = 0; $i <= count($lines) -1; $i++)
    {
      $this->basedata[$i] = explode(",", $lines[$i]);
    }

    //var_dump($this->basedata);exit;
    //echo count($this->basedata);exit;
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

    if ( $this->linecount != 0 )
    {
      //依存関係上このオブジェクトの正常性を保証するのがこのメソッド
      //初回書き込み時にはこの条件に合致するので必須
      if ( $this->linecount == 1 )
      {
        $ids[0] = $this->basedata[0][0];

        return $ids;
      }

      if ( $this->linecount >= 2 )
      {
        if ( $this->basedata[0][0] == $this->basedata[1][0])
        {
          $ids[0] = $this->basedata[0][0];
        }
        else
        {
          //不正データが存在する
          $this->errormessage = array();
          $this->errormessage[] .= '[' . __method__ . '] data has null id.';
          $this->errorLog();
        }

        for ( $i = 1; $i <= $this->linecount -1; $i++)
        {
          if ( $this->basedata[$i][0] != $this->basedata[$i-1][0] )
          {
            if ( isset($this->basedata[$i][0]) )
            {
              $ids[] .= $this->basedata[$i][0];
            }
            else
            {
              //不正データが存在する
              $this->errormessage = array();
              $this->errormessage[] .= '[' . __method__ . '] data has null id.';
              $this->errorLog();
            }
          }
        }
      }

      return $ids;
    }
  }

  public function isData($id)
  {
    //データがあれば true, なければ falseを返す
    $ids = $this->getIds();
    if ( isset($ids) )
    {
      foreach ( $ids as $key => $value )
      {
        if ( $ids[$key] == $id )
        {
          return true;
        }
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

  public function getPostedByById($id)
  {
    $posted_by = '';
    for ($i = 0; $i < count($this->basedata); $i++)
    {
      if ( $this->basedata[$i][0] == $id && $this->basedata[$i][1] == 'posted_by')
      {
        return $created_at = $this->basedata[$i][2];
      }
    }

    return null;
  }

  public function writeData($input,$token)
  {
    $this->message['write'] = '';

    if ( $this->writeTitle($input) == true )
    {
      if ( $this->writeBody($input) == true )
      {
        $this->writeCreatedAt();
        $this->writePostedBy($token);
        $this->initialize();

        return $this->message['write'] = 'SUCCESS';
      }
      else
      {

        return $this->message['write'] = 'FAIL';
      }
    }
    else
    {

      return $this->message['write'] = 'FAIL';
    }
  }

  //title データが body より先にくる想定
  public function writeTitle($input)
  {
    //書き込み成功時にはtrue,失敗時にはfalseを返す
    $lastid = $this->getLastId();
    if ((file_exists($this->path)) && ($fp = fopen($this->path, "a")))
    {
      if ( array_key_exists('title',$input) && isset($input['title']) )
      {
        fwrite($fp, $lastid + 1 . ',' . 'title,' . $input['title'] . "\n");
        fclose($fp);
        //データの更新
        $this->initialize();
      }

      if ( $this->isData($this->getLastId()) && $this->getTitleById($this->getLastId()) == $input['title'] . "\n" )
      {

        return true;
      }
      else
      {
        //不正データが存在する
        $this->errormessage = array();
        $this->errormessage[] .= '[' . __method__ . '] titledata write faild.';
        $this->errorLog();

        return false;
      }
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
    if ( array_key_exists('body',$input) && isset($input['body']) )
    {
      $fp = fopen($this->path, "a");
      $tmp = explode("\n",$input['body']);
      for( $i = 0; $i <= count($tmp) -1; $i++)
      {
        fwrite($fp, $this->getLastId() . ',' . 'body,' . $tmp[$i] . "\n");
      }
      fclose($fp);
      //データの更新
      $this->initialize();

      return true;
    }
    else
    {
      $this->errormessage = array();
      $this->errormessage[] .= '[' . __method__ . '] bodydata write faild.';
      $this->errorLog();

      return false;
    }
  }

  public function writeCreatedAt()
  {
    $fp = fopen($this->path, "a");
    fwrite($fp, $this->getLastId() . ',' . 'created_at,' . strtotime("now") . "\n");
    fclose($fp);
    //データの更新
    $this->initialize();
  }

  public function writePostedBy($token)
  {
    $username = $this->getUserNameByToken($token);
    $fp = fopen($this->path, "a");
    fwrite($fp, $this->getLastId() . ',' . 'posted_by,' . $username . "\n");
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
    if( $this->isData($id) == true )
    {
      include_once('Data.class.php');
      $obj = new Data;
      $obj->setId($id);
      $obj->setTitle($this->getTitleById($id));
      $obj->setBody($this->getBodyById($id));
      $obj->setCreatedAt($this->getCreatedAtById($id));
      $obj->setPostedBy($this->getPostedByById($id));

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
   * setObjectsNum()に0以外の値を渡し、かつ配列要素が存在するときのみ、
   * $objectsnumに値が代入される
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

  public function setMessage($message)
  {
    $this->message = array(
      'nodata'    =>  '',
      'deleted'   =>  '',
      'write'     =>  '',
      );

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
