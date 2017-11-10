<?php
  require '../functions.php';

  if(empty($_GET['id'])){
    die('失败了');
  }

  $id = $_GET['id'];

  // 调用函数，删除元素
  xiu_edit_result('delete from categories where id in('. $id .')');

  // 响应表单
  header('Location: /admin/categories.php');