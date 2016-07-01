<?php include __DIR__.'/../include/header.php'; ?>

<!-- MAIN PANEL -->
<div id="main" role="main">

    <!-- RIBBON -->
    <div id="ribbon">
        <!-- breadcrumb -->
        <ol class="breadcrumb">
            <li><a href="/">首页</a></li>
            <li><a href="/setting/app_project_list">APP项目列表</a></li>
            <li><?=$page_title?></li>
        </ol>
        </ol>
    </div>

    <div id="content">
        <!-- row -->
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?php if (!empty($success)) : ?>
                    <div class="alert alert-success alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="alert-heading"><i class="fa fa-check-square-o"></i> 创建成功</h4>
                    </div>
                <?php elseif (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="alert-heading"><i class="fa fa-ban"></i> 错误</h4>
                        <p><?=$error?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)) : ?>
                    <h4 class="alert alert-info"> APPID: <strong><?= $success['appid'] ?></strong></h4>
                    <h4 class="alert alert-info"> APPKEY: <strong><?= $success['appkey'] ?></strong></h4>
                <?php endif; ?>
            </div>

            <!-- NEW WIDGET START -->
            <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-color-darken jarviswidget-sortable" id="wid-id-0"
                     data-widget-editbutton="false" role="widget" style="width: 600px;float: left">
                    <header role="heading">
                        <span class="widget-icon"> <i class="fa fa-pencil"></i> </span>
                        <h2><?=$page_title?></h2>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>
                    <div class="widget-body">
                        <form class="smart-form" role="form" action="/setting/passport/" method="post">
                            <fieldset>
                                <section>
                                    <label class="label">系统名称<b class="text-danger">*</b></label>
                                    <label class="input">
                                        <?=\Swoole\Form::input('name', filter_value(array_get($form_data, 'name'), true))?>
                                    </label>
                                </section>
                                <section>
                                    <label class="label">系统介绍</label>
                                    <label class="textarea">
                                        <?=\Swoole\Form::text('intro', filter_value(array_get($form_data, 'intro'), true))?>
                                    </label>
                                </section>
                            </fieldset>
                            <footer>
                                <button type="submit" class="btn btn-primary">
                                    提交
                                </button>
                                <button type="button" class="btn btn-default" onclick="window.history.go(-1);">
                                    返回
                                </button>
                            </footer>
                        </form>
                    </div>
                </div>
            </article>
        </div>
    </div>
    <!-- END MAIN CONTENT -->

</div>
<!-- END MAIN PANEL -->

<?php include dirname(__DIR__).'/include/javascript.php'; ?>
<script>
    $(function() {
        pageSetUp();
    });
</script>

</body>
</html>
