<?php


  require_once '../functions.php';

  xiu_session();

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
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
        <h1>所有评论</h1>
      </div>
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right">
          <ul id="pagination" class="pagination pagination-sm pull-right"></ul>
        </ul>
      </div>
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th width="100px;">作者</th>
            <th>评论</th>
            <th width="150px;">评论在</th>
            <th width="90px;">提交于</th>
            <th width="100px;">状态</th>
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
        <tbody id="comments_list">
          <!-- 动态添加 -->
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'comments'; ?>
  <?php include 'inc/sidebar.php'; ?>
  <script id="comments_tmpl" type="text/x-jsrender">
   {{for comments}}
     <tr class="{{: status === 'held' ? 'warning' : status === 'rejected' ? 'danger' : '' }}" data-id="{{: id }}">
      <td class="text-center"><input type="checkbox"></td>
      <td>{{: author }}</td>
      <td>{{: content }}</td>
      <td>《{{: post_title }}》</td>
      <td>{{: created }}</td>
      <td>{{: status === 'held' ? '待审' : status === 'rejected' ? '拒绝' : '准许' }}</td>
      <td class="text-center">
        {{if status === 'held'}}
          <a href="javascript:;" data-status="approved" class="btn btn-info btn-xs btn-edit">批准</a>
          <a href="javascript:;" data-status="rejected" class="btn btn-warning btn-xs btn-edit">拒绝</a>
        {{/if}}
        <a href="javascript:;" class="btn btn-danger btn-xs btn-delete">删除</a>
      </td>
    </tr>
   {{/for}}
  
  </script>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <script>NProgress.done()</script>

  <script>
    $(function () {
      var $tbody = $('tbody');
      var $th = $('th:first-child');
      var $btnBatch = $('.btn-batch');

       // 选中项集合
      var checkedItems = []


      var currentPage = 1;

      // 发送ajax加载指定页吗对应的的数据
      function loadData (page) {
        $.ajax({
          url: '/admin/api/comments.php',
          type: 'get',
          data: {page: page},
          dataType: 'json',
          success: function (data) {
            // console.log(data)
            var render_comm = $("#comments_tmpl").render({comments: data.comments});
            // 渲染到页面
            $('#comments_list').html(render_comm);

            // 记住当前访问的是第几页
            // var date = new Date(),括号内不写参数是，可以不写
           
            // 记住当前访问的页码
             currentPage = page;
          }
        })
      }
      var startPage = 1;
      var cookies = document.cookie.split(';');
      $(cookies).each(function (i, item) {
        var temp = item.trim().split('=');
        if(temp[0] === 'last_comment_visit_page') {
          startPage = parseInt(temp[1]);
        }
      })
       var defOptions = {
        totalPages: 100,
        startPaeg: startPage,
        first: '首页',
        prev: '上一页',
        next: '下一页',
        last: '尾页',
        visiablePages: 5,
        onPageClick: function (e, page) {
          // 页码变，数据变
          loadData(page)
        }
      }
      // twbsPagination 的作用就是在指定元素上呈现一个分页组件
      $('#pagination').twbsPagination(defOptions);
      

      // ===========================================
      // 批量操作按钮
      $tbody.on('change', 'td > input[type=checkbox]', function () {
        var id = parseInt($(this).parent().parent().data('id'));
        if ($(this).prop('checked')) {
          checkedItems.push(id)
        } else {
          checkedItems.splice(checkedItems.indexOf(id), 1)
        }
        checkedItems.length ? $btnBatch.fadeIn() : $btnBatch.fadeOut()
      })
      // 全选按钮
      $('th > input[type=checkbox]').on('change', function () {
        var checked = $(this).prop('checked');
        $('td > input[type=checkbox]').prop('checked', checked).trigger('change');
      })
    
      // 删除评论
      $tbody.on('click', '.btn-delete', function () {
        var tr = $(this).parent().parent();

        var id = parseInt($(this).parent().parent().data('id'));

        $.get('/admin/comment-delete.php', {
          id: id
        }, function (res) {
          res.success && loadData();
        })
      })
      // 修改评论状态
      $tbody.on('click', '.btn-edit', function () {
        var id = parseInt($(this).parent().parent().data('id'))
        var status = $(this).data('status')
        $.post('/admin/comment-status.php?id=' + id, { status: status }, function (res) {
          res.success && loadData()
        })
      })

      // 批量操作
      $btnBatch
      .on('click', '.btn-info', function () {
        $.post('/admin/comment-status.php?id=' + checkedItems.join(','),{
          status: 'approved'
        }, function (res) {
          res.success && loadData();
        })
      })
      .on('click', '.btn-warning', function () {
          $.post('/admin/comment-status.php?id=' + checkedItems.join(','), { status: 'rejected' }, function (res) {
            res.success && loadData()
          })
      })
      .on('click', '.btn-danger', function () {
          $.post('/admin/comment-delete.php?id=' + checkedItems.join(','), function (res) {
            res.success && loadData()
          })
      })
    })
  </script>
</body>
</html>
