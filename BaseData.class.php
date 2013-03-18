<?php
class BaseDataClass
{
  private $data;
  public $title = '';
  public $deleteids = array();
  public $delpath = 'data/del.dat';
//  public $body = '';
//  public $lastid = '';

  function __construct()
  {
    $this->initialize();
    $this->getLastId();
  }

  private function initialize()
  {
    $this->data = array();
    $this->path = '';
    if (null == $this->path)
    {
     $this->setPath('./data/data.txt');
    }
    $lines = array();
    $lines = file($this->path);

    for ($i = 0; $i <= count($lines) -1; $i++)
    {
      $this->data[$i] = explode(",", $lines[$i]);
    }
//    $this->getLastId();
    //var_dump($this->data); exit;
    $this->loadDeleteFlag();
  }

  public function setPath($file)
  {
    if (file_exists($file) && is_dir($file))
    {
      echo $file . " is directory.\n";
    }
    elseif(!file_exists($file))
    {
      echo $file . " is not exist.\n";
    }
    else
    {
      return $this->path = $file;
    }
  }

  public function getTitle($id)
  {
    $title = '';
    $a = $this->data;
    for ($i = 0; $i <= count($a) - 1; $i++)
    {
      if ( $a[$i][0] == $id && $a[$i][1] == 'title')
      {
        return $title = $a[$i][2];
        break;
      }
    }
  }

  public function getBody($id)
  {
    $body = '';
    $a = $this->data;
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

  public function getLastId()
  {
    $ids = array();
    for ( $i = 0; $i <= count($this->data) -1; $i++)
    {
      $ids[] = $this->data[$i][0];
    }
    return max($ids);
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

  //ナンカ違ウ…
//  public function getDeleteFlagById($id)
//  {
//    for ( $i = 0; $i <= count($this->del) -1; $i++)
//    {
//      if ( $id == $this->deleteids[$i])
//      {
//        return 
//      }
//    }
//  }

  public function delete()
  {
    $cmd = "cat data/data.txt | grep -v ^$id > data/data.txt";
    shell_exec($cmd);
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
//    var_dump($this->deleteids); exit;
  }

  public function editTitle($id,$input)
  {
    $title = $this->getTitle($id);
    $title = $input["title"];
    $fp = fopen($this->path, "a");
    fwrite($fp, $this->getLastId() + 1 . ',' . 'title,' . $input . "\n");
    fclose($fp);
    //データの更新
    $this->initialize();
  }

  public function editBody($id)
  {
    $body = $this->getBody($id);
  }
}

class DataClass extends BaseDataClass
{
  public $id = '';
  public $title = '';
  public $body = '';
  private $delkey = false;
}
