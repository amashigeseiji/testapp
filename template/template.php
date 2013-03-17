<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="template/main.css" />
  <title><?php $action->showtitle(); ?></title>
</head>

<body>
  <div id="container">
    <div id="header">
    <a href="http://test2.local">test2.local</a>
    </div>

    <div id="sidebar">
      <ul>
        <?php $action->renderSideBar(6); ?>
      </ul>
    </div>

    <div id="content">
      <?php $action->renderContent(); ?>
    </div>

    <div id="footer">
    </div>
  </div>

</body>
</html>
