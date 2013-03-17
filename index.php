<?php
require_once("action.class.php");
if(!isset($action))
{
  $action = new action();
}

if(array_key_exists("body",$_POST) && array_key_exists("title",$_POST))
{
  if ($_POST['title'] != null  && $_POST['body'] != null)
  {
    $action->writeData($_POST);
  }
  elseif($_POST['title'] != null)
  {
    echo '本文を入力してください';
  }
  else
  {
    echo 'タイトルを入力してください.';
  }
}

include_once("./template/template.php");

