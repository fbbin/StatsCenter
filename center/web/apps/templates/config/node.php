<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title><?= Swoole::$php->config['common']['site_name'] ?></title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?php include __DIR__ . '/../include/css.php'; ?>
</head>
<body class="">
<header style="background: #E4E4E4;color: #22201F" id="header">
    <?php include __DIR__ . '/../include/top_menu.php'; ?>
</header>
<aside id="left-panel">
    <!--            --><?php //include __DIR__.'/../include/login_info.php'; ?>
    <?php include __DIR__ . '/../include/leftmenu.php'; ?>
    <span class="minifyme"> <i class="fa fa-arrow-circle-left hit"></i> </span>
</aside>
<!-- END NAVIGATION -->

<!-- MAIN PANEL -->
<div id="main" role="main">

    <!-- RIBBON -->
    <div id="ribbon">

    <span class="ribbon-button-alignment">
        <span id="refresh" class="btn btn-ribbon" data-title="refresh" rel="tooltip"
              data-placement="bottom"
              data-original-title="<i class='text-warning fa fa-warning'></i> Warning! This will reset all your widget settings."
              data-html="true"><i class="fa fa-refresh"></i></span> </span>

        <!-- breadcrumb -->
        <ol class="breadcrumb">
            <li>Home</li>
            <li>Dashboard</li>
        </ol>

    </div>
    <div id="content">
        <!-- widget grid -->
        <section id="widget-grid" class="">
            <!-- row -->
            <div class="row">
                <!-- NEW WIDGET START -->
                <article class="col-sm-12 col-md-12 col-lg-6 sortable-grid ui-sortable">
                    <?php include dirname(__DIR__) . '/include/msg.php'; ?>
                    <div class="jarviswidget jarviswidget-color-black" id="wid-id-2" data-widget-editbutton="false" role="widget">
                        <header role="heading">
                            <span class="widget-icon"> <i class="fa fa-lg fa-sitemap"></i> </span>
                            <h2>管理集群</h2>
                            <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                        </header>
                        <div role="content" class="no-padding ">
                        <!-- widget div-->
                            <!-- widget content -->
                            <div class="alert alert-info no-margin fade in">
                                <form class="form-inline" method="post" role="form">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-10">
                                            <div class="form-group">
                                                <input name="ip" type="text" class="form-control input-sm" placeholder="服务器IP">
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-2 text-align-right">
                                            <button type="submit" class="btn btn-info">
                                                <i class="fa fa-plus"></i> 添加到集群
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>节点</th>
                                    <th>添加者</th>
                                    <th>添加时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($list as $s)
                                { ?>
                                    <tr>
                                        <td><?= $s['id'] ?></td>
                                        <td><?= $s['ip'] ?></td>
                                        <td><?= $users[$s['add_uid']] ?></td>
                                        <td><?= $s['add_time'] ?></td>
                                        <td><a href="?del=<?=$s['id'] ?>" onclick="return confirm('确定要删除?');" class="btn btn-xs btn-danger deleteNode">删除</a></span></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </article>
                <!-- WIDGET END -->
            </div>
            <!-- end row -->
        </section>
        <!-- end widget grid -->
    </div>
</div>
<!-- END MAIN PANEL -->

<?php include dirname(__DIR__) . '/include/javascript.php'; ?>
<script src="<?=WEBROOT?>/apps/static/js/stats.js" type="text/javascript"></script>
<script src="<?=WEBROOT?>/apps/static/js/list.js" type="text/javascript"></script>
<script>
    $(function () {
        pageSetUp();
        $("#submit").click(function () {
            $("#form").submit();
        });
    });
</script>

</body>
</html>
