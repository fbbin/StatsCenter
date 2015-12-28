<?php include __DIR__.'/../include/header.php'; ?>

<!-- MAIN PANEL -->
<div id="main" role="main">

<!-- RIBBON -->
<div id="ribbon">
    <!-- breadcrumb -->
    <ol class="breadcrumb">
        <li><a href="/">首页</a></li>
        <li><a href="/app_release/app_list">APP列表</a></li>
        <li>「<?=$app['name']?>」APP版本管理</li>
    </ol>

</div>

<div id="content">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
            <div class="jarviswidget jarviswidget-color-darken jarviswidget-sortable" id="wid-id-0"
                 data-widget-editbutton="false" role="widget" style="">
                <header role="heading">
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>「<?=$app['name']?>」APP版本管理</h2>
                    <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                </header>
                <div role="content">
                    <div class="widget-body-toolbar">
                        <div class="row">
                            <div class="col-md-12">
                                <a href="/app_release/new_release?app_id=<?=$app['id']?>" class="btn btn-primary pull-right">
                                    <i class="fa fa-plus"></i> 新增APP版本
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <?php foreach ($data as $row) : ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">
                                                <div class="row">
                                                    <div class="col-md-4">版本<?=$row['version_number']?></div>
                                                    <div class="col-md-8 text-align-right">
                                                        <div class="btn-group">
                                                            <a href="/app_release/edit_release?id=<?=$row['id']?>" class="btn btn-primary btn-xs">
                                                                <i class="fa fa-pencil"></i> 编辑
                                                            </a>
                                                        </div>
                                                        <div class="btn-group">
                                                            <a href="/app_release/add_channel_release_link?release_id=<?=$row['id']?>" class="btn btn-primary btn-xs">
                                                                <i class="fa fa-plus"></i> 新增渠道包
                                                            </a>
                                                        </div>
                                                        <!--
                                                        <div class="onoffswitch-container downswitch" data-releaseid="<?=$row['id']?>">
                                                            <span class="onoffswitch-title"></span>
                                                            <span class="onoffswitch">
                                                                <input class="onoffswitch-checkbox" id="autoopen" type="checkbox">
                                                                <label class="onoffswitch-label" for="autoopen">
                                                                    <span class="onoffswitch-inner" data-swchon-text="启用" data-swchoff-text="停用"></span>
                                                                    <span class="onoffswitch-switch"></span>
                                                                </label>
                                                            </span>
                                                        </div>
                                                        -->
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
                                                            <!--
                                                            <th>缺省渠道 <i class="fa fa-question-circle text-primary" data-toggle="tooltip" data-placement="top" title="没有指定下载地址的渠道，都会使用该渠道的下载地址"></i>
                                                            </th>
                                                            -->
                                                            <th>下载地址</th>
                                                            <th>操作</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($row['release_link_list'] as $link) : ?>
                                                            <tr>
                                                                <td><?=$link['channel_name']?></td>
                                                                <!--
                                                                <td>否</td>
                                                                -->
                                                                <td><a href="<?=$link['release_link']?>"><?=$link['release_link']?></a></td>
                                                                <td><a href="/app_release/edit_channel_release_link?id=<?=$link['id']?>" class="btn btn-info btn-xs">编辑</a></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else : ?>
                                            <div class="panel-body">
                                                没有渠道包，<a href="/app_release/add_channel_release_link?release_id=<?=$row['id']?>">点击新增渠道包</a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!--
                        <table id="data_table_stats" class="table table-bordered table-">
                            <thead>
                                <tr>
                                    <th>APP ID</th>
                                    <th>APP名称</th>
                                    <th>包名</th>
                                    <th>手机操作系统</th>
                                    <th>最新版本</th>
                                </tr>
                            </thead>
                            <tbody id="data_table_body">
                                <tr>
                                    <?php foreach ($data as $row) : ?>
                                        <tr>
                                            <td><?=$row['id']?></td>
                                            <td><a href="/app_release/release_list?app_id=<?=$row['id']?>"><?=$row['name']?></a></td>
                                            <td><?=$row['package_name']?></td>
                                            <td>
                                                <?php if ($row['os'] !== APP_OS_UNKNOWN) : ?>
                                                <?=$row['os_name']?>
                                                <?php else : ?>
                                                <?=$row['os_name']?>
                                                <?php endif; ?>
                                            </td>
                                            <td>1.2.3</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tr>
                            </tbody>
                        </table>

                        -->

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
        /**
        $('.downswitch').click(function () {
            var release_id = $(this).data('releaseid');
        }); **/
    });
</script>

</body>
</html>
