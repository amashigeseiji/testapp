<?php
class dataload
{
  private $data;
  public $title = '';
  public $body = '';
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
     $this->setPath('data.txt');
    }
    $lines = array();
    $lines = file($this->path);

    for ($i = 0; $i <= count($lines) -1; $i++)
    {
      $this->data[$i] = explode(",", $lines[$i]);
    }
//    $this->getLastId();
    //var_dump($this->data); exit;
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
    $this->title = '';
    $a = $this->data;
    for ($i = 0; $i <= count($a) - 1; $i++)
    {
      if ( $a[$i][0] == $id && $a[$i][1] == 'title')
      {
        return $this->title = $a[$i][2];
        break;
      }
    }
  }

  public function getBody($id)
  {
    $this->body = '';
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
      $this->body .= $tmp[$i];
    }

    return $this->body;
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

}
