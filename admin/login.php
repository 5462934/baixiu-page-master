<?php
 // 引入文件
 require '../functions.php';
 function login(){
    if(empty($_POST['email'])){
      $GLOBALS['error_msg'] = '输入邮箱哟~';
      return;
    }
    if(empty($_POST['password'])){
      $GLOBALS['error_msg'] = '输入密码哟~';
      return;
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    // 查询数据库中是否有此邮箱
    $user = xiu_fetch_one("select * from users where email = '{$email}' limit 1;");

    if(!$user){
      $GLOBALS['error_msg'] = '登陆失败，请在次尝试吧~';
      return;
    }

    if($user['password'] !== $password ) {
      $GLOBALS['error_msg'] = '用户名或密码错误~';
      return;
    }

    session_start();

    $_SESSION['current_logged_user'] = $user;
   
    header('Location: /admin/index.php');

}

 if($_SERVER['REQUEST_METHOD'] === 'POST'){
  login();
 }

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
</head>
<body>
  <div class="login">
    <form class="login-wrap" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
      <img class="avatar" src="/static/assets/img/default.png" id="img">
      <!-- 有错误信息时展示 -->
      <?php if(isset($error_msg)): ?>
        <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $error_msg; ?>
      </div>
    <?php endif; ?>
      <div class="form-group">
        <label for="email" class="sr-only" >邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block" type="submit" href="index.html">登 录</button>
    </form>
  </div>

  <script type="text/javascript" src="/static/assets/vendors/jquery/jquery.js"></script>
  <script>

  var email = document.getElementById("email");
  email.onblur = function(){
    var value = this.value;
    var xhr = new XMLHttpRequest();

    xhr.addEventListener("readystatechange",function(){
      if(this.readyState !== 4) return
        var img = document.getElementById("img");
        img.src = this.responseText;
    })
    xhr.open('GET','./check.php?callback=' + value);
    xhr.send(null);
  }
  </script>

</body>
</html>
