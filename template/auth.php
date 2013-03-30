<?php if(null == $this->user): ?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="template/main.css" />
<title>login</title>
</head>
<body>
<div id="auth">
  <form action="#" method="post">
    <table class="auth">
      <tr>
        <td class="title">name</td>
        <td><input type="text" name="name"></td>
      </tr>

      <tr>
        <td class="title">password</td>
        <td><input type="password" name="password"></td>
      </tr>

      <tr>
        <td class="title" colspan="2">
          <input type="submit" value="送信" />
        </td>
      </tr>

    </table>
  </form>
</div>
</body>
</html>
<?php endif; ?>
