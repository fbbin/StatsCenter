<?php include __DIR__.'/../include/header.php'; ?>

<!-- MAIN PANEL -->
<div id="main" role="main">

    <!-- RIBBON -->
    <div id="ribbon">
        <!-- breadcrumb -->
        <ol class="breadcrumb">
            <li><a href="/">首页</a></li>
            <li><a href="/app_release/app_list">APP列表</a></li>
            <li><a href="/app_release/release_list?app_id=<?=$app['id']?>">「<?=$app['name']?>」APP版本管理</a></li>
            <li><?=isset($_GET['id']) ? '编辑' : '新增'?>「<?=$app['name']?> <?=$release['version_number']?>」下载包</li>
        </ol>
        </ol>
    </div>

    <div id="content">
        <!-- row -->
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?php if (!empty($msg)) : ?>
                    <div class="alert alert-success alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="alert-heading"><i class="fa fa-check-square-o"></i> 提示</h4>
                        <p><?=$msg?></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($errors)) : ?>
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="alert-heading"><i class="fa fa-ban"></i> 错误</h4>
                        <p><?=reset($errors)?></p>
                    </div>
                <?php endif ?>
            </div>

            <!-- NEW WIDGET START -->
            <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">

                <div class="jarviswidget jarviswidget-color-darken jarviswidget-sortable" id="wid-id-0"
                     data-widget-editbutton="false" role="widget" style="width: 600px;float: left">
                    <header role="heading">
                        <span class="widget-icon"> <i class="fa fa-pencil"></i> </span>
                        <h2><?=isset($_GET['id']) ? '编辑' : '新增'?>「<?=$app['name']?> <?=$release['version_number']?>」下载包</h2>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>
                    <div class="widget-body">
                        <form id="form1" class="smart-form" role="form" action="" method="post" enctype="multipart/form-data">
                            <?php if (!isset($_GET['id'])) : ?>
                            <fieldset>
                                <section>
                                    <label class="label">渠道 <b class="text-danger">*</b></label>
                                    <label class="select">
                                        <?=\Swoole\Form::select('app_channel', array_filter_value(array_get($form_data, 'channel_list'), true), filter_value(array_get($form_data, 'app_channel'), true), false, ['class' => 'select2'], false)?>
                                    </label>
                                </section>
                            </fieldset>
                            <?php endif; ?>
                            <fieldset>
                                <section>
                                    <label class="label">下载地址 <b class="text-danger">*</b></label>
                                    <label class="input">
                                        <?=\Swoole\Form::input('release_link', filter_value(array_get($form_data, 'release_link')))?>
                                    </label>
                                </section>
                                <?php if (!$has_fallback_link || $is_fallback_link) : ?>
                                    <section>
                                        <label class="label">缺省下载地址</label>
                                        <label class="checkbox">
                                            <input name="fallback_link" type="checkbox"<?php if ($is_fallback_link) : ?> checked="checked"<?php endif; ?>><i></i>
                                            没有指定下载地址的渠道，都使用该渠道的下载地址
                                        </label>
                                    </section>
                                <?php else : ?>
                                    <section>
                                        <label class="label">缺省下载地址</label>
                                        <label class="checkbox state-disabled">
                                            <input name="fallback_link" type="checkbox"><i></i>
                                            没有指定下载地址的渠道，都使用该渠道的下载地址
                                        </label>
                                        <div class="note">只能有一个缺省下载地址，所以这里无法勾选</div>
                                    </section>
                                <?php endif; ?>
                            </fieldset>
                            <footer>
                                <button type="submit" id="sub" class="btn btn-primary">
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
