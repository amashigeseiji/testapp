<?php
//require_once('action.class.php');
require_once('user.action.class.php');
$auth = null;
if ($auth == null)
{
  $auth = new UserAction;
}
