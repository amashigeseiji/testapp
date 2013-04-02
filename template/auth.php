<!-- vim: set foldmethod=marker: -->
<?php $entry = 0 ?>
<?php if(array_key_exists('entry',$_GET)): ?>
<?php $entry = 1 ?>
<?php endif; ?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="template/main.css" />
<title>login</title>
</head>
<body>

<!-- ログイン画面{{{ -->
<?php if($entry == 0): ?>
<div id="auth">
  <form action="#" method="post">
    <table class="auth">

      <?php if(array_key_exists('auth',$this->message)||array_key_exists('register',$this->message)): ?>
        <td class="error" colspan="2">
        <?php echo $this->message['auth'] ?>
        <?php //echo $this->message['register'] ?>
        </td>
      <?php endif; ?>

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

  <div id="entry">
  <form action="/" method="get" name="fm2">
    <input type="hidden" name="entry" value="#">
    <a href="#" onclick="document.fm2.submit()">新規ユーザー登録</a>
  </form>
  </div>

</div>
<!-- ログイン画面ここまで}}} -->

<!-- 新規登録画面{{{ -->
<?php elseif($entry == 1): ?>
<div id="newentry">
  <form action="#" method="post">

    <table class="auth">

      <tr>
        <td colspan="2">
          新規ユーザー登録<br />
      <?php if(array_key_exists('register',$this->message)): ?>
         <span class="error"><?php echo $this->message['register'] ?></span>
      <?php endif; ?>
        </td>
      </tr>

      <tr>
        <td class="title">アカウント名<br />(半角英数字)</td>
        <td><input type="text" name="newname" size="25" placeholder="登録したいアカウント名を入力"></td>
      </tr>

      <tr>
        <td class="title">パスワード<br />(半角英数字)</td>
        <td><input type="password" name="newpassword" size="25" placeholder="パスワードを入力"></td>
      </tr>

      <tr>
        <td class="title" colspan="2">
          <input type="submit" value="登録する" />
        </td>
      </tr>

    </table>

  </form>
<span class="back"><a href="/">前に戻る</a></span>

</div>
<?php endif; ?>
<!-- 新規登録画面ここまで}}} -->

</body>
</html>
