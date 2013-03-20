<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="template/main.css" />
  <title><?php echo $this->escapeText($this->pagetitle); ?></title>
</head>

<body>
  <div id="container">
    <div id="header">
    <a href="http://test2.local">test2.local</a>
    </div>

    <div id="sidebar">
      <ul>
        <?php //$this->renderSideBar(25); ?>
      </ul>
    </div>

    <?php if($this->pageid == ''): ?>
      <div id="write">
          <?php $this->callTemplate('template/inputform.php'); ?>
      </div>

      <div id="content">
      <?php $this->renderObjects(); ?>
      </div>

    <?php else: ?>
      <div id="objectpage">
        <?php $object = $this->searchObject($this->pageid) ?>
        <table>
          <tr>
            <td>
              <?php echo $this->escapeText($object->gettitle()) ?>
            </td>
          <tr>
            <td>
              <?php echo $this->renderBody($object->getBody()) ?>
            </td>
          </tr>
        </table>
      </div>
    <?php endif; ?>


    <div id="footer">
    </div>
  </div>

</body>
</html>
