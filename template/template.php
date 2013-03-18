<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="template/main.css" />
  <title><?php $this->showtitle(); ?></title>
</head>

<body>
  <div id="container">
    <div id="header">
    <a href="http://test2.local">test2.local</a>
    </div>

    <div id="sidebar">
      <ul>
        <?php $this->renderSideBar(15); ?>
      </ul>
    </div>

    <div id="content">
      <?php //$this->renderContent(); ?>
      <?php $this->renderObjects(5); ?>
    </div>

    <div id="footer">
    </div>
  </div>

</body>
</html>
