<!DOCTYPE html>
<html lang="en-us">
<head>
	<meta charset="utf-8">
	<title><?= Swoole::$php->config['common']['site_name'] ?></title>
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<?php include __DIR__ . '/../include/css.php'; ?>
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
		<div class="row">
			<article class="col-sm-12 sortable-grid ui-sortable">
				<!-- new widget -->

				<!-- end widget -->

				<div class="jarviswidget jarviswidget-sortable" id="wid-id-0" data-widget-togglebutton="false"
				     data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-colorbutton="false"
				     data-widget-deletebutton="false" role="widget" style="">
					<header role="heading">
						<ul class="nav nav-tabs pull-left in">
							<li class="active">
								<a data-toggle="tab" href="#s1"><i class="fa fa-clock-o"></i>
									<span class="hidden-mobile hidden-tablet">错误分布</span>
								</a>
							</li>
							<li>
								<a data-toggle="tab" href="#s2"><i class="fa fa-bell"></i>
									<span class="hidden-mobile hidden-tablet">详细信息</span></a>
							</li>
						</ul>
						<span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
					</header>

					<!-- widget div-->
					<div class="no-padding" role="content">
						<div class="widget-body" class="tab_content">
							<div>
								<div class="tab-pane fade active in padding-10 no-padding-bottom" id="s1">
									<table width="100%">
										<tr>
											<td>
												<div id="chart1" style="height:400px;"></div>
											</td>
											<!--                                        <td><div id="chart2" style="height:400px; width: 600px;"></div></td>-->
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</article>
		</div>
	</div>

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
				<a href="#gallery.html" class="jarvismetro-tile big-cubes bg-color-greenLight"> <span
						class="iconbox"> <i
							class="fa fa-picture-o fa-4x"></i> <span>Gallery </span> </span> </a>
			</li>
			<li>
				<a href="javascript:void(0);" class="jarvismetro-tile big-cubes selected bg-color-pinkDark"> <span
						class="iconbox"> <i class="fa fa-user fa-4x"></i> <span>My Profile </span> </span> </a>
			</li>
		</ul>
	</div>
	<?php include dirname(__DIR__) . '/include/javascript.php'; ?>
	<script src="<?= WEBROOT ?>/apps/static/js/stats.js" type="text/javascript"></script>
	<script src="<?= WEBROOT ?>/apps/static/js/list.js" type="text/javascript"></script>
	<script>
		var ret_code = <?=json_encode($ret_code, JSON_NUMERIC_CHECK)?>;
		var fail_server = <?=json_encode([], JSON_NUMERIC_CHECK)?>;
		var ret_code_result = {};
		var fail_server_result = {};
		var i;
		for (i = 0; i < ret_code.length; i++) {
			for (var o in ret_code[i]) {
				if (!ret_code_result[o]) {
					ret_code_result[o] = 0;
				}
				ret_code_result[o] += ret_code[i][o]
			}
		}

		$(function () {
			pageSetUp();

			require(['echarts', 'echarts/chart/pie'],
				function (ec) {
					var myChart1 = ec.init(document.getElementById('chart1'));
					var option1 = {
						title: {
							text: '错误码分布',
							x: 'center'
						},
						legend: {
							orient: 'vertical',
							x: 'left',
							data: []
						},
						calculable: true,
						series: [
							{
								name: '错误数量',
								type: 'pie',
								minAngle: 10,
								radius: '55%',
								center: ['50%', 225],
								data: []
							}
						]
					};
					var show, o;
					for (o in ret_code_result) {
						var code = o > Math.pow(2, 31) ? o - Math.pow(2, 32) : o;
						show = code + ' (' + ret_code_result[o] + '次)';
						option1.legend.data.push(show);
						option1.series[0].data.push({value: ret_code_result[o], name: show})
					}
					myChart1.setOption(option1);
				});
		});
	</script>
</body>
</html>
