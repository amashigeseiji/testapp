<?php
class BaseData
{
  private $basedata;
  private $path;
  public $errormessage = array();
//  public $title = '';
//  public $deleteids = array();
//  public $delpath = 'data/del.dat';
//  public $obj;
//  public $body = '';
//  public $lastid = '';

  public function initialize()
  {
    $this->basedata = array();

    $this->path = '';
    if (null == $this->path)
    {
     $this->setPath('./data/data.txt');
    }

    $lines = array();
    $lines = file($this->path);

    for ($i = 0; $i <= count($lines) -1; $i++)
    {
      $this->basedata[$i] = explode(",", $lines[$i]);
    }
    //var_dump($this->basedata); exit;
    //$this->loadDeleteFlag();
    //$this->createData();
  }

  public function setPath($file)
  {
    if (file_exists($file) && is_dir($file))
    {
      $this->errormessage[] .= $file . " is directory.";
    }
    elseif(!file_exists($file))
    {
      $this->errormessage[] .= $file . " is not exist.";
    }
    else
    {
      return $this->path = $file;
    }
  }

  public function getIds()
  {
    $ids = array();
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

    return $ids;
  }

  public function getLastId()
  {
    return max($this->getIds());
  }

  public function getTitleById($id)
  {
    $title = '';
    $a = $this->basedata;
    for ($i = 0; $i < count($a); $i++)
    {
      if ( $a[$i][0] == $id && $a[$i][1] == 'title')
      {
        return $title = $a[$i][2];
        break;
      }
    }
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
    for ( $i = 0; $i <= count($tmp) -1; $i++)
    {
      $body .= $tmp[$i];
    }

    return $body;
  }

  //title データが body より先にくる想定
  public function writeTitle($input)
  {
    $fp = fopen($this->path, "a");
    fwrite($fp, $this->getLastId() + 1 . ',' . 'title,' . $input . "\n");
    fclose($fp);
    //データの更新
    $this->initialize();
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

  /*ナンカ違う…
  public function getDeleteFlagById($id)
  {
    for ( $i = 0; $i <= count($this->del) -1; $i++)
    {
      if ( $id == $this->deleteids[$i])
      {
        return 
      }
    }
      }*/

  public function delete($id)
  {
    $file = $this->path;
    $cmd = "sh sandbox/delete.sh $file $id";
    shell_exec($cmd);
    $this->initialize();
  }

  public function setDeleteFlag($id)
  {
    $fp = fopen("$this->delpath","a");
    fwrite($fp, $id);
    fclose($fp);
    $this->loadDeleteFlag();
  }

  public function loadDeleteFlag()
  {
    $this->deleteids = array();
    $this->deleteids = file("$this->delpath");
  }

  public function editTitle($id,$input)
  {
    $title = $this->getTitleById($id);
    $title = $input["title"];
    $fp = fopen($this->path, "a");
    fwrite($fp, $this->getLastId() + 1 . ',' . 'title,' . $input . "\n");
    fclose($fp);
    //データの更新
    $this->initialize();
  }

  public function editBody($id)
  {
    $body = $this->getBodyById($id);
  }

  public function createData($id)
  {
    include_once('Data.class.php');
    $obj = new Data;
    $obj->setId($id);
    $obj->setTitle($this->getTitleById($id));
    $obj->setBody($this->getBodyById($id));

    return $obj;
  }

}
