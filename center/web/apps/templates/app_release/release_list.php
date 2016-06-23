<?php include __DIR__.'/../include/header.php'; ?>

<!-- MAIN PANEL -->
<div id="main" role="main">

<!-- RIBBON -->
<div id="ribbon">
    <!-- breadcrumb -->
    <ol class="breadcrumb">
        <li><a href="/">首页</a></li>
        <li><a href="/app_release/app_list">APP列表</a></li>
        <li>「<?=model('App')->getOSName($app['os'])?> - <?=$app['name']?>」APP版本管理</li>
    </ol>

</div>

<div id="content">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
            <div class="jarviswidget jarviswidget-color-darken jarviswidget-sortable" id="wid-id-0"
                 data-widget-editbutton="false" role="widget" style="">
                <header role="heading">
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>「<?=model('App')->getOSName($app['os'])?> - <?=$app['name']?>」APP版本管理</h2>
                    <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                </header>
                <div role="content">
                    <div class="widget-body-toolbar">
                        <div class="row">
                            <div class="col-md-12">
                                <a href="/app_release/new_release?app_id=<?=$app['id']?>" class="btn btn-primary pull-right">
                                    <i class="fa fa-list"></i> 新增APP版本
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="widget-body no-padding">
                        <table id="data_table_stats" class="table table-bordered table-">
                            <thead>
                                <tr>
                                    <th>版本</th>
                                    <?php if (intval($app['os']) === APP_OS_ANDROID) : ?>
                                        <th>Android版本code</th>
                                    <?php endif; ?>
                                    <th>更新策略</th>
                                    <th width="25%">操作</th>
                                </tr>
                            </thead>
                            <tbody id="data_table_body">
                                <?php foreach ($data as $row) : ?>
                                    <tr>
                                        <td>版本<?=$row['version_number']?></td>
                                        <?php if (intval($app['os']) === APP_OS_ANDROID) : ?>
                                            <td><?=$row['version_code']?></td>
                                        <?php endif; ?>
                                        <td>
                                            <?php if ($row['force_upgrade']) : ?>
                                                所有版本强制更新
                                            <?php elseif (!empty($row['force_upgrade_version'])) : ?>
                                                版本 <?=$row['force_upgrade_version']?> 强制更新
                                            <?php else : ?>
                                                不强制更新
                                            <?php endif; ?>
                                            <?php if ($row['prompt_interval']) : ?>
                                                ，升级弹窗提示周期 <?=$row['prompt_interval']?> 秒</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="/app_release/edit_release?id=<?=$row['id']?>" class="btn btn-info btn-xs">
                                                    <i class="fa fa-pencil"></i> 编辑
                                                </a>
                                            </div>
                                            <div class="btn-group">
                                                <a href="/app_release/delete_release?id=<?=$row['id']?>" class="btn btn-danger btn-xs btn-delete">
                                                    <i class="fa fa-pencil"></i> 删除
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="dt-row dt-bottom-row">
                            <div class="pager">
                                <span>总计：<strong><?=$pager->total?></strong>条</span>
                            </div>
                            <?=$pager->render()?>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </div> <!-- end .row -->
</div> <!-- end #content -->

</div>
<!-- END MAIN PANEL -->

<?php include __DIR__.'/../include/javascript.php'; ?>
<script>
    $(function() {
        pageSetUp();
        $('[data-toggle="tooltip"]').tooltip();
        $('.btn-delete').click(function () {
            return confirm('确认删除？');
        });
        /**
        $('.downswitch').click(function () {
            var release_id = $(this).data('releaseid');
        }); **/
    });
</script>

</body>
</html>
