$(function () {
    var capacity = $('#static1').attr('capacity');
    var avail = $('#static1').attr('available');

    $('#static1').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: '剩余容量'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b><br/>大小: <b>{point.size}GB</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },

        series: [{
            type: 'pie',
            name: '百分比',
            data: [
                {
                    name: '剩余容量',
                    y: Math.round(((avail/capacity)*100)*10)/10,
                    size:Math.round((avail/(1024*1024*1024))*100)/100

                },
                {
                    name: '已用容量',
                    y: Math.round((((capacity-avail)/capacity)*100)*10)/10,
                    sliced: true,
                    selected: true,
                    size:Math.round(((capacity-avail)/(1024*1024*1024))*100)/100
                },
            ]
        }],
    });

    $('#static2').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: '文件占比'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b><br/>大小: <b>{point.size}MB</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: '文件占比',
            data: [
                {
                    name:'图片',
                    y : Math.round((($('#static2').attr('picture')/(capacity-avail))*100)*10)/10,
                    size:Math.round(($('#static2').attr('picture')/(1024*1024))*100)/100,
                },
                {
                    name:'文档',
                    y : Math.round((($('#static2').attr('word')/(capacity-avail))*100)*10)/10,
                    size:Math.round(($('#static2').attr('word')/(1024*1024))*100)/100,
                },
                {
                    name: '视频',
                    y:  Math.round((($('#static2').attr('film')/(capacity-avail))*100)*10)/10,
                    sliced: true,
                    selected: true,
                    size:Math.round(($('#static2').attr('film')/(1024*1024))*100)/100,
                },
                {
                    name:'音频',
                    y : Math.round((($('#static2').attr('music')/(capacity-avail))*100)*10)/10,
                    size:Math.round(($('#static2').attr('music')/(1024*1024))*100)/100,
                },
                {
                    name:'其它',
                    y : Math.round((($('#static2').attr('other')/(capacity-avail))*100)*10)/10,
                    size:Math.round(($('#static2').attr('other')/(1024*1024))*100)/100,
                },
            ]
        }]
    });

});