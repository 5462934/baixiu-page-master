<?php


  require_once '../functions.php';

  xiu_session();

  function post_add () {

    if(empty($_POST['email']) || empty($_POST['slug']) || empty($_POST['nickname']) || empty($_POST['password'])){
      $GLOBALS['error_msg'] = '请完整填写个人信息，以便添加';
      return;
    }
    var_dump($_FILES['avatar']);
    // 验证图片上传信息

    if($_FILES['avatar']['error'] !== UPLOAD_ERR_OK){
        $GLOBALS['error_msg'] = '图片上传失败';
        return;
    }

    // 允许图片上传类型
    $allow_avatar_type = array('image/jpeg', 'image/png', 'image/gif');
    if(!in_array($_FILES['avatar']['type'], $allow_avatar_type)){
      $GLOBALS['error_msg'] = '上传图片类型错误';
      return;
    }
    $avatar_name = $_FILES['avatar']['tmp_name'];
    $upload_avatar = './../static/uploads/'.$_FILES['avatar']['name'];
    $avatar = substr($upload_avatar, 1);
    $move_avatar_file = move_uploaded_file($avatar_name, $avatar);

    if(!$move_avatar_file){
      $GLOBALS['error_msg'] = '移动头像文件失败';
      return;
    }

    // 保存临时信息
    $email = $_POST['email'];
    $slug = $_POST['slug'];
    $nickname = $_POST['nickname'];
    $password = $_POST['password'];

    // 匹配邮箱是否符合格式，哎不用配了
    // $email_style = /^\w+@\w+\.\w+(\.\w+)?$/;

    // 添加数据到users数据库
     xiu_edit_result("insert into users  (slug, email, password, nickname, avatar status) values ('{$slug}', '{$email}', '{$password}', '{$nickname}', '{$avatar}' , 'activated');");
  }

  function post_edit () {

    if(empty($_POST['email']) || empty($_POST['slug']) || empty($_POST['nickname']) ||empty($_POST['password'])){
      $GLOBALS['error_msg'] = '请完整填写个人信息，以便添加';
      return;
    }

    // 保存临时信息
    $id = $_POST['id'];
    $email = $_POST['email'];
    $slug = $_POST['slug'];
    $nickname = $_POST['nickname'];
    $password = $_POST['password'];
    // 修改数据到users数据库
    xiu_edit_result("update users set slug = '{$slug}', email = '{$email}', password = '{$password}', nickname = '{$nickname}' where id={$id};");

  }


  if($_SERVER['REQUEST_METHOD'] === 'POST') {

    if(empty($_POST['id'])){
      post_add();
    } else {
      post_edit();
    }
  }

  $result = xiu_get_fetch_assoc('select * from users;');

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if(isset($error_msg)): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $error_msg; ?>
        </div>
      <?php endif; ?>
      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <h2>添加新用户</h2>
            <input type="hidden" name="id" id="ids" value="0">
            <div class="form-group">
              <label for="avatar">头像</label>
              <input type="file" name="avatar" id="avatar" class="form-control" accept="image/*">
              <img src="" alt="" id="avatar_show" style="width: 150px; height: 150px; background: skyblue;">
            </div>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" id="btn-delete" type="submit">添加</button>
              <button class="btn btn-default" id="btn_cancel" type="button" style="display: none">取消</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="#" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($result as $item) {?>
              <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
                <td class="text-center"><img class="avatar" src="<?php echo $item['avatar']; ?>"></td>
                <td><?php echo $item['email']; ?></td>
                <td><?php echo $item['slug']; ?></td>
                <td><?php echo $item['nickname']; ?></td>
                <td><?php echo $item['status'] === 'activated' ? '激活' : $item['status'] ; ?></td>
                <td class="text-center">
                  <button class="btn btn-info btn-xs btn-edit" data-id=<?php echo $item['id']; ?> data-email=<?php echo $item['email'];?> data-slug=<?php echo $item['slug']; ?> data-nickname=<?php echo $item['nickname']; ?> data-pass=<?php echo $item['password']; ?>>编辑</button>
                  <a href="/admin/users_delete.php?id=<?php echo $item['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'users'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function ($) {
      //用户头像预览
      $('#avatar').on('change', function () {
        if(!this.files.length) return;

        var file = this.files[0];

        if(!file.type.startsWith('image/')) return;

        var url = URL.createObjectURL(file);

        $('#avatar_show').attr('src', url).fadeIn().on('load', function () {
          URL.revokeObjectURL(url);
        })
      })




      $('tbody .btn-edit').on('click',function () {
      var id = $(this).data('id')
      var email = $(this).data('email');
      var slug = $(this).data('slug');
      var nickname = $(this).data('nickname');
      var password = $(this).data('pass');
      // 设置相应改变
      $('#ids').val(id)
      $('form h2').text('修改用户')
      $('#email').val(email)
      $('#slug').val(slug)
      $('#nickname').val(nickname)
      $('#password').val(password)
      $('#btn-delete').text('修改')
      $('#btn_cancel').fadeIn()
    })

      $('#btn_cancel').click(function () {
        $('#ids').val(0)
        $('form h2').text('添加用户')
        $('#email').val('')
        $('#slug').val('')
        $('#nickname').val('')
        $('#password').val('')
        $('#btn-delete').text('添加')
        $('#btn_cancel').fadeOut()
      })

      var check = []
      var $btn_delete = $('#btn_delete')
      var $btnCheckedBox = $('tbody input')
    $('tbody').on('change', 'input', function () {
      $this = $(this)
      var id = $this.data('id');
      if($this.prop('checked')){
        check.push(id)
      } else {
        check.splice(check.indexOf(id), 1)
      }

      check.length ? $btn_delete.fadeIn() : $btn_delete.fadeOut()
      $btn_delete.attr('href', '/admin/users_delete.php?id='+check)
    })
    $('thead input').on('change', function () {
      // 记住全选框状态
      $checked = $(this).prop('checked')
      $btnCheckedBox.prop('checked', $checked).trigger('change')
    })

    })


  </script>
  <script>NProgress.done()</script>
</body>
</html>
