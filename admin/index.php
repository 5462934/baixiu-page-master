<?php


  require_once './../functions.php';


  xiu_session();
  // 文章已发布
  $count_published = xiu_fetch_one("select count(1) from posts where status = 'published';");
  // 文章草稿
  $count_drafted = xiu_fetch_one("select count(1) from posts where status = 'drafted';");
  // 文章分类总数
  $count_category = xiu_fetch_one("select count(1) from categories;");
  // 评论已审核
  $count_approved = xiu_fetch_one("select count(1) from comments;");
  // 评论未审核
  $count_held = xiu_fetch_one("select count(1) from comments where status = 'held';");



?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <script src="/static/assets/vendors/jquery/echarts.simple.min.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="jumbotron text-center">
        <h1>One Belt, One Road</h1>
        <p>Thoughts, stories and ideas.</p>
        <p><a class="btn btn-primary btn-lg" href="post-add.php" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo $count_published['count(1)']; ?></strong>篇文章（<strong><?php echo $count_drafted['count(1)']; ?></strong>篇草稿）</li>
              <li class="list-group-item"><strong><?php echo $count_category['count(1)']; ?></strong>个分类</li>
              <li class="list-group-item"><strong><?php echo $count_approved['count(1)']; ?></strong>条评论（<strong><?php echo $count_held['count(1)']; ?></strong>条待审核）</li>
            </ul>

          </div>
        </div>
         <div id="main" style="width: 300px;height:200px;"></div>
        <div class="col-md-4"></div>
        <div class="col-md-4"></div>
      </div>
    </div>
  </div>

  <?php $current_page = 'index'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
   <script type="text/javascript">

    // 基于准备好的dom，初始化echarts实例
    var myChart = echarts.init(document.getElementById('main'));
    // 指定图表的配置项和数据
    var option = {
      title : {
          text: '某站点用户访问来源',
          subtext: '纯属虚构',
          x:'center'
      },
      tooltip : {
          trigger: 'item',
          formatter: "{a} <br/>{b} : {c} ({d}%)"
      },
      legend : {
          orient: 'vertical',
          left: 'left',
          data: ['文章','草稿','分类','评论','评论待审核']
      },
      series : [
          {
            name: '访问来源',
            type: 'pie',
            radius : '55%',
            center: ['50%', '60%'],
            data:[
                {value:1, name:'文章'},
                {value:3, name:'草稿'},
                {value:5, name:'分类'},
                {value:7, name:'总评论'},
                {value:4, name:'评论待审核'}
            ],
      itemStyle : {
            emphasis: {
            shadowBlur: 10,
            shadowOffsetX: 0,
            shadowColor: 'rgba(0, 0, 0, 0.5)'
            }
      }
        }
    ]
};

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
    </script>
  <script>NProgress.done()</script>
</body>
</html>
