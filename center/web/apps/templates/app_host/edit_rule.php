<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title><?=Swoole::$php->config['common']['site_name']?></title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?php include __DIR__.'/../include/css.php'; ?>
</head>
<body class="">
<header style="background: #E4E4E4;color: #22201F" id="header">
    <?php include __DIR__ . '/../include/top_menu.php'; ?>
</header>
<aside id="left-panel">
    <!--            --><?php //include __DIR__.'/../include/login_info.php'; ?>
    <?php include __DIR__.'/../include/leftmenu.php'; ?>
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
        <div class="row">
            <?php if (!empty($error)) : ?>
            <div class="alert alert-block alert-warning">
                <a class="close" data-dismiss="alert" href="#">×</a>
                <p><?=$error?></p>
            </div>
            <?php endif; ?>

            <article class="col-sm-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-sortable" id="wid-id-0" data-widget-togglebutton="false"
                     data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-colorbutton="false"
                     data-widget-deletebutton="false" role="widget" style="width: 800px">
                    <?php if ($view === 'add_rule') : ?>
                    <header role="heading">
                        <ul class="nav nav-tabs pull-left in">
                            <li<?php if ($type === 'uid') : ?> class="active"<?php endif; ?>>
                                <a href="/app_host/add_rule?type=uid">
                                    <span class="hidden-mobile hidden-tablet">按UID指定</span>
                                </a>
                            </li>
                            <li<?php if ($type === 'openudid') : ?> class="active"<?php endif; ?>>
                                <a href="/app_host/add_rule?type=openudid">
                                    <span class="hidden-mobile hidden-tablet">按OpenUDID指定</span>
                                </a>
                            </li>
                        </ul>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                    </header>
                    <?php elseif ($view === 'edit_rule') : ?>
                    <header role="heading">
                        <ul class="nav nav-tabs pull-left in">
                            <?php if ($type === 'uid') : ?>
                                <li class="active">
                                    <a href="/app_host/add_rule?type=uid">
                                        <span class="hidden-mobile hidden-tablet">按UID指定</span>
                                    </a>
                                </li>
                            <?php elseif ($type === 'openudid') : ?>
                                <li class="active">
                                    <a href="/app_host/add_rule?type=openudid">
                                        <span class="hidden-mobile hidden-tablet">按OpenUDID指定</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                    </header>
                    <?php endif; ?>

                    <!-- widget div-->
                    <div class="no-padding tab-content" role="content">
                        <?php if ($type === 'uid') : ?>
                        <div class="widget-body">
                            <form class="smart-form" method="post">
                                <fieldset>
                                    <section>
                                        <label class="label">UID（必填）</label>
                                        <label class="input">
                                            <?=$form['uid']?>
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">项目标识符（必填）</label>
                                        <label class="input">
                                            <?=$form['project_id']?>
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">环境标识符（必填，同“UID+项目”只可指定一个环境，重复指定会覆盖）</label>
                                        <label class="input">
                                            <?=$form['env_id']?>
                                        </label>
                                    </section>
                                </fieldset>
                                <footer>
                                    <input type="hidden" name="type" value="uid">
                                    <button type="submit" class="btn btn-primary">
                                        提交
                                    </button>
                                    <button type="button" class="btn btn-default" onclick="window.history.back();">
                                        返回
                                    </button>
                                </footer>
                            </form>
                        </div>

                        <?php else : ?>

                        <div class="widget-body">
                            <form class="smart-form" method="post">
                                <fieldset>
                                    <section>
                                        <label class="label">OpenUDID（必填）</label>
                                        <label class="input">
                                            <?=$form['openudid']?>
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">项目标识符（必填）</label>
                                        <label class="input">
                                            <?=$form['project_id']?>
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">环境标识符（必填，同“OpenUDID+项目”只可指定一个环境，重复指定会覆盖）</label>
                                        <label class="input">
                                            <?=$form['env_id']?>
                                        </label>
                                    </section>
                                </fieldset>
                                <footer>
                                    <input type="hidden" name="type" value="openudid">
                                    <button type="submit" class="btn btn-primary">
                                        提交
                                    </button>
                                    <button type="button" class="btn btn-default" onclick="window.history.back();">
                                        返回
                                    </button>
                                </footer>
                            </form>
                        </div>

                        <?php endif; ?>
                    </div>
                </div>
            </article>
        </div>
    </div>

    <?php include dirname(__DIR__) . '/include/javascript.php'; ?>
</body>
    <script >
        pageSetUp();
    </script>
</html>

