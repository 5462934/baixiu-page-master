<?php

require_once 'config.php';
session_start();

function xiu_session () {

  if(empty($_SESSION['current_logged_user'])) {
    header('Location: /admin/login.php');
    return;
  }
  return $_SESSION['current_logged_user'];
}

/**
 * 数据库连接函数
 */

function mysqli_conn () {

    $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if(!$connect){
      die('<h1>连接错误 (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() . '</h1>');
    }
    return $connect;
}

/**
 * 查询结果函数
 * @param  [type] $sql [description]
 * @return [type]      [description]
 */
function xiu_get_fetch_assoc ($sql) {

    $conn = mysqli_conn();
    // 设置编码格式
    mysqli_query($conn,"set names 'utf8' ");
    $query = mysqli_query($conn, $sql);

    if(!$query) {
      return false;
    }

    while($rows = mysqli_fetch_assoc($query)){
        $result[] = $rows;
    }

    // 释放结果集
    mysqli_free_result($query);

    // 释放连接
    mysqli_close($conn);

    // 放回结果
    return $result;
}


/**
 * 查询结果单条函数
 */

function xiu_fetch_one ($sql) {
  return xiu_get_fetch_assoc($sql)[0];
}


/**
 * 执行增删改函数
 */

function xiu_edit_result ($sql) {
  $conn = mysqli_conn();
  // 设置编码格式
    mysqli_query($conn,"set names 'utf8' ");
  $query = mysqli_query($conn, $sql);

  if(!$query){
    return false;
  }

  $affected_rows = mysqli_affected_rows($conn);

  mysqli_close($conn);

  return $affected_rows;
}

/**
 * 输出分页链接
 * @param  integer $page    当前页码
 * @param  integer $total   总页数
 * @param  string  $format  链接模板，%d 会被替换为具体页数
 * @param  integer $visible 可见页码数量（可选参数，默认为 5）
 * @example
 *   <?php xiu_pagination(2, 10, '/list.php?page=%d', 5); ?>
 */

function xiu_pagination ($page, $total, $format, $visible = 5) {


$left = floor($visible / 2);

// 开始页码
$begin = $page - $left;

// 确保开始页码不小于1
$begin = $begin < 1 ? 1 : $begin;

// 结束页码
$end = $begin + $visible - 1;

// 结束页码不能大于最大值
$end = $end > $total ? $total : $end;

// 结束页码变化，开始页码也变化
$begin = $end - $visible + 1;

// 确保$begin不小于1
$begin = $begin < 1 ? 1 : $begin;

// 上一页
if ($page - 1 > 0) {
  printf('<li><a href="%s">&laquo;</a></li>', sprintf($format, $page - 1));
}

// 省略号
if ($begin > 1) {
  printf('<li class="disabled"><span>...</span></li>');
}

// 显示页码
for ($i = $begin; $i <= $end; $i++) {
  $activeClass = $i == $page ? ' class="active"' : '';
  printf('<li%s><a href="%s">%d</a></li>', $activeClass, sprintf($format, $i), $i);
}

// 省略号
if ($end < $total - floor($visible / 2)) {
  printf('<li class="disabled"><span>...</span></li>');
}
// 下一页
if ($page + 1 <= $total) {
  printf('<li><a href="%s">&raquo;</a></li>', sprintf($format, $page + 1));
}
}