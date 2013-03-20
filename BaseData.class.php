<?php
class BaseData
{
  private $basedata;
  private $path;
  public $defaultpath = 'data/data.txt';
  public $errormessage = array();
  private $errorlog = 'data/errolog';
//  public $lastid = '';

  public function initialize()
  {
    $this->basedata = array();
    $this->errormessage = array();

    $this->path = '';
    if (null == $this->path)
    {
     $this->setPath($this->defaultpath);
    }

    if ( file_exists($this->path) )
    {
      //filepermissionの確認.775に設定する
      if (substr(sprintf('%o',fileperms($this->path)),-4) != 0775)
      {
        chmod($this->path,0775);
      }

      $lines = array();
      $lines = file($this->path);

      for ($i = 0; $i <= count($lines) -1; $i++)
      {
        $this->basedata[$i] = explode(",", $lines[$i]);
      }
    }
    //var_dump($this->basedata); exit;
  }

  public function setPath($file)
  {
    if (file_exists($file) && is_dir($file))
    {
      $this->errormessage = array();
      $this->errormessage[] .= '[' . __method__ . '] file:' . $file . " is directory.";
      $this->errorLog();
      $this->path = $this->defaultpath;
    }
    elseif (!file_exists($file))
    {
      $this->errormessage = array();
      $this->errormessage[] .= '[' . __method__ . '] file:' . $file . " not exist.";
      $this->errorLog();
      $this->path = $this->defaultpath;
    }
    else
    {
      $this->path = $file;
    }
  }

  public function getIds()
  {
    $ids = array();
    if ( !empty($this->basedata) )
    {
      //TODO もっと綺麗に書けるはず…
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
    for($i = 0; $i < count($ids); $i++)
    {
      if ($ids[$i] == $id)
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
    $basedata = $this->basedata;
    for ($i = 0; $i < count($basedata); $i++)
    {
      if ( $basedata[$i][0] == $id && $basedata[$i][1] == 'title')
      {
        return $title = $basedata[$i][2];
      }
    }

    return null;
  }

  public function getBodyById($id)
  {
    $body = '';
    $a = $this->basedata;
    $tmp = array();
    for ($i = 0; $i <= count($a) - 1; $i++)
    {
      if ( $a[$i][0] == $id && $a[$i][1] == 'body')
      {
        $tmp[] = $a[$i][2];
      }
    }
    if (isset($tmp))
    {
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

  public function writeDate()
  {
    $fp = fopen($this->path, "a");
    fwrite($fp, $this->getLastId() . ',' . 'date,' . date("Y-m-d H:i:s") . "\n");
    fclose($fp);
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

     $this->initialize();
    }
    else
    {
      $this->errormessage = array();
      $this->errormessage[] .= '[' . __method__ . '] id:' . $id . ' no data.';
      $this->errorLog();
    }
  }

  public function editTitle($id,$input)
  {
    //データの更新
    $this->initialize();
  }

  public function editBody($id)
  {
    $body = $this->getBodyById($id);
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

      return $obj;
    }
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
