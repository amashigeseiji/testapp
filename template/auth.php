<?php if(null == $this->user): ?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="template/main.css" />
<title>login</title>
</head>
<body>
  <form action="#" method="post">
    <table>
      <tr>
        <th>name</th>
        <td><input type="text" name="name"></td>
      </tr>

      <tr>
        <th>password</th>
        <td><input type="password" name="password"></td>
      </tr>

      <tr>
        <td>
          <input type="submit" value="送信" />
        </td>
      </tr>

    </table>
  </form>

<!--  <?php //else: ?>
    <div id="logout">
      <?php //echo 'login:'.$this->user->getName() ?>
      <form action="#" method="post" >
        <input type="submit" name="logout" value="logout" />
      </form>
    </div>

    <?php //$action = null; $action = new Action; ?>
-->
<?php endif; ?>
</body>
</html>
