<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title><?=Swoole::$php->config['common']['site_name']?></title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?php include __DIR__.'/../include/css.php'; ?>
</head>
<body class="">
<header style="background: #E4E4E4;color: #22201F" id="header">
    <?php include __DIR__ . '/../include/top_menu.php'; ?>
</header>
<aside id="left-panel">
    <!--            --><?php //include __DIR__.'/../include/login_info.php'; ?>
    <?php include __DIR__ . '/../include/leftmenu.php'; ?>
    <span class="minifyme"> <i class="fa fa-arrow-circle-left hit"></i> </span>
</aside>
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
                        <h2>接口调用统计（仅保存1个月内数据）</h2>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                        <?php if (!empty($_GET['module_id'])){?>
                        <div class="widget-toolbar">
                            <div class="btn-group">
                                <button class="btn dropdown-toggle btn-xs btn-primary" data-toggle="dropdown">
                                    设置 <i class="fa fa-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                    <li>
                                        <a href="/setting/add_module/?id=<?=$_GET['module_id']?>">模块设置</a>
                                    </li>
                                    <li>
                                        <a href="/setting/interface_list/?module_id=<?=$_GET['module_id']?>">接口列表</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php } ?>
                    </header>
                    <div role="content">

                        <div class="jarviswidget-editbox">

                        </div>

                        <div class="widget-body no-padding">
                            <div class="widget-body-toolbar" style="height: 40px;">

                            </div>

                            <div id="dt_basic_wrapper" class="dataTables_wrapper form-inline" role="grid">
                                <div class="dt-top-row">
                                    <div class="dataTables_filter">
                                        <div class="form-group inline-group" style="width: 260px;">
                                            <form class="smart-form inline-group" novalidate="novalidate" onsubmit="return filterInterfaceName()" method="get">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <label class="input">
                                                            <input type="text" name="interface_name" id="interface_name"
                                                                   value="<?= $this->value($_GET, 'interface_name') ?>"
                                                                   placeholder="接口名称">
                                                    </div>
                                                </div>
                                                <div class='form-group'>
                                                    <button type="submit" id='submit' class='btn btn-default'
                                                            style='padding:6px 12px'>过滤
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="form-group inline-group" style="width: 300px;">
                                            <select class="select2" id="module_id">
                                                <option value="">所有模块</option>
                                                <?php foreach ($modules as $m): ?>
                                                    <option value="<?= $m['id'] ?>: <?= $m['name'] ?>"
                                                        <?php if ($m['id'] == $_GET['module_id']) echo 'selected="selected"'; ?> ><?= $m['id'] ?>
                                                        : <?= $m['name'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width: 500px;">
                                            <select id="interface_id" class="select2">
                                                <option value="">所有接口</option>
                                                <?php foreach ($interfaces as $m): ?>
                                                    <option value="<?= $m['id'] ?>: <?= (empty($m['alias']) ? $m['name'] : $m['alias']) ?>"
                                                        <?php if ($m['id'] == $_GET['interface_id']) echo 'selected="selected"'; ?> >
                                                        <?= $m['id'] ?>: <?= (empty($m['alias']) ? $m['name'] : $m['alias']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="label">
                                                <input type="text" class="form-control datepicker"
                                                       data-dateformat="yy-mm-dd" id="data_key"
                                                       value="<?= $_GET['date_key'] ?>"
                                                    />
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <a class="btn btn-sm btn-success" id="btn_last_hour">最近一小时实时数据</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-bordered table-hover dataTables_wrapper">
                                <thead>
                                <tr>
                                    <th style="width: 300px;">域名</th>
                                    <th style="width: 80px;">接口</th>
                                    <th style="width: 70px;" class="order" data-value="total_count">调用次数</th>
                                    <th style="width: 70px;" class="order" data-value="succ_count">成功次数</th>
                                    <th style="width: 70px;" class="order" data-value="fail_count">失败次数</th>
                                    <th style="width: 70px;" class="order" data-value="succ_rate">成功率</th>
                                    <th style="width: 70px;">耗时最大值</th>
                                    <th style="width: 70px;">耗时最小值</th>
                                    <th style="width: 90px;" class="order" data-value="avg_time">平均耗时</th>
                                    <th style="width: 90px;" class="order" data-value="avg_fail_time">失败平均耗时</th>
                                    <th style="width: 260px;">操作</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php foreach ($data as $td):
	                                $bg_color = $td['succ_rate'] > 90 ? "#DFFFDF" : "#FFDFDF";
	                                ?>
	                                <tr style="background-color: <?=$bg_color?>;">
		                                <td><?= $map[$td['host_id']] ?></td>
		                                <td><?= $map[$td['uri_id']] ?></td>
		                                <td><?= number_format($td['t_count']) ?></td>
		                                <?php if (isset($td['time_key']) and !empty($td['time_key'])) {?>
			                                <td><a href="javascript: StatsG.openSuccPage(<?=$td['module_id']?>,<?=$td['interface_id']?>,<?=$td['time_key']?>)" style="color: green">
					                                <?= number_format($td['succ_count']) ?></a></td>
			                                <td><a href="javascript: StatsG.openFailPage(<?=$td['module_id']?>,<?=$td['interface_id']?>,<?=$td['time_key']?>)"
			                                       style="color: <?=$td['fail_count'] > 0? "red" :'black'?>"><?= number_format($td['fail_count']) ?></a></td>
		                                <?php } else { ?>
			                                <td><a href="javascript: StatsG.openSuccPage(<?=$td['module_id']?>,<?=$td['interface_id']?>)" style="color: green">
					                                <?= number_format($td['succ_count']) ?></a></td>
			                                <td><a href="javascript: StatsG.openFailPage(<?=$td['module_id']?>,<?=$td['interface_id']?>)"
			                                       style="color: <?=$td['fail_count'] > 0? "red" :'black'?>"><?= number_format($td['fail_count']) ?></a></td>
		                                <?php } ?>

		                                <td style="color: green"><?= $td['succ_rate'] ?>%</td>
		                                <td><?= $td['time_max'] ?>ms</td>
		                                <td><?= $td['time_min'] ?>ms</td>
		                                <td>ms</td>
		                                <td><?= $td['avg_fail_time'] ?>ms</td>
		                                <td>
			                                <?php if (!$this->isActiveMenu('stats', 'detail')):?><a href="/stats/detail/?module_id=<?= $td['module_id'] ?>&interface_id=<?= $td['interface_id'] ?>&date_key=<?= $_GET['date_key'] ?>"">查看明细</a>
            &nbsp;&nbsp;|&nbsp;&nbsp; <?php endif; ?>
			                                <?php if (isset($td['time_key']) and !empty($td['time_key'])) {?>
				                                <a href="/stats/history/?module_id=<?= $td['module_id'] ?>&interface_id=<?= $td['interface_id'] ?>&date_key=<?= $_GET['date_key'] ?>">历史数据对比</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				                                <a href="/stats/client/?module_id=<?= $td['module_id'] ?>&interface_id=<?= $td['interface_id'] ?>&date_key=<?= $_GET['date_key'] ?>&time_key=<?= $td['time_key']?>">主调明细</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				                                <a href="/stats/server/?module_id=<?= $td['module_id'] ?>&interface_id=<?= $td['interface_id'] ?>&date_key=<?= $_GET['date_key'] ?>&time_key=<?= $td['time_key']?>">被调明细</a>
			                                <?php } else { ?>
				                                <a href="/stats/history/?module_id=<?= $td['module_id'] ?>&interface_id=<?= $td['interface_id'] ?>&date_key=<?= $_GET['date_key'] ?>">历史数据对比</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				                                <a href="/stats/client/?module_id=<?= $td['module_id'] ?>&interface_id=<?= $td['interface_id'] ?>&date_key=<?= $_GET['date_key'] ?>">主调明细</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				                                <a href="/stats/server/?module_id=<?= $td['module_id'] ?>&interface_id=<?= $td['interface_id'] ?>&date_key=<?= $_GET['date_key'] ?>">被调明细</a>
			                                <?php } ?>
		                                </td>
	                                </tr>
                                <?php endforeach; ?>

                                </tbody>
                            </table>
                        </div>
                        <div class="dt-row dt-bottom-row">
                            <div class="row">
                                <div class="col-sm-2 text-left">
                                    <div class="pager"><span>共有 <?=$total?> 个接口</span></div>
                                </div>
                                <div class="col-sm-10 text-left">
                                    <?=$pager?>
                                </div>
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
<script src="<?=WEBROOT?>/apps/static/js/stats.js" type="text/javascript"></script>
<script src="<?=WEBROOT?>/apps/static/js/list.js" type="text/javascript"></script>
<script>
    StatsG.filter.hour_start = 0;
    StatsG.filter.hour_end = 23;

    var TableOrder = {
        "desc": <?=empty($_GET['desc'])?0:1?>,
        "orderby": "<?=empty($_GET['orderby'])?'':$_GET['orderby']?>"
    };

    function filterInterfaceName() {
        delete StatsG.filter.page;
        delete StatsG.filter.interface_id;
        StatsG.filter.interface_name = $('#interface_name').val();
        StatsG.go();
        return false;
    }

    $(function() {
        $('.order').click(function (e) {
            var o = $(e.currentTarget);
            var orderby = o.attr('data-value');
            if (orderby != TableOrder.orderby) {
                StatsG.filter.orderby = orderby;
                StatsG.filter.desc = 1;
            } else {
                StatsG.filter.desc = 1 - TableOrder.desc;
            }
            delete StatsG.filter.page;
            StatsG.go();
        });

        $('.order').each(function(_o, e){
            var o = $(e);
            if (o.attr('data-value') == TableOrder.orderby) {
                if (TableOrder.desc) {
                    o.addClass('order_desc');
                } else {
                    o.addClass('order_asc');
                }
            } else {
                o.addClass('order_none');
            }
        });

        pageSetUp();
        StatsG.filter = <?php echo json_encode($_GET);?>;
        $("#datepicker").datepicker("option", $.datepicker.regional[ 'zh-CN' ]);

        $("#module_id").change(function (e) {
            var module_id = e.currentTarget.value.split(':')[0];
            window.localStorage.module_id = module_id;
            delete StatsG.filter.page;
            StatsG.filter.module_id = window.localStorage.module_id;
            StatsG.go();
        });
        $("#data_key").change(function(){
            window.localStorage.date_key = $(this).val();
            delete StatsG.filter.page;
            StatsG.filter.date_key = window.localStorage.date_key;
            StatsG.go();
        });
        $("#interface_id").change(function (e) {
            StatsG.filter.interface_id = e.currentTarget.value.split(':')[0];
            delete StatsG.filter.page;
            StatsG.go();
        });
        $('#btn_last_hour').click(function (e){
            location.href = '/stats/last_hour/?module_id=' + StatsG.filter.module_id;
        });
    });
</script>

</body>
</html>
