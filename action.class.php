<?php
include('file.class.php');
class action
{
  function __construct()
  {
    $this->obj = "";
    $this->createInstance();
  }

  public function createInstance()
  {
    $this->obj = new dataload;
  }

  public function showDataById($id)
  {
    $this->showTitleById($id);
    $this->showBodyById($id);
  }

  public function showTitleById($id)
  {
    return $this->obj->getTitle($id);
  }

  public function showBodyById($id)
  {
    return $this->obj->getBody($id);
  }

  public function writeData($input)
  {
    $this->obj->writeTitle($input["title"]);
    $this->obj->writeBody($input["body"]);
  }

  public function getLastId()
  {
    return $this->obj->getLastId();
  }
}
