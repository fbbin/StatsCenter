<?php include __DIR__.'/../include/header.php'; ?>
<!-- END NAVIGATION -->

<!-- MAIN PANEL -->
<div id="main" role="main">

    <style>
        .order {
            cursor: pointer;
        }
        .order_none {
            background: url("/static/smartadmin/img/sort_both.png") no-repeat center right;
        }
        .order_desc {
            background: url("/static/smartadmin/img/sort_desc.png") no-repeat center right;
        }
        .order_asc {
            background: url("/static/smartadmin/img/sort_asc.png") no-repeat center right;
        }
    </style>

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
                     data-widget-editbutton="false" role="widget" style="">
                    <header role="heading">
                        <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                        <h2>验证码统计</h2>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>
                    <div role="content">

                        <div class="jarviswidget-editbox">

                        </div>

                        <div class="widget-body no-padding">
                            <div class="widget-body-toolbar" style="height: 40px;">

                            </div>

                            <div id="dt_basic_wrapper" class="dataTables_wrapper form-inline" role="grid">
                                <div class="dt-top-row">
                                    <div class="dataTables_filter" style="margin-left: 5px;">
                                        <form id="form" class="form-inline" novalidate="novalidate" method="get">
                                            <div class="form-group" style="width: 300px;">
                                                <select class="select2" id="type" name="type">
                                                    <?php foreach ($type as $k => $v): ?>
                                                        <option value="<?= $k ?>"
                                                            <?php if ($k == $_GET['type']) echo 'selected="selected"'; ?> ><?= $v ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="label">
                                                    <input type="text" class="form-control datepicker"
                                                           data-dateformat="yy-mm-dd" id="start_time" name="start_time"
                                                           value="<?php
                                                           if (empty($_GET['start_time'])){
                                                               echo date("Y-m-d");
                                                           } else {
                                                               echo $_GET['start_time'];
                                                           }
                                                           ?>"
                                                        />
                                                </label>
<!--                                                <label class="label">-->
<!--                                                    <input type="text" class="form-control datepicker"-->
<!--                                                           data-dateformat="yy-mm-dd" id="end_time" name="end_time"-->
<!--                                                           value="--><?php
//                                                           if (empty($_POST['end_time'])){
//                                                               echo date("Y-m-d",time());
//                                                           } else {
//                                                               echo $_POST['end_time'];
//                                                           }
//                                                           ?><!--"-->
<!--                                                        />-->
<!--                                                </label>-->
                                            </div>
                                            <div class='form-group'>
                                                <button type="submit" class="form-control btn-success input-sm">搜索
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-bordered table-hover dataTables_wrapper">
                                <thead>
                                <tr>
                                    <th>通道名称</th>
                                    <th>消息类型</th>
                                    <th>发送次数</th>
                                    <th>使用</th>
                                    <th>使用率</th>
                                </tr>
                                </thead>
                                <tbody id="data_table_body">
                                    <?php
                                    foreach ($data as $k=>$d)
                                    {
                                        ?>
                                        <tr>
                                            <td><?= $d['name'] ?></td>
                                            <td><?= $d['type'] ?></td>
                                            <td><?=(int)$d['count'] ?></td>
                                            <td><?=(int)$d['used']?></td>
                                            <td><?=$d['used_rate']?>%</td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="dt-row dt-bottom-row">
                            <div class="pager">
                                <span>汇总：发送<?=(int)$all['count']?>条 使用<?=(int)$all['used']?>条 使用率<?=$all['used_rate']?>%
                                </span>
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
<?php include dirname(__DIR__).'/include/javascript.php'; ?>
<script>
    pageSetUp();
</script>

</body>
</html>
