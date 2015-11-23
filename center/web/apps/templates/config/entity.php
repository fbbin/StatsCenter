<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title><?= Swoole::$php->config['common']['site_name'] ?></title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?php include __DIR__ . '/../include/css.php'; ?>
    <script src='<?= WEBROOT ?>/static/jsoneditor/dist/jsoneditor.js'></script>
    <link rel="stylesheet" href="<?= WEBROOT ?>/static/jsoneditor/dist/jsoneditor.css">
</head>
<body class="">
<header style="background: #E4E4E4;color: #22201F" id="header">
    <span><img style="vertical-align:top;padding: 8px" width="80"
               src="<?=Swoole::$php->config['common']['logo_url']?>"/></span>
    <span id="logo" style="margin-left: 0px"><strong
            style="font-size: 18px;"><?= Swoole::$php->config['common']['site_name'] ?></strong></span>
    <span style="float: right;padding: 15px 5px;font-weight: bolder">
        <span style="text-transform: none;">
                    <a style="text-decoration: none" href="/user/edit">用户：<?= $_COOKIE['username'] ?>
        </span>
        <span style="text-transform: none;padding: 15px 5px;">
                    <a style="text-decoration: none;font-weight: bolder" href="/page/logout/">退出</a>
        </span>
    </span>
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
        <!-- breadcrumb -->
        <ol class="breadcrumb">
            <li>Home</li>
            <li>内容管理</li>
        </ol>

    </div>

    <div id="content">
        <div class="row">
            <article class="col-sm-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-sortable" id="wid-id-0" data-widget-togglebutton="false"
                     data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-colorbutton="false"
                     data-widget-deletebutton="false" role="widget" style="width: 933px">
                    <header role="heading">
                        <ul class="nav nav-tabs pull-left in">
                            <li class="active">
                                <a><i class="fa fa-clock-o"></i>
                                    新增配置
                                </a>
                            </li>
                        </ul>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                    </header>

                    <div role="content" class="no-padding">
                        <div class="widget-body">
                            <form class="smart-form" id="form" method="post">
                                <?php include dirname(__DIR__) . '/include/msg.php'; ?>
                                <fieldset>
                                    <section>
                                        <label class="label">名称（仅用于管理平台展示，必填）</label>
                                        <label class="input">
                                            <input class="input" name="name" value="<?=$name?>" data-placeholder="请输入配置名称"/>
                                            <input type="hidden" name="json" id="json" />
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">KEY（调用端使用KEY来访问配置，必填）</label>
                                        <label class="input">
                                            <input class="input" name="ckey" value="<?=$ckey?>" data-placeholder="请输入配置KEY"/>
                                        </label>
                                    </section>
                                </fieldset>
                                <fieldset>
                                    <div class="widget-body bg-color-white" style="height: 600px;"
                                         id="jsoneditor"></div>
                                </fieldset>
                                <footer>
                                    <button type="button" class="btn btn-primary" id='json_submit'>
                                        提交
                                    </button>
                                    <button type="button" class="btn btn-default" onclick="window.history.back();">
                                        返回
                                    </button>
                                </footer>
                            </form>
                        </div>
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
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/include/javascript.php'; ?>
<script>
    var container = document.getElementById('jsoneditor');
    var options = {
        mode: 'tree',
        modes: ['code', 'tree'], // allowed modes
        error: function (err) {
            alert(err.toString());
        }
    };
    var editor = new JSONEditor(container, options);
    editor.set(<?=$json?>);
    $('#json_submit').click(function () {
        $('#json').val(JSON.stringify(editor.get()));
        $('#form').submit();
    });
    $(function () {
        $("#form").validate({
            rules: {
                name: {
                    required: true
                },
                ckey: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: '名称不能为空'
                },
                ckey: {
                    required: 'KEY不能为空'
                }
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element.parent());
            }
        });
    });
</script>
</body>
</html>