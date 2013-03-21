<?php function plus(){$n = 10;$n + 10;} ?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="template/main.css" />
  <title><?php echo $this->escape($this->pagetitle); ?></title>
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
        <?php $n = 10 ?>
        <?php $this->renderObjects($n); ?>
      </div>

    <?php else: ?>
      <div id="objectpage">
        <?php $this->isObject($this->pageid)? $object = $this->object:$object = null; ?>
        <form action="#" method="post">
          <table>
            <tr>
              <td>
                <?php echo $this->escape($object->getTitle()) ?>
              </td>
            </tr>
            <tr>
              <td>
                <?php echo $this->renderBody($object->getBody()) ?>
              </td>
            </tr>
            <tr>
              <td class="delete" colspan="2">
                <input type="hidden" value="<?php echo $object->getId() ?>" name="delete" />
                <input type="submit" value="削除" />
              </td>
            </tr>
          </table>
        </form>
      </div>
    <?php endif; ?>


    <div id="footer">
    </div>
  </div>

</body>
</html>
