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
        <!-- row -->
        <div class="row">
            <!-- NEW WIDGET START -->
            <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                    <div role="content">
                        <div class="widget-body">
                            <ul class="nav nav-tabs bordered">
                                <?php foreach($categorys as $v):?>
                                    <li class="<?php if ($_GET['category'] == $v['id']) echo "active" ?>">
                                        <a href="?category=<?= $v['id'] ?>"><?= $v['name'] ?></a>
                                    </li>
                                <?php endforeach; ?>
                                <li class="dropdown pull-right">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">设置 <b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="/config/category/"><i class="fa fa-table"></i> 增加分类</a>
                                        </li>
                                        <li>
                                            <a href="/config/entity/?category=<?=$_GET['category']?>"><i class="fa fa-plus"></i> 增加配置</a>
                                        </li>
                                        <li>
                                            <a href="/config/node/"><i class="fa fa-sitemap"></i> 集群管理</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade active in" id="tab-system">
                                    <div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false">
                                        <header>
                                            <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                                            <h2>配置列表</h2>
                                        </header>
                                        <div>
                                            <div class="widget-body no-padding">
                                                <table class="table table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <th>配置名称</th>
                                                        <th>最近更新者</th>
                                                        <th>最近更新时间</th>
                                                        <th>创建者</th>
                                                        <th>创建时间</th>
                                                        <th>操作</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
    <?php foreach($list as $li):?>
    <tr>
        <td><?=$li['name']?></td>
        <td><?=empty($li['update_uid'])?'-':$users[$li['update_uid']]?></td>
        <td><?=empty($li['update_uid'])?'-':date('Y-m-d H:i:s', $li['update_time'])?></td>
        <td><?=$users[$li['owner_uid']]?></td>
        <td><?=date('Y-m-d H:i:s', $li['create_time'])?></td>
        <td>
            <a href="<?=Swoole\Tool::urlAppend($url_base, ['id'=>$li['id'], 'op' => 'modify']) ?>" class="btn btn-primary btn-xs">修改配置</a>
            <a href="<?=Swoole\Tool::urlAppend($url_base, ['id'=>$li['id'], 'op' => 'push']) ?>" class="btn btn-warning btn-xs">下发配置</a>
            <a onclick="return confirm('确定要删除?');" href="<?=Swoole\Tool::urlAppend($url_base, ['id'=>$li['id'], 'op' => 'delete']) ?>" class="btn btn-danger btn-xs">删除配置</a>
        </td>
    </tr>
    <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </article>
        </div>
    </div>
<!-- END MAIN CONTENT -->

</div>
<!-- END MAIN PANEL -->
<?php include dirname(__DIR__) . '/include/javascript.php'; ?>
<script src="<?=WEBROOT?>/apps/static/js/stats.js" type="text/javascript"></script>
<script src="<?=WEBROOT?>/apps/static/js/list.js" type="text/javascript"></script>
<script>
    $(function () {
        pageSetUp();

        var dialog = $("#create_form").dialog({
            autoOpen : false,
            width : 600,
            resizable : false,
            modal : true,
            buttons : [{
                html : "<i class='fa fa-times'></i>&nbsp; 取消",
                "class" : "btn btn-default",
                click : function() {
                    $(this).dialog("close");
                }
            }, {

                html : "<i class='fa fa-plus'></i>&nbsp; 确认",
                "class" : "btn btn-danger",
                click : function() {
                    location.href ="/config/entity/?new=1&category=<?=$category?>&key=" + encodeURI($('#config_id').val());
                    $(this).dialog("close");
                }
            }]
        });

        $("#create_button").button().click(function() {
            dialog.dialog("open");
        });
    });
</script>

</body>
</html>
