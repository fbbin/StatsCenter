<?php include __DIR__.'/../include/header.php'; ?>

<!-- MAIN PANEL -->
<div id="main" role="main">

<!-- RIBBON -->
<div id="ribbon">
    <!-- breadcrumb -->
    <ol class="breadcrumb">
        <li>Home</li>
        <li>APP管理</li>
    </ol>

</div>

<div id="content">
        <!-- row -->
        <div class="row">

            <!-- NEW WIDGET START -->
            <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">

                <div class="jarviswidget jarviswidget-color-darken jarviswidget-sortable" id="wid-id-0"
                     data-widget-editbutton="false" role="widget" style="">
                    <header role="heading">
                        <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                        <h2>APP管理</h2>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>
                    <div role="content">

                        <div class="jarviswidget-editbox">

                        </div>
                        <div class="widget-body no-padding">
                            <div class="widget-body-toolbar">
                                <div class="row">
                                    <div class="col-md-8">
                                        <form id="form" class="form-inline" novalidate="novalidate" method="post">
                                            <div class="form-group">
                                                <?=$form['name']?>
                                            </div>
                                            <div class='form-group'>
                                                <button type="submit" class="form-control btn-success input-sm">搜索
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="/setting/add_app" class="btn btn-primary pull-right">
                                            <i class="fa fa-plus"></i> 添加APP
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <table id="data_table_stats" class="table table-bordered table-">
                                <thead>
                                    <tr>
                                        <th style="width: 50px">序列</th>
                                        <th>ID</th>
                                        <th>APP</th>
                                        <th>APP_KEY</th>
                                        <th>OS</th>
                                        <th>状态</th>
                                        <th>是否初始化</th>
                                        <th>过期时间</th>
                                        <th>创建时间</th>
                                        <th>更新时间</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody id="data_table_body">
                                    <?php
                                    foreach ($data as $k=>$d)
                                    {
                                    ?>
                                        <tr>
                                            <td><?= $k + 1 ?></td>
                                            <td><?= $d['id'] ?></td>
                                            <td><?= $d['name'] ?></td>
                                            <td><?= $d['app_key'] ?></td>
                                            <td><?= $d['os_name'] ?></td>
                                            <td
                                                <?php
                                                if ($d['enable'] == 1)
                                                {
                                                    echo 'style="color:green"';
                                                } else {
                                                    echo 'style="color:red"';
                                                }
                                                ?>
                                            ><?= $d['enable_name'] ?></td>
                                            <td
                                                <?php
                                                if ($d['has_init'] == 1)
                                                {
                                                    echo 'style="color:green"';
                                                } else {
                                                    echo 'style="color:red"';
                                                }
                                                ?>
                                            ><?=$d['has_init_name']?></td>
                                            <td><?=array_get($d, 'expire_time')?></td>
                                            <td><?=$d['create_time']?></td>
                                            <td><?=$d['update_time']?></td>
                                            <td>
                                                <a href="/project/edit/?id=<?=$d['id']?>">编辑</a>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <div class="dt-row dt-bottom-row">
                                <div class="pager"> <span>总计：<strong>
                                            <?php echo $pager['total'];?>
                                        </strong> 条</span></div>
                                <?php echo $pager['render'];?>
                            </div>
                            </div>

                        </div>
                        <!-- end widget content -->

                    </div>
                    <!-- end widget div -->

                </div>
                <div class="jarviswidget jarviswidget-color-blueDark jarviswidget-sortable" id="wid-id-1"
                     data-widget-editbutton="false" role="widget" style="">


                </div>
                <div class="jarviswidget jarviswidget-color-blueDark jarviswidget-sortable" id="wid-id-2"
                     data-widget-editbutton="false" role="widget" style="">

                </div>
                <div class="jarviswidget jarviswidget-color-blueDark jarviswidget-sortable" id="wid-id-3"
                     data-widget-editbutton="false" role="widget" style="">

                </div>
            </article>
            <!-- WIDGET END -->
        </div>
</div>
<!-- END MAIN CONTENT -->

</div>
<!-- END MAIN PANEL -->

<!-- SHORTCUT AREA : With large tiles (activated via clicking user name tag)
Note: These tiles are completely responsive,
you can add as many as you like
-->
<div id="shortcut">
    <ul>
        <li>
            <a href="#inbox.html" class="jarvismetro-tile big-cubes bg-color-blue"> <span class="iconbox"> <i
                        class="fa fa-envelope fa-4x"></i> <span>Mail <span
                            class="label pull-right bg-color-darken">14</span></span> </span> </a>
        </li>
        <li>
            <a href="#calendar.html" class="jarvismetro-tile big-cubes bg-color-orangeDark"> <span class="iconbox"> <i
                        class="fa fa-calendar fa-4x"></i> <span>Calendar</span> </span> </a>
        </li>
        <li>
            <a href="#gmap-xml.html" class="jarvismetro-tile big-cubes bg-color-purple"> <span class="iconbox"> <i
                        class="fa fa-map-marker fa-4x"></i> <span>Maps</span> </span> </a>
        </li>
        <li>
            <a href="#invoice.html" class="jarvismetro-tile big-cubes bg-color-blueDark"> <span class="iconbox"> <i
                        class="fa fa-book fa-4x"></i> <span>Invoice <span
                            class="label pull-right bg-color-darken">99</span></span> </span> </a>
        </li>
        <li>
            <a href="#gallery.html" class="jarvismetro-tile big-cubes bg-color-greenLight"> <span class="iconbox"> <i
                        class="fa fa-picture-o fa-4x"></i> <span>Gallery </span> </span> </a>
        </li>
        <li>
            <a href="javascript:void(0);" class="jarvismetro-tile big-cubes selected bg-color-pinkDark"> <span
                    class="iconbox"> <i class="fa fa-user fa-4x"></i> <span>My Profile </span> </span> </a>
        </li>
    </ul>
</div>
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
