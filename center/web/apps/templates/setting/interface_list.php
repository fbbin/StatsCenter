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
                <div class="jarviswidget jarviswidget-color-darken jarviswidget-sortable" id="wid-id-0"
                     data-widget-editbutton="false" role="widget" style="">
                    <header role="heading">
                        <span class="widget-icon"> <i class="fa fa-table"></i> </span>

                        <h2>接口列表</h2>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>
                    <div role="content">
                        <div id="delete_tip">
                        </div>
                        <div class="jarviswidget-editbox">

                        </div>

                        <div class="widget-body no-padding">
                            <div class="widget-body-toolbar" style="height: 40px;">

                            </div>
                            <div id="dt_basic_wrapper" class="dataTables_wrapper form-inline" role="grid">
                                <div class="dt-top-row">
                                    <div id="data_table_stats_length" style="position: absolute;left: 10px;top: -38px;">
                                        <form id="form" class="smart-form" novalidate="novalidate" method="post">
                                            <div class="form-group" style="width: 200px;">
                                                <div class="form-group">
                                                    <label class="input" style="height: 34px;">
                                                        <input type="text" name="id" id="id"
                                                               value="<?= $this->value($_GET, 'id') ?>"
                                                               placeholder="接口ID">
                                                </div>
                                            </div>
                                            <div class="form-group" style="width: 200px;">
                                                <div class="form-group">
                                                    <label class="input" style="height: 34px;">
                                                        <input type="text" name="name" id="name"
                                                               value="<?= $this->value($_GET, 'name') ?>"
                                                               placeholder="接口名称">
                                                </div>
                                            </div>
                                            <div class="form-group" style="width: 200px;">
                                                <div class="form-group">
                                                    <label class="input" style="height: 34px;">
                                                        <input type="text" name="module_id" id="module_id"
                                                               value="<?= $this->value($_GET, 'module_id') ?>"
                                                               placeholder="模块ID">
                                                </div>
                                            </div>
                                            <div class='form-group' style="padding-left: 100px">
                                                <button class='btn btn-success' style='padding:6px 12px'>提交查询</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <table id="data_table_stats" class="table table-hover table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th style="width: 100px; overflow-x: hidden;">接口ID</th>
                                    <th style="width: 300px; overflow-x: hidden;">接口名称</th>
                                    <th style="width: 300px; overflow-x: hidden;">接口别名</th>
                                    <th>成功率阀值</th>
                                    <th>调用量波动阀值</th>
                                    <th style="width: 200px; overflow-x: hidden;">负责人</th>
                                    <th>添加时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody id="data_table_body">
                                <?php
                                foreach ($data as $d)
                                {
                                    ?>
                                    <tr height="32">
                                        <td class=" "><?= $d['id'] ?></td>
                                        <td class=" "><?= $d['name'] ?></td>
                                        <td class=" "><?= $d['alias'] ?></td>
                                        <td class=" "><?= $d['succ_hold'] ?></td>
                                        <td class=" "><?= $d['wave_hold'] ?></td>
                                        <td class=" "><?= $d['owner_uid_name'] ?></td>
                                        <td class=" "><?= $d['addtime'] ?></td>
                                        <td class=" ">
                                            <a href="/setting/add_interface/?id=<?= $d['id'] ?>"
                                               class="btn btn-info btn-xs">修改</a>
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <a value="<?= $d['id'] ?>" onclick="deleteItem(this)"
                                               href="javascript:void(0)" class="btn btn-danger btn-xs">删除</a>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="pager-box">
                            <?php echo $pager['render']; ?>
                        </div>
                    </div>
                    <!-- end widget content -->

                </div>
                <!-- end widget div -->

        </div>
        <div class="jarviswidget jarviswidget-color-blueDark jarviswidget-sortable" id="wid-id-1"
             data-widget-editbutton="false" role="widget" style="">


        </div>
        <div class="jarviswidget jarviswidget-color-blueDark jarviswidget-sortable" id="wid-id-2"
             data-widget-editbutton="false" role="widget" style="">

        </div>
        <div class="jarviswidget jarviswidget-color-blueDark jarviswidget-sortable" id="wid-id-3"
             data-widget-editbutton="false" role="widget" style="">

        </div>
        </article>
        <!-- WIDGET END -->
    </div>
</div>
<!-- END MAIN CONTENT -->

</div>
<!-- END MAIN PANEL -->

<!-- SHORTCUT AREA : With large tiles (activated via clicking user name tag)
Note: These tiles are completely responsive,
you can add as many as you like
-->
<div id="shortcut">
    <ul>
        <li>
            <a href="#inbox.html" class="jarvismetro-tile big-cubes bg-color-blue"> <span class="iconbox"> <i
                        class="fa fa-envelope fa-4x"></i> <span>Mail <span
                            class="label pull-right bg-color-darken">14</span></span> </span> </a>
        </li>
        <li>
            <a href="#calendar.html" class="jarvismetro-tile big-cubes bg-color-orangeDark"> <span class="iconbox"> <i
                        class="fa fa-calendar fa-4x"></i> <span>Calendar</span> </span> </a>
        </li>
        <li>
            <a href="#gmap-xml.html" class="jarvismetro-tile big-cubes bg-color-purple"> <span class="iconbox"> <i
                        class="fa fa-map-marker fa-4x"></i> <span>Maps</span> </span> </a>
        </li>
        <li>
            <a href="#invoice.html" class="jarvismetro-tile big-cubes bg-color-blueDark"> <span class="iconbox"> <i
                        class="fa fa-book fa-4x"></i> <span>Invoice <span
                            class="label pull-right bg-color-darken">99</span></span> </span> </a>
        </li>
        <li>
            <a href="#gallery.html" class="jarvismetro-tile big-cubes bg-color-greenLight"> <span class="iconbox"> <i
                        class="fa fa-picture-o fa-4x"></i> <span>Gallery </span> </span> </a>
        </li>
        <li>
            <a href="javascript:void(0);" class="jarvismetro-tile big-cubes selected bg-color-pinkDark"> <span
                    class="iconbox"> <i class="fa fa-user fa-4x"></i> <span>My Profile </span> </span> </a>
        </li>
    </ul>
</div>
<?php include dirname(__DIR__) . '/include/javascript.php'; ?>
<script src="<?=WEBROOT?>/apps/static/js/stats.js" type="text/javascript"></script>
<script src="<?=WEBROOT?>/apps/static/js/list.js" type="text/javascript"></script>
<script>
    $(function () {
        pageSetUp();
    });
</script>

</body>
</html>
