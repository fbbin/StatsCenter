<?php include __DIR__.'/../include/header.php'; ?>

<!-- MAIN PANEL -->
<div id="main" role="main">

<!-- RIBBON -->
<div id="ribbon">
    <!-- breadcrumb -->
    <ol class="breadcrumb">
        <li><a href="/">首页</a></li>
        <li><?=$page_title?></li>
    </ol>

</div>

<div id="content">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
            <div class="jarviswidget jarviswidget-color-darken jarviswidget-sortable" id="wid-id-0"
                 data-widget-editbutton="false" role="widget" style="">
                <header role="heading">
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2><?=$page_title?></h2>
                    <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                </header>
                <div role="content">
                    <div class="widget-body-toolbar">
                        <div class="row">
                            <div class="col-md-12">
                                <a href="/app_release/channel_list" class="btn btn-primary pull-right">
                                    <i class="fa fa-list"></i> 渠道管理
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="widget-body no-padding">
                        <table id="data_table_stats" class="table table-bordered table-">
                            <thead>
                                <tr>
                                    <th>APP ID</th>
                                    <th>APP名称</th>
                                    <th>包名</th>
                                    <th>最新版本号</th>
                                    <th>已发布版本数</th>
                                    <th>手机操作系统</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody id="data_table_body">
                                <?php foreach ($data as $row) : ?>
                                    <tr>
                                        <td><?=$row['id']?></td>
                                        <td><a href="/app_release/release_list?app_id=<?=$row['id']?>"><?=$row['name']?></a></td>
                                        <td><?=$row['package_name']?></td>
                                        <?php if (isset($release_info_list[$row['id']])) : ?>
                                            <td><a href="/app_release/edit_release?version=<?=urlencode(version_int_to_string($release_info_list[$row['id']]['max_version']))?>"><?=version_int_to_string($release_info_list[$row['id']]['max_version'])?></a></td>
                                            <td><a href="/app_release/release_list?app_id=<?=$row['id']?>" class="badge"><?=$release_info_list[$row['id']]['num_version']?></a></td>
                                        <?php else : ?>
                                            <td>暂无</td>
                                            <td><a href="/app_release/release_list?app_id=<?=$row['id']?>" class="badge">0</a></td>
                                        <?php endif; ?>
                                        <td>
                                            <?php if ($row['os'] !== APP_OS_UNKNOWN) : ?>
                                                <?=$row['os_name']?>
                                            <?php else : ?>
                                                <?=$row['os_name']?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="/app_release/release_list?app_id=<?=$row['id']?>" class="btn btn-info btn-xs">版本管理</a>
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
        $("#submit").click(function(){
            $("#form").submit();
        });
    });
</script>

</body>
</html>
