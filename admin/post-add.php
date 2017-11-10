<?php


  require_once '../functions.php';

  xiu_session();

$category = xiu_get_fetch_assoc('select * from categories;');

function receive_form () {
  if(empty($_POST['title'])
    || empty($_POST['content'])
    || empty($_POST['slug'])
    || empty($_POST['category'])
    || empty($_POST['created'])
    || empty($_POST['status'])) {
    $GLOBALS['error_msg'] = '请完整填写信息';
    return;
  }
// 判断上传图片=========================
// 上传图片类型
  // $allow_img = array('image/png', 'image/jpeg', 'image/gif');
  // if(!array_search($_FILES['feature']['type'],$allow_img)){
  //   $GLOBALS['error_msg'] = '上传图片类型错误';
  //   return;
  // }
// 设置上传图片路径
// 图片名称
  $feature_name = $_FILES['feature']['tmp_name'];
  $img_lujing = './../static/uploads/' . $_FILES['feature']['name'];
  $feature = substr($img_lujing, 4);
  $move_uploaded_file = move_uploaded_file($feature_name, $img_lujing);
  if(!$move_uploaded_file) {
    $GLOBALS['error_msg'] = '上传图片失败';
    return;
  }
// 获取临时数据
  $title = $_POST['title'];
  $content = $_POST['content'];
  $slug = $_POST['slug'];
  $category_id = $_POST['category'];
  $created = $_POST['created'];
  $status = $_POST['status'];

// 添加到posts数据库中
  xiu_edit_result("insert into posts values (null, '{$slug}', '{$title}', '{$feature}', '{$created}', '{$content}', 11, 11, '{$status}', 1,{ $category_id})");
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
  receive_form();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
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
        <h1>写文章</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if(isset($error_msg)) { ?>
      <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误 <?php echo $error_msg; ?>
      </div>
       <?php } ?>
      <form class="row" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" placeholder="文章标题">
          </div>
          <div class="form-group">
            <label for="content">内容</label>
            <script id="content" name="content" type="text/javascript">
            </script>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
            <p class="help-block">https://zce.me/post/<strong>slug</strong></p>
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img id="preview" class="help-block thumbnail" style="display: none">
            <input id="feature" class="form-control" name="feature" type="file" accept=image/*>
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
            <?php foreach ($category as $item) { ?>
              <option value="<?php echo $item['id'];?>"><?php echo $item['name']; ?></option>
            <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
             <option value="published">发布</option>
             <option value="drafted">草稿</option>
             <option value="trashed">回收站</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php $current_page = 'post-add'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/ueditor/ueditor.config.js"></script>
  <script src="/static/assets/vendors/ueditor/ueditor.all.js"></script>
  <script src="/static/assets/vendors/moment/moment.js"></script>
  <script type="text/javascript">

  $(function ($) {
    UE.getEditor('content');

    $('#feature').on('change', function () {
      if(!this.files.length) return;
      var file = this.files[0];
      if(!file.type.startsWith('image/')) return;

      var url = URL.createObjectURL(file);
      $('#preview').attr('src', url)
      .fadeIn().on('load', function () {
        URL.revokeObjectURL(url);
      })
    })

    var time = moment().format('YYYY-MM-DDTHH:mm')
    $('#created').val(time);
  })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
