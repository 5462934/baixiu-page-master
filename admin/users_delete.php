<?php

  require '../functions.php';

  if(empty($_GET['id'])){
    exit();
  }

  $id = $_GET['id'];

  xiu_edit_result('delete from users where id in('. $id .');');

  header('Location: /admin/users.php');