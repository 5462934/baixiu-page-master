<?php
require '../config.php';

    $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if(!$connect){
      die('数据库连接失败');
      return;
    }

    $value = $_GET['callback'];

    $query = mysqli_query($connect, "select * from users where email = '{$value}' limit 1;");

    if(!$query){
      $GLOBALS['error_msg'] = '失败，请在次尝试吧~';
      return;
    }

    $user = mysqli_fetch_assoc($query);

    $avatar = $user['avatar'];
    echo $avatar;