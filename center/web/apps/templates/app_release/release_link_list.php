<?php include __DIR__.'/../include/header.php'; ?>

<!-- MAIN PANEL -->
<div id="main" role="main">

<!-- RIBBON -->
<div id="ribbon">
    <!-- breadcrumb -->
    <ol class="breadcrumb">
        <li><a href="/">首页</a></li>
        <li><a href="/app_release/app_list">APP列表</a></li>
        <li>「<?=model('App')->getOSName($app['os'])?> - <?=$app['name']?>」
            <?php echo \App\PackageType::getPackageTypeName($package_type); ?>管理
        </li>
    </ol>

</div>

<div id="content">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
            <div class="jarviswidget jarviswidget-color-darken jarviswidget-sortable" id="wid-id-0"
                 data-widget-editbutton="false" role="widget" style="">
                <header role="heading">
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>「<?=model('App')->getOSName($app['os'])?> - <?=$app['name']?>」
                        <?php echo \App\PackageType::getPackageTypeName($package_type); ?>管理
                    </h2>
                    <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                </header>
                <div role="content">
                    <div class="widget-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <?php foreach ($data as $row) : ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        版本<?=$row['version_number']?>
                                                        <?php if ($package_type === \App\PackageType::INSTALL) : ?>
                                                            <?php if ($row['status']) : ?>
                                                                <span class="label label-success">已发布</span>
                                                            <?php else : ?>
                                                                <span class="label label-default">未发布</span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-md-8 text-align-right">
                                                        <?php if ($package_type === \App\PackageType::INSTALL) : ?>
                                                            <div class="btn-group">
                                                                <a href="/app_release/add_channel_release_link?release_id=<?=$row['id']?>&package_type=0" class="btn btn-info btn-xs">
                                                                    <i class="fa fa-plus"></i> 新增<?php echo \App\PackageType::getPackageTypeName($package_type); ?>
                                                                </a>
                                                            </div>
                                                            <div class="btn-group">
                                                                <?php if (!$row['status']) : ?>
                                                                    <a href="/app_release/enable_release?id=<?=$row['id']?>" class="btn btn-info btn-xs">
                                                                        <i class="fa fa-arrow-circle-up"></i> 发布
                                                                    </a>
                                                                <?php else : ?>
                                                                    <div class="btn-group">
                                                                        <a href="/app_release/disable_release?id=<?=$row['id']?>" class="btn btn-danger btn-xs">
                                                                            <i class="fa fa-arrow-circle-down"></i> 下架
                                                                        </a>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php else : ?>
                                                            <div class="btn-group">
                                                                <a href="/app_release/add_channel_release_link?release_id=<?=$row['id']?>&package_type=<?php echo $package_type; ?>" class="btn btn-info btn-xs">
                                                                    <i class="fa fa-plus"></i> 新增<?php echo \App\PackageType::getPackageTypeName($package_type); ?>
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </h3>
                                        </div>
                                        <?php if (!empty($row['release_link_list'])) : ?>
                                            <div class="panel-body no-padding">
                                                <table class="table table-bordered table-hover table-condensed">
                                                    <thead>
                                                        <tr>
                                                            <th>渠道名称</th>
                                                            <th>渠道标识符</th>
                                                            <th>缺省渠道 <i class="fa fa-question-circle text-primary" data-toggle="tooltip" data-placement="top" title="没有配置的渠道，都使用该渠道下发的内容"></i>
                                                            </th>
                                                            <th width="40%">
                                                                <?php if ($package_type === \App\PackageType::SHARED_OBJECT) : ?>
                                                                    下发内容
                                                                <?php else : ?>
                                                                    下载地址
                                                                <?php endif; ?>
                                                            </th>
                                                            <th width="10%">操作</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($row['release_link_list'] as $link) : ?>
                                                            <tr<?php if ($link['fallback_link']) : ?> class="success"<?php endif; ?>>
                                                                <td><?=$link['channel_name']?></td>
                                                                <td><?=$link['channel_key']?></td>
                                                                <td>
                                                                    <?php if ($link['fallback_link']) : ?>
                                                                        <i class="fa fa-check-circle text-success"></i>
                                                                    <?php else : ?>
                                                                        <i class="fa fa-times-circle text-danger"></i>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <?php if ($package_type === \App\PackageType::SHARED_OBJECT) : ?>
                                                                        <?php if ($data = json_decode($link['custom_data'], true)) : ?>
                                                                            <pre><?php echo json_encode($data, JSON_PRETTY_PRINT) ?></pre>
                                                                        <?php else : ?>
                                                                            <?php echo filter_value(array_get($link, 'custom_data'), true); ?>
                                                                        <?php endif; ?>
                                                                    <?php else : ?>
                                                                        <a href="<?=$link['release_link']?>"><?=$link['release_link']?></a>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <a href="/app_release/edit_channel_release_link?id=<?=$link['id']?>" class="btn btn-info btn-xs">编辑</a>
                                                                    <a href="/app_release/delete_channel_release_link?id=<?=$link['id']?>" class="btn btn-danger btn-xs btn-delete">删除</a>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else : ?>
                                            <div class="panel-body">
                                                没有渠道<?php echo \App\PackageType::getPackageTypeName($package_type); ?>，<a href="/app_release/add_channel_release_link?release_id=<?=$row['id']?>&package_type=<?php echo $package_type; ?>">点击新增渠道<?php echo \App\PackageType::getPackageTypeName($package_type); ?></a>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($package_type === \App\PackageType::INSTALL) : ?>
                                        <div class="panel-footer">
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
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
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
