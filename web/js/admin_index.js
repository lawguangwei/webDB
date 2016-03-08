/**
 * Created by luoguangwei on 16/3/3.
 */
$(function () {
    Highcharts.setOptions({
        lang: {
            loading: 'Loading...',
            months: ['January', 'February', 'March', 'April', 'May', 'June', 'July',
                'August', 'September', 'October', 'November', 'December'],
            shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            weekdays: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            // invalidDate: '',
            decimalPoint: '.',
            numericSymbols: ['k', 'M', 'G', 'T', 'P', 'E'], // SI prefixes used in axis labels
            resetZoom: 'Reset zoom',
            resetZoomTitle: 'Reset zoom level 1:1',
            thousandsSep: ' '
        },
    });

    setBasicInfo();
    setAdminLoginLogs();
    setTableFile1();
    setTableFile2();

    $.ajax({
        type:'get',
        url:'index.php?r=admin/login-statistics',
        data:{},
        dataType:'json',
        success:function(data){
            setStatistics1(data);
        }
    });

    $('.user-option').hide();
    $('#div-user-info').hide();

    $('#btn-query-user').on('click',function(){
        setUser();
    });


    $('#modal-set-user').find('.btn-primary').on('click',function(){
        var email = $('#p-email').text();
        var info = $('#set-user-info').val();
        $.ajax({
            type:'post',
            url:'index.php?r=admin/set-user',
            data:{'user_email':email,'info':info},
            dataType:'json',
            success:function(data){
                if(data['code'] == '0'){
                    setUser();
                }else{
                    alert('设置失败')
                }
            }
        });
    });
    $('#modal-set-size').find('.btn-primary').on('click',function(){
        var email = $('#p-email').text();
        var size = $('#input-size').val();
        var info = $('#set-size-info').val();
        $.ajax({
            type:'post',
            url:'index.php?r=admin/set-user-size',
            data:{'user_email':email,'size':size,'info':info},
            dataType:'json',
            success:function(data){
                if(data['code'] == '0'){
                    setUser();
                }else{
                    alert('设置失败')
                }
            }
        });
    });

    $("#statistics-btn-group button").on('click',function(){
        $('#statistics-btn-group button').removeClass('btn-success');
        $(this).addClass('btn-success');
        var num = $(this).attr('aria-statistics');
        if(num == '1'){
            $.ajax({
                type:'get',
                url:'index.php?r=admin/login-statistics',
                data:{},
                dataType:'json',
                success:function(data){
                    setStatistics1(data);
                }
            });
        }
        if(num == '2'){
            $.ajax({
                type:'get',
                url:'index.php?r=admin/statistics-size',
                data:{},
                dataType:'json',
                success:function(data){
                    setStatistics2(data);
                }
            });
        }
        if(num == '3'){
            $.ajax({
                type:'get',
                url:'index.php?r=admin/statistics-user',
                data:{},
                dataType:'json',
                success:function(data){
                    setStatistics3(data);
                }
            });
        }
    });
});

function setStatistics1(data){
    var xx = [];
    for(var i=data.length-1;i>=0;i--){
        var dt = new Date(data[i]['date']);
        var y = dt.getUTCFullYear();
        var m = dt.getMonth();
        var d = dt.getUTCDate();
        xx.push([Date.UTC(y,m,d),parseInt(data[i]['num'])]);
    }

    $('#statistics').highcharts({
        chart: {
            zoomType: 'x',
            spacingRight: 20,
            marginTop: 80,
            marginRight: 40
        },
        title: {
            text: '网站热度'
        },
        subtitle: {
            text: '每日登录量'
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: { // don't display the dummy year
                month: '%e. %b',
                year: '%b'
            }
        },
        yAxis: {
            title: {
                text: '登录量'
            },
            min:0
        },
        tooltip: {
            shared: true
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            area: {
                fillColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                lineWidth: 1,
                marker: {
                    enabled: false
                },
                shadow: false,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },

        series: [{
            type: 'area',
            name: '登录量',
            //pointInterval: 24 * 3600 * 1000,
            data: xx
        }]
    });
}

function setStatistics2(data){
    var add = [];
    var del = [];
    for(var i=data['add'].length-1;i>=0;i--){
        var dt = new Date(data['add'][i]['date']);
        var y = dt.getUTCFullYear()
        var m = dt.getMonth();
        var d = dt.getUTCDate();
        var add_size = parseInt(data['add'][i]['size']);
        //var add_size = Math.round((add_size/(1024*1024*1024))*100)/100;
        add.push([Date.UTC(y,m,d),add_size]);
    }
    for(var i=data['delete'].length-1;i>=0;i--){
        var dt = new Date(data['delete'][i]['date']);
        var y = dt.getUTCFullYear()
        var m = dt.getMonth();
        var d = dt.getUTCDate();
        var del_size = parseInt(data['delete'][i]['size']);
        //var del_size = Math.round((del_size/(1024*1024*1024))*100)/100;
        del.push([Date.UTC(y,m,d),del_size]);
    }

    $('#statistics').highcharts({
        chart: {
            type: 'spline'
        },
        title: {
            text: '容量变动'
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: { // don't display the dummy year
                month: '%e. %b',
                year: '%b'
            }
        },
        yAxis: {
            title: {
                text: '单位(G)'
            },
            min: 0
        },
        tooltip: {
            formatter: function() {
                return '<b>'+ this.series.name +'</b><br/>'+
                    Highcharts.dateFormat('%e. %b', this.x) +': '+ this.y +' G';
            }
        },

        series: [{
            name: '新增容量',
            data: add
        },{
            name: '减少容量',
            data: del
        }]
    });
}

function setStatistics3(data){
    var xx = [];
    for(var i=data.length-1;i>=0;i--){
        var dt = new Date(data[i]['date']);
        var y = dt.getUTCFullYear()
        var m = dt.getMonth();
        var d = dt.getUTCDate();
        xx.push([Date.UTC(y,m,d),parseInt(data[i]['num'])]);
    }

    $('#statistics').highcharts({
        chart: {
            type: 'spline'
        },
        title: {
            text: '新增用户统计'
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: { // don't display the dummy year
                month: '%e. %b',
                year: '%b'
            }
        },
        yAxis: {
            title: {
                text: '单位(人)'
            },
            min: 0
        },
        tooltip: {
            formatter: function() {
                return '<b>'+ this.series.name +'</b><br/>'+
                    Highcharts.dateFormat('%e. %b', this.x) +': '+ this.y +' 人';
            }
        },

        series: [{
            name: '注册人数',
            data: xx
        }]
    });
}

function setBasicInfo(){
    $.ajax({
        type:'get',
        url:'index.php?r=admin/set-basic-info',
        data:{},
        dataType:'json',
        success:function(data){
            $('#span-user-num').text(data['user']+'人');
            $('#span-file-num').text(data['file_num']+'个');
            $('#span-file-size').text((Math.round(data['file_size']/(1024*1024*1024))*100)/100+'GB');
        }
    });
}

function setAdminLoginLogs(){
    $.ajax({
        type:'get',
        url:'index.php?r=admin/get-admin-login-log',
        data:{},
        dataType:'json',
        success:function(data){
            var i=1;
            data.forEach(function(log){
                var content = '<tr>' +
                    '<td>'+ i++ +'</td>' +
                    '<td>'+log['login_ip']+'</td>' +
                    '<td>'+log['ip_address']+'</td>' +
                    '<td>'+log['login_date']+'</td>' +
                    '</tr>';
                $('#login-log').find('table').append(content);
            });
        }
    });
}

function setUser(){
    var userEmail = $('#input-email').val();
    $.ajax({
        type:'post',
        url:'index.php?r=admin/query-user',
        data:{'user_email':userEmail},
        dataType:'json',
        success:function(data){
            if(data['user'] != '1'){
                $('.p-email').text(data['user']['user_email']);
                $('#p-name').text(data['user']['user_name']);
                $('#p-date').text(data['user']['create_date']);
                if(data['user']['state'] == '0'){
                    $('#p-state').removeClass('text-danger');
                    $('#p-state').addClass('text-success');
                    $('#p-state').text('正常');
                }else{
                    $('#p-state').removeClass('text-success');
                    $('#p-state').addClass('text-danger');
                    $('#p-state').text('禁用');
                }
                $('#p-size').text(Math.round((data['disk']['capacity']/(1024*1024*1024))*100)/100+"GB");
                $('#div-user-info').show();
                $('.user-option').show();
                setUserManageLog(data['um_logs'],data['admin']);
            }else{
                alert('账号不存在');
                $('.user-option').hide();
                $('#div-user-info').hide();
            }
        }
    });
}

function setUserManageLog(logs,admin){
    var i=0;
    $(".tr-um-user").remove();
    logs.forEach(function(log){
        var optionType;
        if(log['um_manage_type'] == '0'){
            optionType = '启用';
        }
        if(log['um_manage_type'] == '1'){
            optionType = '禁用';
        }
        if(log['um_manage_type'] == '2'){
            optionType = '设置空间';
        }
        var content = '<tr class="tr-um-user" info="'+log['um_manage_info']+'">' +
            '<td>'+ i++ +'</td>' +
            '<td>'+optionType+'</td>' +
            '<td>'+admin+'</td>' +
            '<td>'+log['create_date']+'</td>' +
            '</tr>';
        $('#table-um-user').append(content);
    });
    $('.tr-um-user').on('dblclick',function(){
        alert($(this).attr('info'));
    });
}

function setTableFile1(){
    $.ajax({
        type:'get',
        url:'index.php?r=admin/most-down-files',
        data:{},
        dataType:'json',
        success:function(data){
            var i=1;
            $('.tr-file-1').remove();
            data.forEach(function(item){
                var content = '<tr class="tr-file-1">' +
                    '<td>'+ i++ +'</td>' +
                    '<td class="td-file-id">'+item['file_id']+'</td>' +
                    '<td class="td-file-type">'+item['file_type']+'</td>' +
                    '<td class="td-file-size">'+item['file_size']+'MB</td>' +
                    '<td>'+item['num']+'</td>' +
                    '<td>' +
                    '<a class="text-success btn-download" file-id="'+item['file_id']+'" style="cursor: pointer">下载</a>' +
                    '<a class="text-danger btn-disable-file" style="cursor: pointer" data-toggle="modal" data-target="#modal-set-file">禁用</a>' +
                    '</td>' +
                    '</tr>';
                $('#table-file-1').append(content);
            })
            $('.btn-download').off('click');
            $('.btn-download').on('click',function(){
                var file_id = $(this).attr('file-id');
                //$('#download-id').val(file_id);
                window.open('index.php?r=admin/getfile&file_id='+file_id);
            });
            $('.btn-disable-file').off('click');
            $('.btn-disable-file').on('click',function(){
                $('#p-file-id').text($(this).parent().parent().find('.td-file-id').text())
                $('#p-file-type').text($(this).parent().parent().find('.td-file-type').text());
                $('#p-file-size').text($(this).parent().parent().find('.td-file-size').text());
            });
        }
    });
}

function setTableFile2(){
    $.ajax({
        type:'get',
        url:'index.php?r=admin/most-user-files',
        data:{},
        dataType:'json',
        success:function(data){
            var i=1;
            $('.tr-file-2').remove();
            data.forEach(function (item) {
                var content = '<tr class="tr-file-2">' +
                    '<td>'+ i++ +'</td>' +
                    '<td class="td-file-id">'+item['file_id']+'</td>' +
                    '<td class="td-file-type">'+item['file_type']+'</td>' +
                    '<td class="td-file-size">'+item['file_size']+'MB</td>' +
                    '<td>'+item['num']+'</td>' +
                    '<td>' +
                    '<a class="text-success btn-download" file-id="'+item['file_id']+'" style="cursor: pointer">下载</a>' +
                    '<a class="text-danger btn-disable-file" style="cursor: pointer"  data-toggle="modal" data-target="#modal-set-file">禁用</a>' +
                    '</td>' +
                    '</tr>';
                $('#table-file-2').append(content);
            })
            $('.btn-download').off('click');
            $('.btn-download').on('click',function(){
                var file_id = $(this).attr('file-id');
                //$('#download-id').val(file_id);
                window.open('index.php?r=admin/getfile&file_id='+file_id);
            });
            $('.btn-disable-file').off('click');
            $('.btn-disable-file').on('click',function(){
                $('#p-file-id').text($(this).parent().parent().find('.td-file-id').text())
                $('#p-file-type').text($(this).parent().parent().find('.td-file-type').text());
                $('#p-file-size').text($(this).parent().parent().find('.td-file-size').text());
            });
        }
    });
}

