<?php
require_once('user.action.class.php');
$auth = null;
if ($auth == null)
{
  $auth = new UserAction;
}
