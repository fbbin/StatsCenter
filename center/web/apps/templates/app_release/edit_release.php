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
        <li>新增「<?=$app['name']?>」APP版本</li>
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
                        <h2>新增「<?=$app['name']?>」APP版本【APP ID：<?=$app['id']?>】</h2>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>
                    <div class="widget-body">
                        <form id="form1" class="smart-form" role="form" action="" method="post" enctype="multipart/form-data">
                            <fieldset>
                                <section>
                                    <label class="label">APP版本号 <b class="text-danger">*</b></label>
                                    <label class="input">
                                        <?=\Swoole\Form::input('version_number', filter_value(array_get($form_data, 'version_number')))?>
                                    </label>
    <div class="note">格式：x.y.z，x、y、z均为数字，取之范围均为<b class="text-warning">0~255</b>，如1.2.4</div>
                                </section>
                            </fieldset>
                            <fieldset>
                                <section>
                                    <label class="label">弹框标题 <b class="text-danger">*</b></label>
                                    <label class="input">
                                        <?=\Swoole\Form::input('prompt_title', filter_value(array_get($form_data, 'prompt_title')))?>
                                    </label>
                                </section>
                            </fieldset>
                            <fieldset>
                                <section>
                                    <label class="label">弹框内容 <b class="text-danger">*</b></label>
                                    <div class="textarea">
                                        <label class="textarea textarea-resizable">
                                            <?=\Swoole\Form::text('prompt_content', filter_value(array_get($form_data, 'prompt_content')), ['rows' => 5])?>
										</label>
                                    </div>
                                </section>
                            </fieldset>
                            <fieldset>
                                <section>
                                    <label class="label">弹框提示周期（秒，86400为一天） <b class="text-danger">*</b></label>
                                    <div class="input">
                                        <?=\Swoole\Form::input('prompt_interval', filter_value(array_get($form_data, 'prompt_interval', 86400)))?>
                                    </div>
                                    <div class="note">填数字，不要带单位</div>
                                </section>
                            </fieldset>
                            <fieldset>
                                <section>
                                    <label class="label">弹框确认按钮文字 <b class="text-danger">*</b></label>
                                    <div class="input">
                                        <?=\Swoole\Form::input('prompt_confirm_button_text', filter_value(array_get($form_data, 'prompt_confirm_button_text', '确定')))?>
                                    </div>
                                </section>
                            </fieldset>
                            <fieldset>
                                <section>
                                    <label class="label">弹框取消按钮文字 <b class="text-danger">*</b></label>
                                    <div class="input">
                                        <?=\Swoole\Form::input('prompt_cancel_button_text', filter_value(array_get($form_data, 'prompt_cancel_button_text', '取消')))?>
                                    </div>
                                </section>
                            </fieldset>
                            <fieldset>
                                <section>
                                    <label class="label">强制更新策略 <b class="text-danger">*</b></label>
                                    <label class="radio">
    <input type="radio" name="force_upgrade_strategy" value="3"<?php if (intval(array_get($form_data, 'force_upgrade_strategy')) === 3) : ?> checked="checked"<?php endif; ?>>
                                        <i></i> 不强制更新
                                    </label>
                                    <label class="radio">
    <input type="radio" name="force_upgrade_strategy" value="0"<?php if (intval(array_get($form_data, 'force_upgrade_strategy')) === 0) : ?> checked="checked"<?php endif; ?>>
                                        <i></i> 所有版本强制更新
                                    </label>
                                    <label class="radio">
    <input type="radio" name="force_upgrade_strategy" value="1"<?php if (intval(array_get($form_data, 'force_upgrade_strategy')) === 1) : ?> checked="checked"<?php endif; ?>>
                                        <i></i> 上一个版本强制更新
                                    </label>
                                    <label class="radio">
    <input type="radio" name="force_upgrade_strategy" value="2"<?php if (intval(array_get($form_data, 'force_upgrade_strategy')) === 2) : ?> checked="checked"<?php endif; ?>>
                                        <i></i> 指定版本强制更新
                                    </label>
                                    <label class="input">
    <?=\Swoole\Form::input('force_upgrade_version', filter_value(array_get($form_data, 'force_upgrade_version')))?>
                                        <div class="note">用半角逗号隔开，如“1.1.2,1.2.3”</div>
                                    </label>
                                </section>
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
