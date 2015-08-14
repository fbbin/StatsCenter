<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title><?=Swoole::$php->config['common']['site_name']?></title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?php include __DIR__.'/../include/css.php'; ?>
    <link rel="stylesheet" type="text/css" media="screen" href="/static/plupload-2.1.8/jquery.plupload.queue/css/jquery.plupload.queue.css">
</head>
<body class="">
<header style="background: #E4E4E4;color: #22201F" id="header">
    <span><img style="vertical-align:top;padding: 8px" width="80" src="<?=Swoole::$php->config['common']['logo_url']?>" /></span>
    <span id="logo" style="margin-left: 0px"><strong style="font-size: 18px;"><?=Swoole::$php->config['common']['site_name']?></strong></span>
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
                    <!-- widget div-->
                    <div class="no-padding" role="content">
                        <div class="widget-body">
                            <ul style="margin-top:20px;color:red;">
                                <li>同名文件会覆盖原文件。</li>
                            </ul>
                            <div id="uploader" style="width: 100%; height: 330px;">浏览器不支持上传功能。</div>
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
    <script type="text/javascript" src="/static/plupload-2.1.8/plupload.full.min.js"></script>
    <script type="text/javascript" src="/static/plupload-2.1.8/jquery.plupload.queue/jquery.plupload.queue.js"></script>
    <script type="text/javascript" src="/static/plupload-2.1.8/i18n/zh_CN.js"></script>
    <script>
        pageSetUp();

        $(function() {
            var url_list = [];

            var uploader = $("#uploader").pluploadQueue({
                runtimes: 'html5,flash,silverlight,html4',
                url: 'http://file.chelun.com/upload.php',
                multi_selection: true,
                prevent_duplicates: true,
                dragdrop: true,
                multiple_queues: true,
                multipart_params: {
                    ftype: 100,
                    retmd5: 1,
                },

                filters : {
                    max_file_size : '100mb',
                    mime_types: [
                       {title : "Apk files", extensions : "apk"}
                    ]
                },

                flash_swf_url: '../js/Moxie.swf',
                silverlight_xap_url: '../js/Moxie.xap',

                init: {
                    FileUploaded: function(uploader, file, response) {
                        var data = $.parseJSON(response.response);
                        if (data.code == 0)
                        {
                            url_list.push(JSON.stringify(data.data.file));
                        }
                    },
                    uploadComplete: function(uploader, files) {
                        $.post('/upload/add', {'url_list': url_list}, function(data) {
                            if (data.code != 0)
                            {
                                alert('上传文件失败');
                            }
                        }, 'json');
                    }
                }
            });
        });
    </script>
</body>
</html>

