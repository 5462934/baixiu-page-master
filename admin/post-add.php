<?php


  require_once '../functions.php';

  xiu_session();
  
  $category = xiu_get_fetch_assoc('select * from categories;');

function receive_form () {
  // 校验表单是否完整
  if(empty($_POST['title'])
    || empty($_POST['content'])
    || empty($_POST['slug'])
    || empty($_POST['category'])
    || empty($_POST['created'])
    || empty($_POST['status'])) {
    $GLOBALS['error_msg'] = '请完整填写信息';
    return;
  }

  // 特色图像校验
  if (!(isset($_FILES['feature']) && $_FILES['feature']['error'] === UPLOAD_ERR_OK)) {
    $GLOBALS['error_msg'] = '上传图片失败';
    return;
  }
  if(empty($_FILES['feature']['error'])) {
     $temp_file = $_FILES['feature']['tmp_name'];
     $target_file = 'C:/Users/lenovo/Documents/GitHub/baixiu-page-master/static/uploads' . $_FILES['feature']['name'];
  
    if(move_uploaded_file($temp_file, $target_file)) {
      $image_file = 'C:/Users/lenovo/Documents/GitHub/baixiu-page-master/static/uploads' . $_FILES['feature']['name'];
    }
  }
// 获取临时数据
  $feature = isset($image_file) ? $image_file : '';
  $title = $_POST['title'];
  $created = $_POST['created'];
  $content = $_POST['content'];
  $slug = $_POST['slug'];
  $category_id = $_POST['category'];
  $status = $_POST['status'];

// 添加到posts数据库中
 $affected_rows = xiu_edit_result("insert into posts values (null, '{$slug}', '{$title}', '{$feature}', '{$created}', '{$content}', 11, 11, '{$status}', 1,'{$category_id}')");

  if ($affected_rows === 1) {
    $GLOBALS['success'] = '添加成功';
  }

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
   <link rel="stylesheet" href="/static/assets/vendors/simplemde/simplemde.min.css">
   <script src="/static/assets/vendors/simplemde/simplemde.min.js"></script>
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
        <strong>错误！</strong> <?php echo $error_msg; ?>
      </div>
       <?php } ?>
      <form class="row" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" placeholder="文章标题">
          </div>
          <div class="form-group">
            <label for="content">内容</label>
            <div class="form-group">
              <textarea id="fieldTest" name="content" cols="30" rows="10"></textarea>
            </div>
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
            <input id="feature" class="form-control" name="feature" type="file">
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
  <script src="/static/assets/vendors/moment/moment.js"></script>
  <script type="text/javascript">

  $(function ($) {
    var simplemde = new SimpleMDE({
        element: document.getElementById("fieldTest"),
        autoDownloadFontAwesome: false,
        status: false
    });

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
