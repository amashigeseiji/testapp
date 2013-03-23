<?php
require_once('user.action.class.php');
require_once('template/auth.php');
$auth = new UserAction;
if ( isset($_POST['name']) && isset($_POST['password']) )
{
  $auth->Authentication();
}
