<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title><?=Swoole::$php->config['common']['site_name']?></title>
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
        <div class="row">
            <article class="col-sm-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-sortable" id="wid-id-0" data-widget-togglebutton="false"
                     data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-colorbutton="false"
                     data-widget-deletebutton="false" role="widget" style="width: 500px">
                    <header role="heading">
                        <ul class="nav nav-tabs pull-left in">
                            <li class="active">
                                <a><i class="fa fa-clock-o"></i>
                                    <span class="hidden-mobile hidden-tablet">新增用户</span>
                                </a>
                            </li>
                        </ul>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                    </header>

                    <!-- widget div-->
                    <div class="no-padding" role="content">
                        <div class="widget-body">
                            <form class="smart-form" method="post">
                                <?=$this->value($form, 'id')?>
                                <fieldset>
                                    <section>
                                        <label class="label">用户名</label>
                                        <label class="input"><i class="icon-prepend fa fa-user"></i>
                                            <?=$form['username']?>
                                        </label>
                                        <div class="note">
                                            请使用真实姓名的全拼
                                        </div>
                                    </section>
                                </fieldset>
                                <fieldset>
                                    <section>
                                        <label class="label">真实姓名</label>
                                        <label class="input"><i class="icon-prepend fa fa-user"></i>
                                            <?=$form['realname']?>
                                        </label>
                                        <div class="note">
                                            默认密码为123456
                                        </div>
                                    </section>
                                    <section>
                                        <label class="label">用户类型</label>
                                        <label class="input">
                                            <?=$form['usertype']?>
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">可参与项目（为空表示可以参与全部项目）</label>
                                        <label class="input"> <i class="icon-prepend fa fa-phone"></i>
                                            <?= $form['project_id'] ?>
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">特殊权限分配（使用逗号分割）</label>
                                        <label class="textarea">
                                            <?= $form['rules'] ?>
                                        </label>
                                    </section>
                                </fieldset>
                                <fieldset>
                                    <section>
                                        <label class="label">子系统权限分配</label>
                                        <div class="inline-group">
                                            <label class="checkbox">
                                                <input type="checkbox" name="git_account" value="1" <?php if ($gitAccount) echo 'checked="checked" disabled=disabled"';?>>
                                                <i></i>开通GIT账户</label>
                                            <?php
                                            if (isset($crowdUser) and $crowdUser === false):
                                            ?>
                                                <label class="checkbox">
                                                    <input type="checkbox" name="crowd_user" value="1">
                                                    <i></i>开通Confluence账户</label>
                                            <?php endif; ?>
                                        </div>
                                    </section>
                                </fieldset>
                                <fieldset>
                                    <section>
                                        <label class="label">手机号码</label>
                                        <label class="input"> <i class="icon-prepend fa fa-phone"></i>
                                            <?=$form['mobile']?>
                                        </label>
                                        <div class="note">
                                            报警绑定手机号
                                        </div>
                                    </section>
                                    <section>
                                        <label class="label">钉钉ID</label>
                                        <label class="input"> <i class="icon-prepend fa fa-user"></i>
                                            <?=$form['dingdingid']?>
                                        </label>
                                        <div class="note">
                                            钉钉UserId
                                        </div>
                                    </section>
                                    <section>
                                        <label class="label">微信号</label>
                                        <label class="input"> <i class="icon-prepend fa fa-user"></i>
                                            <?=$form['weixinid']?>
                                        </label>
                                        <div class="note">
                                            绑定报警微信
                                        </div>
                                    </section>
                                </fieldset>
                                <footer>
                                    <button type="submit" class="btn btn-primary">
                                        提交
                                    </button>
                                    <button type="button" class="btn btn-default" onclick="window.history.back();">
                                        返回
                                    </button>
                                </footer>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>

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
                <a href="#gallery.html" class="jarvismetro-tile big-cubes bg-color-greenLight"> <span
                        class="iconbox"> <i
                            class="fa fa-picture-o fa-4x"></i> <span>Gallery </span> </span> </a>
            </li>
            <li>
                <a href="javascript:void(0);" class="jarvismetro-tile big-cubes selected bg-color-pinkDark"> <span
                        class="iconbox"> <i class="fa fa-user fa-4x"></i> <span>My Profile </span> </span> </a>
            </li>
        </ul>
    </div>
    <?php include dirname(__DIR__) . '/include/javascript.php'; ?>
</body>
    <script >
        pageSetUp();
    </script>
</html>
