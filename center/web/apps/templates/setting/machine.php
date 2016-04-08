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
<div id="main" role="main">

    <!-- RIBBON -->
    <div id="ribbon">
        <!--				<span class="ribbon-button-alignment"> <span id="refresh" class="btn btn-ribbon" data-title="refresh"  rel="tooltip" data-placement="bottom" data-original-title="<i class='text-warning fa fa-warning'></i> Warning! This will reset all your widget settings." data-html="true"><i class="fa fa-refresh"></i></span> </span>-->

        <!-- breadcrumb -->
        <ol class="breadcrumb">
            <li>Home</li>
            <li>Dashboard</li>
        </ol>
    </div>
    <!-- END RIBBON -->

    <!-- MAIN CONTENT -->
    <div id="content">

        <div class="row">
            <article class="col-sm-12 sortable-grid ui-sortable">
                <!-- new widget -->

                <!-- end widget -->

                <div class="jarviswidget jarviswidget-sortable" id="wid-id-0" data-widget-togglebutton="false"
                     data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-colorbutton="false"
                     data-widget-deletebutton="false" role="widget" style="">
                    <!-- widget options:
                    usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

                    data-widget-colorbutton="false"
                    data-widget-editbutton="false"
                    data-widget-togglebutton="false"
                    data-widget-deletebutton="false"
                    data-widget-fullscreenbutton="false"
                    data-widget-custombutton="false"
                    data-widget-collapsed="true"
                    data-widget-sortable="false"

                    -->
                    <header role="heading">
                        <span class="widget-icon"> <i class="glyphicon glyphicon-stats txt-color-darken"></i> </span>
                        <h2>介绍 </h2>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>
                    <!-- widget div-->
                    <div role="content">
                        <div class="widget-body-toolbar bg-color-white">
                            <form class="form-inline" role="form" action="/setting/machine/" method="post">
                                <div class="row">
                                    <div class="col-sm-12 col-md-10">
                                        <div class="form-group">
                                            <input type="text" name="ip" class="form-control input-sm" placeholder="IP">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="intro" class="form-control input-sm" placeholder="备注">
                                        </div>
                                        <div class="form-group">
                                            <label class="sr-only">分类</label>
                                            <select class="form-control input-sm" name="layer">
                                                <option value="-1">分层</option>
                                                <?php foreach ($this->config['common']['node_categorys'] as $k => $v): ?>
                                                    <option value="<?= $k ?>"><?= $v ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-plus"></i> 添加
                                        </button>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class="widget-body">
                            <!-- content -->
                            <div id="myTabContent" class="tab-content">
                                <div class="tree ">
                                    <ul>
                                        <li>
                                            <span id="span_project"> <?= $_project_info['name'] ?></span>
                                            <ul>
                                                <?php
                                                $table = table('machine', 'platform');
                                                $colors = array('info', 'success', 'default', 'warning', 'danger');
                                                $n_machine = 0;
                                                foreach ($this->config['common']['node_categorys'] as $k => $v): ?>
                                                <li>
                                                    <?php
                                                    $list = $table->gets(array('project_id' => $_project_info['id'], 'layer' => $k));
                                                    $n_machine += count($list);
                                                    ?>
                                                    <span class="label label-<?=$colors[$k]?>"><?=$v?>（<?=count($list)?>台机器）</span>
                                                    <ul>
                                                        <li>
                                                            <?php foreach ($list as $v2): ?>
                                                                <span>
                                                                    <a
                                                                        href="http://op.oa.com/mod/status?lan_ip=192.168.1.118"
                                                                        target="_blank"><?= $v2['ip'] ?></a>
                                                                <?php if (!empty($v2['intro'])): ?><code><?= $v2['intro'] ?></code><?php endif; ?>
                                                                    </span>
                                                            <?php endforeach; ?>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    <!-- end widget div -->
                </div>
            </article>
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
                    <a href="#calendar.html" class="jarvismetro-tile big-cubes bg-color-orangeDark"> <span
                            class="iconbox"> <i class="fa fa-calendar fa-4x"></i> <span>Calendar</span> </span> </a>
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
                    <a href="#gallery.html" class="jarvismetro-tile big-cubes bg-color-greenLight"> <span
                            class="iconbox"> <i class="fa fa-picture-o fa-4x"></i> <span>Gallery </span> </span> </a>
                </li>
                <li>
                    <a href="javascript:void(0);" class="jarvismetro-tile big-cubes selected bg-color-pinkDark"> <span
                            class="iconbox"> <i class="fa fa-user fa-4x"></i> <span>My Profile </span> </span> </a>
                </li>
            </ul>
        </div>
        <?php include dirname(__DIR__) . '/include/javascript.php'; ?>
    <script>
        $('#span_project').html($('#span_project').html() + "（" + <?=$n_machine?>+"台机器）");
    </script>
</body>
</html>
