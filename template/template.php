<!-- vim: set foldmethod=marker: -->
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="template/main.css" />
  <title><?php echo $this->escape($this->pagetitle); ?></title>
</head>

<body>
  <div id="container">

    <!-- ヘッダー領域{{{ -->
    <div id="header">

      <div id="logo">
        <a href="/">test2.local</a>
      </div>

      <div id="logout">
        <form action="/" method="post" name="fm1">
          <input type="hidden" name="logout" value="<?php echo $this->getUser()->getToken() ?>" />
          <a href="#" onclick="document.fm1.submit();">logout</a>
        </form>
        <?php echo 'login : ' . $this->getUser()->getName() ?>
      </div>

      <div id="navbar">
        <ul class="menu">
          <li class="nav">
            <a href="#">title</a>
            <ul>
            <?php $titles = $this->obj->getTitles() ?>
            <?php foreach($titles as $val){ ?>
            <?php echo '<a href="?title='.$val.'"><li>'.$val.'</li></a>';} ?>
            </ul>
          </li>
          <li class="nav">test</li>
          <li class="nav">test</li>
          <li class="nav">test</li>
        </ul>
      </div>

    </div> <!-- header -->
    <!-- ヘッダー領域ここまで}}} -->

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
              <td class="title">
                <?php echo $this->escape($object->getTitle()) ?>
              </td>
            </tr>
            <tr class="body">
              <td colspan="2">
                <p>
                <?php echo $this->renderBody($object->getBody()) ?>
                </p>
              </td>
            </tr>
            <tr>
              <td colspan="2" class="posted_by">
                posted_by : <?php echo $this->object->getPostedBy() ?>
                <?php echo date("Y-m-d H:i",(int)$this->object->getCreatedAt()) ?>
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
