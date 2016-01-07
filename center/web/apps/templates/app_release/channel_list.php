<?php include __DIR__.'/../include/header.php'; ?>

<!-- MAIN PANEL -->
<div id="main" role="main">

<!-- RIBBON -->
<div id="ribbon">
    <!-- breadcrumb -->
    <ol class="breadcrumb">
        <li><a href="/">首页</a></li>
        <li><a href="/app_release/app_list">APP列表</a></li>
        <li>渠道列表</li>
    </ol>

</div>

<div id="content">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
            <div class="jarviswidget jarviswidget-color-darken jarviswidget-sortable" id="wid-id-0"
                 data-widget-editbutton="false" role="widget" style="">
                <header role="heading">
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>渠道列表</h2>
                    <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                </header>
                <div role="content">
                    <div class="widget-body-toolbar">
                        <div class="row">
                            <div class="col-md-12">
                                <a href="/app_release/add_channel" class="btn btn-primary pull-right">
                                    <i class="fa fa-plus"></i> 新增渠道
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="widget-body no-padding">
                        <table id="data_table_stats" class="table table-bordered table-">
                            <thead>
                                <tr>
                                    <th>渠道ID</th>
                                    <th>渠道名称</th>
                                    <th>渠道标识符</th>
                                    <th>下载包数量</th>
                                    <th>添加时间</th>
                                    <th>更新时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody id="data_table_body">
                                <?php foreach ($data as $row) : ?>
                                    <tr>
                                        <td><?=$row['id']?></td>
                                        <td><?=$row['name']?></td>
                                        <td><?=$row['channel_key']?></td>
                                        <td><?=isset($release_link_info[$row['id']]) ? $release_link_info[$row['id']]['num_release_link'] : 0?></td>
                                        <td><?=$row['create_time']?></td>
                                        <td><?=$row['update_time']?></td>
                                        <td>
                                            <a href="/app_release/edit_channel?id=<?=$row['id']?>" class="btn btn-info btn-xs">编辑</a>
                                            <a href="/app_release/delete_channel?id=<?=$row['id']?>" class="btn btn-danger btn-xs btn-delete">删除</a>
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
        $('.btn-delete').click(function () {
            return confirm('确认删除？');
        });
    });
</script>

</body>
</html>
