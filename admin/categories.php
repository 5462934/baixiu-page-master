<?php


  require_once '../functions.php';

  xiu_session();

  function check () {
    if(empty($_POST['name']) || empty($_POST['slug'])) {
      $GLOBALS['error_msg'] = '不能有空哦';
      return false;
    }
  }

  // 添加操作函数
  function edit_add () {
    // $check = check();
    // if(!$check){
    //   return;
    // }
    if(empty($_POST['name']) || empty($_POST['slug'])) {
      $GLOBALS['error_msg'] = '不能有空哦';
      return false;
    }
    //暂存用户输入值
    $name = $_POST['name'];
    $slug = $_POST['slug'];

    xiu_edit_result("insert into categories values (null, '{$slug}', '{$name}');");

  }

  function edit_set () {
    // check();
    if(empty($_POST['name']) || empty($_POST['slug'])) {
      $GLOBALS['error_msg'] = '不能有空哦';
      return false;
    }
    //暂存用户输入值
    $id = $_POST['id'];
    $name = $_POST['name'];
    $slug = $_POST['slug'];

    xiu_edit_result("update categories set slug='{$slug}', name='{$name}' where id={$id};");

  }

  if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 查看提交的id是否不为0 如果为0则提交数据为添加，不为0则修改数据
    if(empty($_POST['id'])){
      edit_add();
    }else{
      edit_set();
    }

  }

  $xiu_fetch_assoc = xiu_get_fetch_assoc('select * from categories;');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
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
            <h2>添加新分类目录</h2>
            <input type="hidden" name="id" value="0" id="id">
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://<?php?> 邮箱用户名专用位/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary btn-save" type="submit">添加</button>
              <button class="btn btn-default btn-cancel" type="button" style="display:none;">取消</button>
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
                <th id="bossCheck" class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($xiu_fetch_assoc as $item): ?>
                <tr>
                <td class="text-center"><input type="checkbox" data-id=<?php echo $item['id']; ?>></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['slug']; ?></td>
                <td class="text-center">
                  <button class="btn btn-info btn-xs btn-edit" data-name="<?php echo $item['name'];  ?>" data-slug=<?php echo $item['slug']; ?> data-id=<?php echo $item['id']; ?>>编辑</button>
                  <a href="/admin/category_delete.php?id=<?php echo $item['id']; ?>"class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'categories'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function ($) {

      // 编辑功能
      $('tbody').on('click', '.btn-edit',function(){

        var id = $(this).data('id');
        var name = $(this).data('name');
        var slug = $(this).data('slug');

        $('form h2').text('编辑分类')
        $('form .btn-save').text('保存')
        $('form .btn-cancel').fadeIn()
        $('#id').val(id)
        $('#name').val(name)
        $('#slug').val(slug)
      })
      //取消功能
      $('.btn-cancel').on('click', function(){

        $('form h2').text('添加新目录分类')
        $('form .btn-save').text('添加')
        $('form .btn-cancel').fadeOut()
        $('#id').val(0)
        $('#name').val('')
        $('#slug').val('')
        // return false;
      })

      //批量删除功能
      // var $tbodyCheckboxs = $('tbody input');
      // var $btnDelete = $('#btn_delete');
      // $tbodyCheckboxs.on('change', function () {
      //   var show = false;
      //   $tbodyCheckboxs.each(function (i, item) {
      //     if($(item).prop('checked')) {
      //       show = true;
      //     }
      //   })
      //     show ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
      // })
      var $tbodyCheckboxs = $('tbody input');

      var $btnDelete = $('#btn_delete');

      var checks = []; // 创建一个数组保存选中的check标签id

      $('tbody').on('change', 'input', function () {
      $this = $(this);
      var id = $this.data('id');
      if($this.prop('checked')){
        checks.push(id);
      } else {
        checks.splice(checks.indexOf(id), 1)
      }

      // 判断checks里是否有数据，如果有批量删除按钮出现，否则不出现
      checks.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut();

      $btnDelete.attr('href', '/admin/category_delete.php?id='+checks)
      })

      // 全选按钮
      $('thead input').on('change', function () {
        var checked = $(this).prop('checked')
        // ↓ 记住thead里面的checked是否选中，设置tbody中的checked属性，接着调用tbody的check标签设置过的change属性，显示批量删除按钮
        $tbodyCheckboxs.prop('checked',checked).trigger('change')
      })


    })

  </script>
  <script>NProgress.done()</script>
</body>
</html>
