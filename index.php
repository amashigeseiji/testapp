<!DOCTYPE html>
<html>
<?php
require_once("action.class.php");
$action = new action();

if($_POST)
{
  $action->writeData($_POST);
}

function showtitle()
{
  global $action;
  if(!$_GET){
    echo 'test';
  }else{
    echo $action->showTitleById($_GET["id"]);
  }
}
?>
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="./main.css" />
  <title><?php showtitle(); ?></title>
</head>

<body>
  <div id="container">
    <div id="header">
    <h2><a href="http://test2.local">test2.locsl</a></h2>
    </div>

    <div id="sidbar">
      <ul>
        <?php
        for ($i = $action->getLastId(); $i >= $action->getLastId() - 10; $i--)
        {
          echo "<li><a href=index.php".'?id='.$i.">";
          echo $action->showTitleById($i);
          echo '</a></li>';
        }
        ?>
      </ul>
    </div>

    <div id="content">
      <?php 
        if(!null == $_GET)
        {
          echo '<h3>title : ';
          echo $action->showTitleById($_GET["id"]);
          echo '</h3>';
          echo $action->showBodyById($_GET["id"]);
        }
        else
        {
         include("inputform.html");
        }
      ?>
    </div>

    <div id="footer">
    </div>
  </div>

</body>
</html>
