<?php include __DIR__.'/../include/header.php'; ?>

<!-- MAIN PANEL -->
<div id="main" role="main">

    <!-- RIBBON -->
    <div id="ribbon">
        <ol class="breadcrumb">
            <li><a href="/">首页</a></li>
            <li><a href="/setting/app_list">APP列表</a></li>
            <li>编辑APP</li>
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
                        <h2>项目管理</h2>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>
                    <div class="widget-body">
                        <form id="form1" class="smart-form" role="form" action="" method="post" enctype="multipart/form-data">
                            <legend><strong>项目信息</strong></legend>
                            <fieldset>
                                <section>
                                    <label class="label">APP名称 <b class="text-danger">*</b></label>
                                    <label class="input">
                                        <?=\Swoole\Form::input('name', filter_value(array_get($form_data, 'name'), true))?>
                                    </label>
                                </section>
                            </fieldset>
                            <fieldset>
                                <section>
                                    <label class="label">包名 <b class="text-danger">*</b></label>
                                    <label class="input">
                                        <?=\Swoole\Form::input('package_name', filter_value(array_get($form_data, 'package_name'), true))?>
                                    </label>
                                </section>
                            </fieldset>
                            <fieldset>
                                <section>
                                    <label class="label">app_key <b class="text-danger">*</b></label>
                                    <label class="input">
                                        <?=\Swoole\Form::input('app_key', filter_value(array_get($form_data, 'app_key'), true))?>
                                    </label>
                                </section>
                            </fieldset>
                            <fieldset>
                                <section>
                                    <label class="label">OS <b class="text-danger">*</b></label>
                                    <label class="select">
                                        <?=\Swoole\Form::select('os', array_get($form_data, 'os_list'), filter_value(array_get($form_data, 'os'), true), false, ['class' => 'select2'], false)?>
                                    </label>
                                </section>
                            </fieldset>
                            <fieldset id="1_cert">
                                <section>
                                    <label class="label">APNS证书 <?=filter_value(array_get($form_data, 'apns_cert'), true)?></label>
                                    <div class="input input-file">
                                        <span class="button state-success"><input id="apns_cert" type="file" name="apns_cert" onchange="this.parentNode.nextSibling.value = this.value" class="valid">Browse</span><input type="text" placeholder="选择文件" readonly="">
                                    </div>
                                </section>
                            </fieldset>
                            <fieldset id="1_pass">
                                <section>
                                    <label class="label">APNS密钥</label>
                                    <label class="input">
                                        <?=\Swoole\Form::input('apns_pwd', filter_value(array_get($form_data, 'apns_pwd'), true))?>
                                    </label>
                                </section>
                            </fieldset>
                            <fieldset id="2_cert">
                                <section>
                                    <label class="label">友盟证书 <?=filter_value(array_get($form_data, 'umeng_cert'), true)?></label>
                                    <div class="input input-file">
                                        <span class="button state-success"><input id="umeng_cert" type="file" name="umeng_cert" onchange="this.parentNode.nextSibling.value = this.value" class="valid">Browse</span><input type="text" placeholder="选择文件" readonly="">
                                    </div>
                                </section>
                            </fieldset>
                            <fieldset id="2_pass">
                                <section>
                                    <label class="label">友盟密钥</label>
                                    <label class="input">
                                        <?=\Swoole\Form::input('umeng_pwd', filter_value(array_get($form_data, 'umeng_pwd'), true))?>
                                    </label>
                                </section>
                            </fieldset>
                            <fieldset>
                                <section>
                                    <label class="label">项目状态 <b class="text-danger">*</b></label>
                                    <label class="radio state-success" style="display: inline-block">
                                        <input type="radio" name="enable"
                                               value="1" <?php echo filter_value(array_get($form_data, 'enable'), true) == 1 ? 'checked' : ''; ?>>
                                        <i></i>开启
                                    </label>
                                    <label class="radio state-error" style="display: inline-block">
                                        <input type="radio" name="enable"
                                               value="2" <?php echo filter_value(array_get($form_data, 'enable'), true) == 2 ? 'checked' : ''; ?>>
                                        <i></i>关闭
                                    </label>
                                </section>
                            </fieldset>
                            <footer>
                                <button type="submit" id="sub" class="btn btn-primary">
                                    提交
                                </button>
                                <button type="button" class="btn btn-default">
                                    <a href="/project/lists">返回</a>
                                </button>
                            </footer>
                        </form>
                    </div>
                </div>
                <div class="jarviswidget jarviswidget-color-darken jarviswidget-sortable" id="wid-id-0"
                     data-widget-editbutton="false" role="widget" style="padding-left:10px;width: 600px;float: left">
                    <header role="heading">
                        <span class="widget-icon"> <i class="fa fa-pencil"></i> </span>
                        <h2>证书管理</h2>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>
                    <div class="widget-body">
                        <form id="form1" class="smart-form" role="form" action="/setting/app_gen_cert/" method="post" enctype="multipart/form-data">
                            <?=\Swoole\Form::hidden('id', intval(array_get($form_data, 'id')))?>
                            <legend><strong>证书信息</strong></legend>
                            <footer>
                                <?php if (!empty($cert)) : ?>
                                <?php
                                if (filter_value(array_get($form_data, 'os'), true) == 1) {
                                    ?>
                                    <fieldset>
                                        <section>
                                            <label class="label">生成的证书</label>
                                            <label class="label">
                                                <pre>
                                                <?=filter_value(array_get($cert, 'apns_pem_content'), true)?>
                                                </pre>
                                            </label>
                                        </section>
                                    </fieldset>
                                    <?php
                                }
                                ?>
                                <?php
                                if (filter_value(array_get($form_data, 'os'), true) == 2) {
                                    ?>
                                    <fieldset>
                                        <section>
                                            <label class="label">证书</label>
                                            <label class="label">
                                                <pre>
                                                <?=filter_value(array_get($cert, 'umeng_pem_content'), true)?>
                                                </pre>
                                            </label>
                                        </section>
                                    </fieldset>
                                    <fieldset>
                                        <section>
                                            <label class="label">证书密钥</label>
                                            <label class="label">
                                                <pre><?=filter_value(array_get($cert, 'umeng_key_content'), true)?></pre>
                                            </label>
                                        </section>
                                    </fieldset>
                                    <?php
                                }
                                ?>
                                <?php endif; ?>
                                <button type="submit" id="init" class="btn btn-danger">
                                    初始化证书
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
        init();
        $("#os").click(function(){
            var os = $(this).val();
            if (os == 1) {
                h = 2;
                s = 1;
            } else if (os == 2){
                h = 1;
                s = 2;
            }
            $("#"+h+"_cert").hide();
            $("#"+h+"_pass").hide();
            $("#"+s+"_cert").show();
            $("#"+s+"_pass").show();
        });

        function init()
        {
            var os = $("#os").val();
            var h = 2;
            if (os == 1) {
                h = 2;
            } else if (os == 2){
                h = 1;
            }
            $("#"+h+"_cert").hide();
            $("#"+h+"_pass").hide();
        }
    });
</script>

</body>
</html>
