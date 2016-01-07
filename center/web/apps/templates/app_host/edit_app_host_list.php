<?php include __DIR__.'/../include/header.php'; ?>

<!-- MAIN PANEL -->
<div id="main" role="main">

    <!-- RIBBON -->
    <div id="ribbon">
        <!-- breadcrumb -->
        <ol class="breadcrumb">
            <li><a href="/">首页</a></li>
            <li><a href="/app_host/project_list">APP项目列表</a></li>
            <li><a href="/app_host/app_host_list?id=<?=intval($project['id'])?>">「<?=$project['name']?> (<?=$project['app_key']?>)」APP项目接口列表</a></li>
            <li><?=$page_title?></li>
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
                     data-widget-editbutton="false" role="widget">
                    <header role="heading">
                        <span class="widget-icon"> <i class="fa fa-pencil"></i> </span>
                        <h2><?=$page_title?></h2>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>
                    <div class="widget-body">
                        <form id="form1" class="smart-form" role="form" action="" method="post" enctype="multipart/form-data">
                            <fieldset>
                                <section>
                                    <label class="label">下发接口</label>
                                    <div class="row">
                                        <?php foreach ($data as $index => $row) : ?>
                                            <?php if ($index % 3 === 0) : ?>
                                                <div class="col col-4">
                                            <?php endif; ?>
                                            <label class="checkbox">
                                                <input name="project_list[<?=$row['ckey']?>]" type="checkbox"<?php if (!empty($form_data['project_list'][$row['ckey']])) : ?> checked="checked"<?php endif; ?>>
                                                <i></i> <?=$row['name']?>
                                            </label>
                                            <?php if ($index %3 === 2) : ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach ?>
                                        <?php if ($index %3 !== 2) : ?>
                                               </div>
                                        <?php endif; ?>
                                    </div>
                                </section>
                            </fieldset>
                            <footer>
                                <button type="submit" name="submited" value="1" id="sub" class="btn btn-primary">
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
