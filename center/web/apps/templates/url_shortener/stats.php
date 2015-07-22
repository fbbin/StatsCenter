<?php include __DIR__.'/../include/header.php'; ?>
<!-- END NAVIGATION -->

<!-- MAIN PANEL -->
<div id="main" role="main">

<!-- RIBBON -->
<div id="ribbon">

    <span class="ribbon-button-alignment">
        <span id="refresh" class="btn btn-ribbon" data-title="refresh" rel="tooltip"
                                                 data-placement="bottom"
                                                 data-original-title="<i class='text-warning fa fa-warning'></i> Warning! This will reset all your widget settings."
                                                 data-html="true"><i class="fa fa-refresh"></i></span> </span>

    <!-- breadcrumb -->
    <ol class="breadcrumb">
        <li>Home</li>
        <li>Dashboard</li>
    </ol>

</div>

<div id="content">
        <!-- row -->
        <div class="row">

            <!-- NEW WIDGET START -->
            <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-color-darken jarviswidget-sortable" id="wid-id-0"
                     data-widget-editbutton="false" role="widget" style="width:500px;">
                    <header role="heading">
                        <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                        <h2>查看统计</h2>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>
                    <div role="content">
                        <div id="delete_tip">
                        </div>
                        <div class="jarviswidget-editbox">

                        </div>

                        <div class="widget-body">
                            <p>短网址：<a href="<?=$tiny_url?>"><?=$tiny_url?></a></p>

                            <p><a href="/url_shortener/stats?id=<?=$tiny_url_id?>&format=csv">导出 CSV 格式</a>（可以用 Excel、WPS表格 打开）</p>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>日期</th>
                                        <th>访问次数</th>
                                    </tr>
                                </thead>
                                <tbody id="data_table_body">
                                    <?php
                                    foreach ($data as $d)
                                    {
                                    ?>
                                        <tr height="32">
                                            <td><?=$d['date']?></td>
                                            <td><?=$d['visits']?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="pager-box">
                            <div class="pager">
                                <?php if ($has_prev) : ?>
                                    <a href="/url_shortener/stats?id=<?=$tiny_url_id?>&from=<?=$prev_from_date_str?>" class="">上一页</a>
                                <?php endif; ?>
                                <?php if ($has_next) : ?>
                                    <a href="/url_shortener/stats?id=<?=$tiny_url_id?>&from=<?=$next_from_date_str?>" class="">下一页</a>
                                <?php endif; ?>
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
<?php include dirname(__DIR__).'/include/javascript.php'; ?>
<script>
    $(function() {
        pageSetUp();
//        ListsG.getListsData();
        $("#submit").click(function(){
            $("#form").submit();
        });
    });
</script>

</body>
</html>
