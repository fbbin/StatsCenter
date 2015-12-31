<?php include __DIR__.'/../include/header.php'; ?>

<!-- MAIN PANEL -->
<div id="main" role="main">

<!-- RIBBON -->
<div id="ribbon">
    <!-- breadcrumb -->
    <ol class="breadcrumb">
        <li><a href="/">首页</a></li>
        <li><a href="/app_host/project_list">APP项目列表</a></li>
        <li><?=filter_value($page_title, true)?></li>
    </ol>

</div>

<div id="content">
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
            <div class="jarviswidget jarviswidget-color-darken jarviswidget-sortable" id="wid-id-0"
                 data-widget-editbutton="false" role="widget" style="">
                <header role="heading">
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2><?=filter_value($page_title)?></h2>
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
                                                    <?=filter_value($row['name'], true)?>
                                                </div>
                                                <div class="col-md-8 text-align-right">
                                                    <div class="btn-group">
                                                        <a href="/app_host/edit_hosts?id=<?=intval($row['id'])?>" class="btn btn-info btn-xs">
                                                            <i class="fa fa-pencil"></i> 编辑
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </h3>
                                    </div>
                                    <div class="panel-body no-padding">
                                        <table class="table table-bordered table-hover table-condensed">
                                            <thead>
                                                <tr>
                                                    <th>环境名称</th>
                                                    <th>环境标识符</th>
                                                    <th>接口地址</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($row['host_list'] as $env) : ?>
                                                    <tr>
                                                        <td><?=filter_value($env['env_name'], true)?></td>
                                                        <td><?=filter_value($env['env_id'], true)?></td>
                                                        <td><?=filter_value($env['host'], true)?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <div class="dt-row dt-bottom-row">
                                    <div class="pager">
                                        <span>总计：<strong><?=$pager->total?></strong>条</span>
                                    </div>
                                    <?=$pager->render()?>
                                </div>
                            </div>
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
