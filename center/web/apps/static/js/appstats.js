/**
 * Created by htf on 14-4-29.
 */
var StatsG = {
    url: '/appstats/data/',
    page_url: '/appstats/index/',
    history_url : '/appstats/history_data/',
    filter: {
        hour_start: 0,
        hour_end: 23
    },
    stats: {},
    interface_name: {},
    interface_stats: {},
    config: {
        'green_rate': 80,
        'data_table': {
            "sPaginationType": "bootstrap_full",
            "iDisplayLength": 25,
            "bAutoWidth": false,
            "stateSave": true,
            "oLanguage": {
                "sInfo": "总计：_TOTAL_ ，当前：_START_ 到 _END_",
                "oPaginate": {
                    "sFirst": "首页",
                    "sPrevious": "前一页",
                    "sNext": "后一页",
                    "sLast": "尾页"
                }
            }
        }
    }
};

var history_chart_option = {
    calculable : true,
    xAxis : [
        {
            type : 'category',
            boundaryGap : false,
            data : []
        }
    ],
    yAxis : [
        {
            type : 'value',
            splitArea : {show : true}
        }
    ],
    series : [
        {
            name:'最高气温',
            type:'line',
            itemStyle: {
                borderWidth : 1,
                borderRadius : 2,
                normal: {
                    lineStyle: {
                        shadowColor : 'rgba(0,0,0,0.4)',
                        shadowBlur: 5,
                        shadowOffsetX: 3,
                        shadowOffsetY: 3
                    }
                }
            },
            data:[]
        },
        {
            name:'最低气温',
            type:'line',
            itemStyle: {
                borderWidth : 1,
                borderRadius : 2,
                normal: {
                    lineStyle: {
                        shadowColor : 'rgba(0,0,0,0.4)',
                        shadowBlur: 5,
                        shadowOffsetX: 3,
                        shadowOffsetY: 3
                    }
                }
            },
            data:[1, -2, 2, 5, 3, 2, 0]
        }
    ]
};

StatsG.filterByHour = function (reload) {
    StatsG.filter.hour_start = parseInt($('#filter_hour_s').val(), 10);
    StatsG.filter.hour_end = parseInt($('#filter_hour_e').val(), 10);
    window.localStorage.hour_start = StatsG.filter.hour_start;
    window.localStorage.hour_end = StatsG.filter.hour_end;
    if (reload) {
        StatsG.go();
    } else {
        StatsG.refresh(StatsG.filter.interface_id);
    }
};

StatsG.refresh = {};

function paserHistoryData(data) {
    var ret = {};
    for(var i=0; i< data.length; i++) {
        ret[data[i].index] = data[i];
    }
    return ret;
}

StatsG.showHistoryData = function () {
    var filter =  StatsG.filter;
    filter.date_start = $('#history_date_start').val();
    filter.date_end = $('#history_date_end').val();
    StatsG.refresh = StatsG.showHistoryData;

    $.ajax({
        url: StatsG.history_url,
        dataType : 'json',
        data: filter,
        success: function(data) {
            var data1 = paserHistoryData(data.data1);
            var data2 = paserHistoryData(data.data2);

            require(['echarts', 'echarts/chart/bar'], function(ec) {
                history_chart_option.xAxis[0].data = [];
                history_chart_option.series[0].data = [];
                history_chart_option.series[1].data = [];

                var time_start = StatsG.filter.hour_start * 12;
                var time_end = (StatsG.filter.hour_end + 1) * 12;
                var myChart1 = ec.init(document.getElementById('history_chart1'));
                $('#history_table').html('');
                for(var i = time_start; i< time_end; i++) {
                    history_chart_option.xAxis[0].data.push(getTimerStr(i));
                    if (data1[i]) {
                        history_chart_option.series[0].data.push(data1[i].count_all);
                    } else {
                        history_chart_option.series[0].data.push(0);
                    }

                    if (data2[i]) {
                        history_chart_option.series[1].data.push(data2[i].count_all);
                    } else {
                        history_chart_option.series[1].data.push(0);
                    }
                    var _data1 = {}, _data2 = {};
                    if (data1[i]) {
                        _data1 = data1[i];
                    }
                    if (data2[i]) {
                        _data2 = data2[i];
                    }
//                    if (_data1[i])
                    StatsG.appendToHistoryTable(_data1, _data2);
                }
                myChart1.setOption(history_chart_option);
            });
        }
    });
};

StatsG.go = function () {
    var url = StatsG.page_url + '?';
    for (var o in StatsG.filter) {
        url += o + '=' + StatsG.filter[o] + '&';
    }
    location.href = url;
};

StatsG.appendToHistoryTable = function (_data1, _data2) {
    var tr_color; //green, normal
    if (!_data1['count_all'] && !_data2['count_all']) {
        return;
    } else {
        //console.dir(_data1);
        //console.dir(_data2);
    }
    var line;
    var fail_rate, avg_fail_time, avg_time, td_color;
    line = "<tr height='32'>";
    //时间
    var time_key = _data1.ctime ? _data1.ctime : _data2.ctime;
    var date_key;
    for(var i = 0; i < 2; i++) {
        if (i == 0) {
            d = _data1;
            date_key = StatsG.filter.date_start;
            line += '<td width="100">' + StatsG.filter.date_start + ' '+ getHM(time_key) + '</td>';
        } else {
            d = _data2;
            date_key = StatsG.filter.date_end;
            line += '<td width="100">' + StatsG.filter.date_end + ' '+ getHM(time_key) + '</td>';
        }
        if (!d['count_all']) {
            j = 1;
            line += '<td width="100"> -- </td>';
            line += '<td width="100"> -- </td>';
            line += '<td width="100"> -- </td>';
            line += '<td width="100"> -- </td>';
            line += '<td width="100"> -- </td>';
        } else {
//            console.dir(d);
            fail_rate = round((d['count_all'] - d['count_failed']) / d['count_all'] * 100, 2);
            //调用次数
            line += '<td width="100">' + d['count_all'] + '</td>';
            if (fail_rate >= StatsG.config.green_rate) {
                td_color = 'green';
            } else {
                td_color = 'red';
            }
            //失败次数
            line += '<td width="100"><a href="/appstats/fail?id=' + d['id']+'" ' +
                'style="color: red; ">' + d['count_failed'] + '</td>';
            //成功率
            line += '<td width="100" style="color: '+td_color+'">' + fail_rate + '%</td>';
            //成功平均响应事件
            avg_time = round((d['time_sum']-d['time_failed_sum'])*1000 / (d['count_all'] - d['count_failed']), 2);
            line += '<td width="100">' + avg_time + 's </td>';
            //失败响应时间
            if (d['time_failed_sum'] > 0) {
                avg_fail_time = round(d['time_failed_sum']*1000 / d['count_failed'], 2);
                line += '<td width="100">' + avg_fail_time + 's </td>';
            } else {
                line += '<td width="100"> -- </td>';
            }
        }
    }
    line += "</tr>";
    $('#history_table').append(line);
    i = 0;
    $('#history_table tr').each(function(e, o){
        if ((i++%2)==1) {
            $(o).attr('style', "background-color: #efefef;");
        }
    });
};

StatsG.appendToTable = function (interface_id, _data, option) {
    var line;
    var tr_color; //green, normal
    if (!_data['count_all']) {
        return;
    }
    _data.interface_id = interface_id;
    var stats_str = StatsG.parseStatsData(_data);
    if (_data.fail_rate < StatsG.config.green_rate) {
        tr_color = '#FFDFDF';
    } else {
        tr_color = '#DFFFDF';
    }
    line = "<tr height='32' style='background-color: "+tr_color+"' width='100%'>";
    //接口名称
    line += '<td>' + StatsG.interface_name[interface_id] + '</td>';
    //日期
    //+ data.date + ' '
    //时间
    line += '<td>' + _data.time_str + '</td>';
    line += stats_str;
    line += '<td>';
    if (!option.no_detail) {
        line += '<a href="javascript: StatsG.showDetail(' + interface_id + ')">查看明细</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        line += '<a href="/stats/history/?module_id=' + _data['module_id'] + '&interface_id=' + interface_id + '&date_key=' +
        StatsG.filter.date_key
        + '">历史数据对比</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        line += '<a href="/stats/client/?module_id=' + _data['module_id'] + '&interface_id=' + interface_id + '&date_key=' +
        StatsG.filter.date_key
        + '">主调明细</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        line += '<a href="/stats/server/?module_id=' + _data['module_id'] + '&interface_id=' + interface_id + '&date_key=' +
        StatsG.filter.date_key
        + '">被调明细</a>';
    }
    line += '</td>';

    line += "</tr>";
    $('#data_table_body').append(line);
};

StatsG.parseStatsData = function(_data) {
    var line = '';
    var fail_rate, avg_fail_time, avg_time, td_color;
    // 成功率
    fail_rate = round((_data['count_all'] - _data['count_failed']) / _data['count_all'] * 100, 2);
    //调用总次数
    line += '<td>' + number_format(_data['count_all']) + '</td>';
    //成功次数
    if (_data['succ_count'] > 0) {
        if (_data.time_key != undefined) {
            line += '<td data-order="' + _data['succ_count'] + '"><a href="javascript: StatsG.openSuccPage(' + _data.module_id + ',' + _data.interface_id + ',\'' + _data.time_key + '\')" ' +
            'style="color: green ">' + number_format(_data['succ_count']) + '</td>';
        }
        else {
            line += '<td data-order="' + _data['succ_count'] + '"><a href="javascript: StatsG.openSuccPage(' + _data.module_id + ',' + _data.interface_id + ')" ' +
            'style="color: green ">' + number_format(_data['succ_count']) + '</td>';
        }
    } else {
        line += '<td data-order="0"> 0 </td>';
    }
    //成功率颜色
    if (fail_rate >= StatsG.config.green_rate) {
        td_color = 'green';
    } else {
        td_color = 'red';
    }
    _data['fail_rate'] = fail_rate;
    //失败次数
    if (_data['count_failed'] > 0) {
        if (_data.time_key != undefined)
        {
            line += '<td data-order="' + _data['count_failed'] + '"><a href="/appstats/fail?id='+_data['id']+'" ' +
            'style="color: red; ">' + number_format(_data['count_failed']) + '</td>';
        }
        else
        {
            line += '<td data-order="' + _data['count_failed'] + '"><a href="/appstats/fail?id='+_data['id']+'" ' +
            'style="color: red; ">' + number_format(_data['count_failed']) + '</td>';
        }
    } else {
        line += '<td data-order="0"> 0 </td>';
    }
    //成功率
    line += '<td style="color: '+td_color+'" data-order="' + fail_rate + '">' + fail_rate + '%</td>';
    //响应时间最大值
    line += '<td data-order="' + _data['time_max'] + '">' + _data['time_max'] + 'ms</td>';
    //响应时间最小值
    line += '<td data-order="' + _data['time_min'] + '">' + _data['time_min'] + 'ms</td>';
    //成功平均响应事件
    avg_time = round((_data['time_sum']-_data['time_failed_sum']) / (_data['count_all'] - _data['count_failed']), 2);
    line += '<td data-order="' + avg_time + '">' + avg_time + 'ms </td>';

    //失败响应时间
    if (_data['count_failed'] > 0) {
        avg_fail_time = round(_data['time_failed_sum'] / _data['count_failed'], 2);
        line += '<td data-order="' + avg_fail_time + '">' + avg_fail_time + 'ms </td>';
    } else {
        line += '<td data-order="0"> -- </td>';
    }
    return line;
};

StatsG.appendToTable2 = function (ip, _data, param) {
    var line;
    var tr_color; //green, normal
    //console.dir(_data);
    //console.dir(interface_id);
    if (!_data['count_all']) {
        return;
    }
    var stats_str = StatsG.parseStatsData(_data);
    line = "<tr height='32'>";
    //机器IP
    line += '<td>' + ip + '</td>';
    //调用比例
    line += '<td>' + round((_data['count_all'] / param['count_all'])*100, 2)  + '% </td>';
    //失败比例
    if (param['count_failed']) {
        line += '<td style="color: red">' + round((_data['count_failed'] / param['count_failed'])*100, 2)  + '% </td>';
    } else {
        line += '<td> -- </td>';
    }
    //日期
    line += stats_str;
    line += "</tr>";
    $('#data_table_body').append(line);
};

StatsG.showDetail = function (interface_id) {
    StatsG.filter.interface_id = interface_id;
    StatsG.refresh = StatsG.showDetail;
    $('#interface_id').val(interface_id+': '+StatsG.interface_name[interface_id]);
    var o, hour, i;
    $('#data_table_body').html('');
    StatsG.interface_stats[interface_id].sort(function(a, b){
        return a.time_key - b.time_key;
    });
    //将所有接口的统计数据进行汇总
    for (i = 0; i < StatsG.interface_stats[interface_id].length; i++) {
        o = StatsG.interface_stats[interface_id][i];
        o.time_str = getTimerStr(o.time_key);
        hour = parseInt(o.time_str.split(':')[0], 10);
        if (hour < StatsG.filter.hour_start || hour > StatsG.filter.hour_end) {
            continue;
        }
        StatsG.appendToTable(interface_id, o, {no_detail: true});
    }
}

function fillZero4Time(s) {
    if (s < 10) {
        return '0' + s;
    } else {
        return s;
    }
}

function getHM(time_key) {
    var d=new Date(parseInt(time_key)*1000);
    return d.getHours()+":"+ d.getMinutes();
}

function getTimerStr(time_key) {
    var _h = time_key / 12.0;
     var h = parseInt(_h, 10);
     var _m = round((((_h - h) * 60)/5)*5);
     return fillZero4Time(h) + ':'+ fillZero4Time(_m);
}

StatsG.openFailPage = function (module_id,interface_id, time_key, date_key) {
    var url = '/stats/fail/?';
    if (module_id) {
        url += '&module_id=' + module_id;
    } else {
        url += 'module_id=' + StatsG.filter.module_id;
    }

    if (interface_id) {
        url += '&interface_id=' + interface_id;
    } else {
        url += '&interface_id=' + StatsG.filter.interface_id;
    }
    if (date_key) {
        url += '&date_key=' + date_key;
    } else {
        url += '&date_key=' + StatsG.filter.date_key;
    }
    if (time_key) {
        url += '&time_key=' + time_key;
    }
    location.href = url;
};

StatsG.openSuccPage = function (module_id,interface_id, time_key, date_key) {
    var url = '/stats/succ/?';
    if (module_id) {
        url += '&module_id=' + module_id;
    } else {
        url += 'module_id=' + StatsG.filter.module_id;
    }

    if (interface_id) {
        url += '&interface_id=' + interface_id;
    } else {
        url += '&interface_id=' + StatsG.filter.interface_id;
    }
    if (date_key) {
        url += '&date_key=' + date_key;
    } else {
        url += '&date_key=' + StatsG.filter.date_key;
    }
    if (time_key) {
        url += '&time_key=' + time_key;
    }
    location.href = url;
};

StatsG.getStatsData = function () {
    $.ajax({
        url: StatsG.url,
        dataType : 'json',
        data: StatsG.filter,
        success: function(data) {
            $('#data_table_stats').dataTable().fnDestroy();
            $('#data_table_body').empty();
            var i = 0;
            var interface_id = 0;
            var stats = {};
            StatsG.interface_stats = {};
            StatsG.data = data;
            //将interface组成一个map
            for (i = 0; i < data.interface.length; i++) {
                interface_id = data.interface[i].id;
                if (data.interface[i].alias) {
                    StatsG.interface_name[interface_id] = data.interface[i].alias;
                } else {
                    StatsG.interface_name[interface_id] = data.interface[i].name;
                }
                stats[interface_id] = {
                    'count_all': 0,
                    'count_failed': 0,
                    'succ_count':0,
                    'time_failed_sum': 0.0,
                    'time_sum': 0.0,
                    'time_max': 0,
                    'time_min': 0,
                    time_str: data.time_str,
                    date_key: data.date
                }
            }

            //将所有接口的统计数据进行汇总
            for (i = 0; i < data.stats.length; i++) {
                interface_id = data.stats[i]['interface_id'];
                if (!StatsG.interface_stats[interface_id]) {
                    StatsG.interface_stats[interface_id] = [];
                }
                if (!array_key_exists(interface_id,stats))
                {
                    stats[interface_id] = {
                        'count_all': 0,
                        'count_failed': 0,
                        'succ_count':0,
                        'time_failed_sum': 0.0,
                        'time_sum': 0.0,
                        'time_max': data.stats[i]['time_max'],
                        'time_min': data.stats[i]['time_min'],
                        time_str: '00:00 ~ 23:59',
                        date_key: data.date
                    }
                }
                if (stats[interface_id]['time_max'] == undefined)
                {
                    stats[interface_id]['time_max'] = data.stats[i]['time_max'];
                }
                else
                {
                    if (data.stats[i]['time_max'] > stats[interface_id]['time_max'])
                    {
                        stats[interface_id]['time_max'] = data.stats[i]['time_max'];
                    }
                }

                if (stats[interface_id]['time_min'] == undefined)
                {
                    stats[interface_id]['time_min'] = data.stats[i]['time_min'];
                }
                else
                {
                    if (data.stats[i]['time_min'] < stats[interface_id]['time_min'])
                    {
                        stats[interface_id]['time_min'] = data.stats[i]['time_min'];
                    }
                }
                /*
                 if (i == 0)
                 {
                 stats[interface_id]['time_max'] = data.stats[i]['time_max'];
                 stats[interface_id]['time_min'] = data.stats[i]['time_min'];
                 }
                 else
                 {
                 if (data.stats[i]['time_max'] > stats[interface_id]['time_max'])
                 {
                 stats[interface_id]['time_max'] = data.stats[i]['time_max'];
                 }
                 if (data.stats[i]['time_min'] < stats[interface_id]['time_min'])
                 {
                 stats[interface_id]['time_min'] = data.stats[i]['time_min'];
                 }
                 }*/
                data.stats[i]['succ_count'] = data.stats[i]['count_all'] - data.stats[i]['count_failed'];
                StatsG.interface_stats[interface_id].push(data.stats[i]);
                if (!array_key_exists('count_all', stats[interface_id])) {
                    continue;
                }
                stats[interface_id]['count_all'] += data.stats[i]['count_all'];
                stats[interface_id]['succ_count'] += data.stats[i]['succ_count'];
                stats[interface_id]['count_failed'] += data.stats[i]['count_failed'];
                if (!stats[interface_id]['module_id']) {
                    stats[interface_id]['module_id'] = data.stats[i]['module_id'];
                }
                stats[interface_id]['time_failed_sum'] += parseFloat(data.stats[i]['time_failed_sum']);
                stats[interface_id]['time_sum'] += parseFloat(data.stats[i]['time_sum']);
//                console.log(data.stats[i]['time_key']);
//                console.log(data.stats[i]['time_sum']);
//                console.log(stats[interface_id]['time_failed_sum']);
                //console.log(stats[interface_id]['time_sum']);
            }
            for (i = 0; i < data.interface.length; i++) {
                interface_id = data.interface[i].id;
                StatsG.appendToTable(interface_id, stats[interface_id], {});
            }
            StatsG.dataTable = $('#data_table_stats').dataTable(StatsG.config.data_table);
        }
    });
};

StatsG.showDetailStats = function() {
    StatsG.page_url = location.pathname;
    StatsG.refresh = StatsG.showDetailStats;
    $.ajax({
        url: StatsG.url,
        dataType : 'json',
        data: StatsG.filter,
        success: function(data) {
            var ip = '';
            var stats = {};
            var count_all = 0;
            var count_failed = 0;
            var succ_count = 0;

            //将interface组成一个map
            for (var i = 0; i < data.length; i++) {
                ip = data[i].ip;
                count_all += parseInt(data[i]['count_all'], 10);
                count_failed +=  parseInt(data[i]['count_failed'], 10);
                succ_count +=  parseInt(data[i]['count_all']-data[i]['count_failed'], 10);

                if (!stats[ip]) {
                    stats[ip] = {
                        'ip' : ip,
                        'interface_id': StatsG.filter.interface_id,
                        'module_id': StatsG.filter.module_id,
                        'count_all': 0,
                        'succ_count': 0,
                        'count_failed': 0,
                        'time_failed_sum': 0.0,
                        'time_sum': 0.0,
                        'time_max': 0,
                        'time_min': 0
                    }
                }
            }
            //将所有接口的统计数据进行汇总
            for (i = 0; i < data.length; i++) {
                ip = data[i]['ip'];
                stats[ip]['count_all'] += parseInt(data[i]['count_all']);
                stats[ip]['count_failed'] += parseInt(data[i]['count_failed'], 10);
                stats[ip]['succ_count'] += parseInt(data[i]['count_all']-data[i]['count_failed'], 10);
                stats[ip]['time_failed_sum'] += parseFloat(data[i]['time_failed_sum']);
                stats[ip]['time_sum'] += parseFloat(data[i]['time_sum']);
                if (i == 0)
                {
                    stats[ip]['time_max'] = parseFloat(data[i]['time_max']);
                    stats[ip]['time_min'] = parseFloat(data[i]['time_min']);
                }
                else
                {
                    if (parseFloat(data[i]['time_max']) > stats[ip]['time_max'])
                    {
                        stats[ip]['time_max'] = parseFloat(data[i]['time_max']);
                    }
                    if (parseFloat(data[i]['time_min']) < stats[ip]['time_min'])
                    {
                        stats[ip]['time_min'] = parseFloat(data[i]['time_min']);
                    }
                }
            }
            for (ip in stats) {
                StatsG.appendToTable2(ip, stats[ip], {'count_all' : count_all, 'count_failed' : count_failed});
            }
            StatsG.dataTable = $('#data_table_stats').dataTable(StatsG.config.data_table);
        }
    });
}