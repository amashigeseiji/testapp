<!DOCTYPE>
<html>
<head>
</head>
<body>

<?php if(null == $this->user): ?>
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

  <?php else: ?>
  <?php echo 'login:'.$this->user->getName() ?>
    <form action="#" method="post" >
      <input type="submit" name="logout" value="logout" />
    </form>
    <a href="<?php $_SERVER['PHP_SELF'] ?>">test</a>

<?php endif; ?>
</body>
</html>
