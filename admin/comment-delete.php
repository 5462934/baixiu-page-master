<?php
  /**
   * 删除评论
   * id 在参数里
   */

   require '../functions.php';

   // 设置响应类型为JSON
   header('Content-Type: application/json');

   if (empty($_GET['id'])) {
    // 缺少必要参数
    exit(json_encode(array(
    'success' => false,
    'message' => '缺少必要参数'
    )));
   };

   $affected_rows = xiu_edit_result(sprintf('delete from comments where id in (%s)', $_GET['id']));

   echo json_encode(array(
     'success' => $affected_rows > 0
   ));