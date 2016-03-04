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
                        <h2>短信使用分布</h2>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                    </header>
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
                                            <div class="form-group">
                                                <label class="label">
                                                    <input type="text" class="form-control datepicker"
                                                           data-dateformat="yy-mm-dd" id="start_time" name="start_time"
                                                           value="<?php
                                                           if (empty($_GET['start_time'])){
                                                               echo date("Y-m-d",time()-86400);
                                                           } else {
                                                               echo $_GET['start_time'];
                                                           }
                                                           ?>"
                                                        />
                                                </label>
                                                <label class="label">
                                                    <input type="text" class="form-control datepicker"
                                                           data-dateformat="yy-mm-dd" id="end_time" name="end_time"
                                                           value="<?php
                                                           if (empty($_GET['end_time'])){
                                                               echo date("Y-m-d",time());
                                                           } else {
                                                               echo $_GET['end_time'];
                                                           }
                                                           ?>"
                                                        />
                                                </label>
                                            </div>
                                            <div class='form-group'>
                                                <button type="submit" class="form-control btn-success input-sm">查询
                                                </button> 最多选择3天跨度
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-bordered table-hover dataTables_wrapper">
                                <tr>
                                    <td><div id="chart1" style="height:400px;"></div></td>
                                </tr>
                            </table>
                            <table class="table table-bordered table-hover dataTables_wrapper">
                                <tr>
                                    <td><div id="chart2" style="height:400px;"></div></td>
                                </tr>
                            </table>
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
    var time = <?=json_encode($time, JSON_NUMERIC_CHECK)?>;
    var sms = <?=json_encode($sms, JSON_NUMERIC_CHECK)?>;
    var captcha = <?=json_encode($captcha, JSON_NUMERIC_CHECK)?>;
    var channel = <?=json_encode($channel, JSON_NUMERIC_CHECK)?>;

    $(function () {

        require(['echarts', 'echarts/chart/bar'],
            function (ec) {
                var myChart1 = ec.init(document.getElementById('chart1'));
                var myChart2 = ec.init(document.getElementById('chart2'));
                var option1 = {
                    title: {
                        text: '短信使用报告',
                        x: 'left'
                    },
                    legend: {
                        data: []
                    },
                    toolbox: {
                        show : true,
                        feature : {
                            mark : {show: true},
                            dataView : {show: true, readOnly: false},
                            magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                            restore : {show: true},
                            saveAsImage : {show: true}
                        }
                    },
                    tooltip : {
                        trigger: 'axis'
                    },
                    calculable: true,
                    yAxis : [
                        {
                            type : 'value'
                        }
                    ],
                    xAxis : [
                        {
                            type : 'category',
                            data : time
                        }
                    ],
                    series: [
                    ]
                };
                var option2 = {
                    title: {
                        text: '验证码使用报告',
                        x: 'left'
                    },
                    legend: {
                        data: []
                    },
                    toolbox: {
                        show : true,
                        feature : {
                            mark : {show: true},
                            dataView : {show: true, readOnly: false},
                            magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                            restore : {show: true},
                            saveAsImage : {show: true}
                        }
                    },
                    tooltip : {
                        trigger: 'axis'
                    },
                    calculable: true,
                    yAxis : [
                        {
                            type : 'value'
                        }
                    ],
                    xAxis : [
                        {
                            type : 'category',
                            data : time
                        }
                    ],
                    series: [
                    ]
                };

                //通道初始化
                for (var i in channel) {
                    var name = channel[i];
                    var name1 = channel[i]+"/成功";
                    var init = {
                        name : name,
                        type: 'bar',
                        data: []
                    };
                    var init_1 = {
                        name : name1,
                        type: 'bar',
                        data: []
                    };

                    option1.series.push(init);
                    option1.series.push(init_1);
                    option1.legend.data.push(name);
                    option1.legend.data.push(name1);
                }

                for(var i=0;i<option1.xAxis[0].data.length;i++){
                    var day = option1.xAxis[0].data[i];
                    if (sms[day] != undefined) {
                        for (var j in channel) //
                        {
                            var index = j*2-2;//渠道对应数据数组
                            if (sms[day][j] != undefined) {
                                var number = sms[day][j].count;
                                option1.series[index].data.push(sms[day][j].count);
                            } else {
                                option1.series[index].data.push(0);
                            }
                            var index = j*2-1;//渠道对应数据数组
                            if (sms[day][j] != undefined) {
                                if (number && sms[day][j].success) {
                                    var rate = number_format((sms[day][j].success/number)*100,2);
                                    var new_name = option1.series[index].name+rate+"%";
                                    option1.series[index].name = new_name;
                                    option1.legend.data[index] = new_name;
                                }
                                option1.series[index].data.push(sms[day][j].success);
                            } else {
                                option1.series[index].data.push(0);
                            }
                        }
                    } else {
                        for (var j in option1.series)
                        {
                            option1.series[j].data.push(0);
                        }
                    }
                }
                myChart1.setOption(option1);
                for (var i in channel) {
                    var name = channel[i];
                    var name2 = channel[i]+"/使用";
                    var init = {
                        name : name,
                        type: 'bar',
                        data: []
                    };
                    var init_2 = {
                        name : name2,
                        type: 'bar',
                        data: []
                    };
                    option2.series.push(init);
                    option2.series.push(init_2);
                    option2.legend.data.push(name);
                    option2.legend.data.push(name2);
                }

                for(var i=0;i<option2.xAxis[0].data.length;i++){
                    var day = option2.xAxis[0].data[i];
                    if (captcha[day]) {
                        for (var j in channel) //
                        {
                            var index = j*2-2;//渠道对应数据数组
                            if (captcha[day][j]) {
                                var number1 = captcha[day][j].count;
                                option2.series[index].data.push(captcha[day][j].count);
                            } else {
                                option2.series[index].data.push(0);
                            }
                            var index = j*2-1;//渠道对应数据数组
                            if (captcha[day][j]) {
                                if (number1 && sms[day][j].success) {
                                    var rate = number_format((captcha[day][j].is_used/number1)*100,2);
                                    var new_name = option2.series[index].name+rate+"%";
                                    option2.series[index].name = new_name;
                                    option2.legend.data[index] = new_name;
                                }
                                option2.series[index].data.push(captcha[day][j].is_used);
                            } else {
                                option2.series[index].data.push(0);
                            }
                        }
                    } else {

                        for (var j in option2.series)
                        {
                            option2.series[j].data.push(0);
                        }
                    }
                }
                myChart2.setOption(option2);

            });
    });
</script>

</body>
</html>
