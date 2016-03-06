
function setHeight(){
    var w_height = $(window).height();
    var b_height = $(document).height();
    $('#lr-div').css({'height':b_height});
    $('#content-panel').css({'height':b_height});
}
function setWebdbSize(){
    var capacity = $('#webdb-size').attr('capacity');
    var avail = $('#webdb-size').attr('available-size');
    var present = ((capacity-avail)/capacity)*100;
    $('#webdb-size > div').css('width',present+"%");
}
function setLiOption(){
    var option = $('#lr-bar').attr('li-option');
    $('#li-'+option).addClass('selected');
    $('#li-'+option).children('span').css('color','#0088e4');
}
$(function(){
    var myxhr = null;
    var formDatas = new Array();
    var optionFiles = new Array();

    setHeight();
    setWebdbSize();
    setLiOption();


    window.onbeforeunload = function (event)
    {
        if(myxhr != null){
            var c = event || window.event;
            if (/webkit/.test(navigator.userAgent.toLowerCase())) {
                return"离开页面将导致数据丢失！";
            }
            else
            {
                c.returnValue ="离开页面将导致数据丢失！";
            }
        }
    }

    $('#lr-bar > a > li').hover(function(){
        $(this).children('span').addClass('selected');
    },function(){
        $(this).children('span').removeClass('selected');
    });

    $(window).resize(function(){
        setHeight();
    });

    $('.tr-file').hover(function(){
        $(this).find('.td-btns').attr('style','display:block');
    },function(){
        $(this).find('.td-btns').attr('style','display:none');
    });


    $('#upload-btn').click(function(){
        $('#file-input').click();
    });

    $('#mkdir-btn').click(function(){
        $('#modal-mkdir').modal('toggle');
    });

    $('#file-input').change(function(){
        $('#span-filename').text($('#file-input').val());
        $("#modal-upload").modal('toggle');
    });


    $('#modal-upload-btn').click(function(){
        var formData = new FormData($('#form-upload-file')[0]);
        var url = $(this).attr("url");
        formDatas.unshift(formData);
        var item = '<div class="col-md-12 div-upload-item">' +
            '<p class="col-md-12" style="word-break: break-all">'+$('#file-input').val()+'</p></div>';
        $('#upload-list-div').append(item);
        $('#file-input').val('');
        if(formDatas.length == 1){
            var data = formDatas[0];
            uploadFile(url,data);
        }
    });


    $('#progress-div').find('button').click(function(){
        myxhr.abort();
        myxhr = null;
        $('#upload-list-div').children(':first').remove();
        formDatas.pop();
        $('progress').attr({'value':'0','max':100});
        if(formDatas.length != 0){;
            var url = $('#modal-upload-btn').attr('url');
            var data = formDatas.pop();
            uploadFile(url,data);
        }
    });


    function uploadFile(url,data){
        $.ajax({
            url:url,
            type:'post',
            xhr:function(){
                myxhr = $.ajaxSettings.xhr();
                if(myxhr.upload){
                    myxhr.upload.addEventListener('progress',progressHandlingFunction,false);
                }
                return myxhr;
            },
            data:data,
            dataType:'json',
            error:function(){
                alert('网络错误');
            },
            success:function(result){
                if(result['msg'] == 'success'){
                    addNewFile(result['file'],result['disk']);
                    myxhr = null;
                    $('progress').attr({'value':'0','max':100});
                    $('#upload-list-div').children(':first').remove();
                    var data = formDatas.pop();
                    if(formDatas.length != 0){
                        var data = formDatas.pop();
                        uploadFile(url,data);
                    }
                }else{
                    alert(result['msg']);
                    myxhr = null;
                    location.reload();
                }
            },
            cache:false,
            contentType:false,
            processData:false
        });
    }


    function addNewFile(file,disk){

        var content = '<tr class="tr-file" base-url="localhost/webdb/web">' +
            '<td>' +
            '<label class="checkbox-inline"><input type="checkbox" value="'+file['f_record_id']+'">&nbsp;&nbsp;<span class="glyphicon glyphicon-file"></span>&nbsp;<span class="span-file-name">'+file['file_name']+'</span></label>' +
            '<div class="td-btns" style="display: none">' +
            '<a class="btn-download" file-id="'+file['file_id']+'" url="index.php?r=file/getfile"><span class="glyphicon glyphicon-download-alt"></span></a>&nbsp ' +
            '<a class="btn-delete" record-id="'+file['f_record_id']+'" url="index.php?r=file/delete-file"><span class="glyphicon glyphicon-remove"></span></a>' +
            '</div>' +
            '</td>' +
            '<td>'+Math.round((file['file_size']/(1024*1024))*100)/100+'MB</td>' +
            '<td>'+file['upload_date']+'</td>' +
            '</tr>';
        $('#file-table').append(content);

        var present = ((disk['capacity']-disk['available_size'])/disk['capacity'])*100;
        $('#webdb-size > div').css('width',present+"%");
        $('#p-capacity').text(Math.round((disk['capacity']/(1024*1024*1024))*100)/100);
        $('#p-available').text(Math.round((disk['available_size']/(1024*1024*1024))*100)/100);

        $('.tr-file').on('mouseover mouseout', function(event){
            if(event.type == "mouseover"){
                $(this).find('.td-btns').attr('style','display:block');
                //鼠标悬浮
            }else if(event.type == "mouseout"){
                $(this).find('.td-btns').attr('style','display:none');
                //鼠标离开
            }
        });
        $('.btn-download').on('click',function(){
            var file_id = $(this).attr('file-id');
            //$('#download-id').val(file_id);
            window.open('index.php?r=file/getfile&file_id='+file_id);
        });
        $('.btn-delete').on('click',function(){
            var node = $(this).parent().parent().parent();
            var recordId = $(this).attr('record-id');
            var url = $(this).attr('url');
            $.ajax({
                url:url,
                type:'post',
                data:{'record_id':recordId},
                dataType:'json',
                success:function(result){
                    var disk = result['disk'];
                    var present = ((disk['capacity']-disk['available_size'])/disk['capacity'])*100;
                    $('#webdb-size > div').css('width',present+"%");
                    $('#p-capacity').text(Math.round((disk['capacity']/(1024*1024*1024))*100)/100);
                    $('#p-available').text(Math.round((disk['available_size']/(1024*1024*1024))*100)/100);
                    node.remove();
                }
            });
        });
        $(":checkbox").on('click',function(){
            var flag = false;
            $(":checkbox").each(function () {
                if ($(this).is(":checked")) {
                    optionFiles.push($(this).attr("value"));
                    flag = true;
                }
            });
            if(flag){
                $('.file-option1').show();
            }else{
                $('.file-option1').hide();
            }
        });
        $('.tr-file').on('dblclick',function(){
            var item = '<input type="text" placeholder="输入新文件名">&nbsp;&nbsp;<button class="rename-yes">确定</button>&nbsp;&nbsp;<button class="rename-no">取消</button>';
            var baseUrl = $(this).attr('base-url');
            $(this).find('label').after(item);
            $(this).find('.rename-no').on('click',function(){
                $(this).parent().find(':text').remove();
                $(this).parent().find('.rename-yes').remove();
                $(this).remove();
            });
            $(this).find('.rename-yes').on('click',function(){
                var newName = $(this).parent().find(':text').val();
                var item1 = $(this).parent().find(':text');
                var item2 = $(this).parent().find('.rename-no');
                var item3 = $(this);
                var item4 = $(this).parent().find('label').children('.span-file-name');
                if(newName != ""){
                    var url ='index.php?r=file/rename';
                    var recordId = $(this).parent().find(':checkbox').val();
                    $.ajax({
                        url:url,
                        type:'post',
                        data:{'record_id':recordId,'new_name':newName},
                        dataType:'json',
                        success:function(result){
                            if(result['code'] == '0'){
                                item1.remove();
                                item2.remove();
                                item3.remove();
                                item4.text(result['file_name']);
                            }
                        }
                    });

                }else{
                    alert("请输入新文件名!");
                }
            });
        });
    }


    function progressHandlingFunction(e){
        //console.log(e.loaded*100/e.total+"%");
        $("progress").attr({"value":e.loaded,"max":e.total});
    }


    $('#modal-mkdir-btn').click(function(){
        $('#form-mkdir').submit();
    });

    $('.btn-download').click(function(){
        var file_id = $(this).attr('file-id');
        //$('#download-id').val(file_id);
        window.open('index.php?r=file/getfile&file_id='+file_id);
    });


    $('.btn-delete').click(function(){
        var node = $(this).parent().parent().parent();
        var recordId = $(this).attr('record-id');
        var url = $(this).attr('url');
        $.ajax({
            url:url,
            type:'post',
            data:{'record_id':recordId},
            dataType:'json',
            success:function(result){
                var disk = result['disk'];
                var present = ((disk['capacity']-disk['available_size'])/disk['capacity'])*100;
                $('#webdb-size > div').css('width',present+"%");
                $('#p-capacity').text(Math.round((disk['capacity']/(1024*1024*1024))*100)/100);
                $('#p-available').text(Math.round((disk['available_size']/(1024*1024*1024))*100)/100);
                node.remove();
            }
        });
    })

    $(":checkbox").click(function(){
        var flag = false;
        $(":checkbox").each(function () {
            if ($(this).is(":checked")) {
                optionFiles.push($(this).attr("value"));
                flag = true;
            }
        });
        if(flag){
            $('.file-option1').show();
        }else{
            $('.file-option1').hide();
        }
    });

    $('#copy-btn').click(function(){
        var url = $(this).attr('url');
        optionFiles = new Array();
        $(':checkbox').each(function(){
            if($(this).is(':checked')){
                optionFiles.push($(this).attr("value"));
            }
        });
        $.ajax({
            url:url,
            type:'post',
            data:{'files':optionFiles,'option':'copy'},
            dataType:'json',
            success:function(){
                $('.file-option1').hide();
                $('.file-option2').show();
            }
        });
    });


    $('#cut-btn').click(function(){
        var url = $(this).attr('url');
        optionFiles = new Array();
        $(':checkbox').each(function(){
            if($(this).is(':checked')){
                $(this).parent().parent().parent().hide();
                optionFiles.push($(this).attr("value"));
            }
        });

        $.ajax({
            url:url,
            type:'post',
            data:{'files':optionFiles,'option':'cut'},
            dataType:'json',
            success:function(){
                $('.file-option1').hide();
            }
        });
    });

    $('#paste-btn').click(function(){
        var url = $(this).attr('url');
        $.ajax({
            url:url,
            type:'get',
            data:{},
            dataType:'json',
            success:function(result){
                if(result['msg'] == 'success'){
                    location.reload();
                }else{
                    alert(result['msg']);
                }

            }
        });
    });

    $('#delete-files-btn').click(function(){
        var url = $(this).attr('url');
        optionFiles = new Array();
        $(':checkbox').each(function(){
            if($(this).is(':checked')){
                optionFiles.push($(this).attr("value"));
            }
        });
        $.ajax({
            url:url,
            type:'post',
            data:{'files':optionFiles},
            dataType:'json',
            success:function(result){
                //console.log(result[0]);
                $(':checkbox').each(function(){
                    if($(this).is(':checked')){
                        $(this).parent().parent().parent().remove();
                        var disk = result['disk'];
                        var present = ((disk['capacity']-disk['available_size'])/disk['capacity'])*100;
                        $('#webdb-size > div').css('width',present+"%");
                        $('#p-capacity').text(Math.round((disk['capacity']/(1024*1024*1024))*100)/100);
                        $('#p-available').text(Math.round((disk['available_size']/(1024*1024*1024))*100)/100);
                    }
                });
            }
        });
    });

    $('#recycle-files-btn').on('click',function(){
        var url = $(this).attr('url');
        optionFiles = new Array();
        $(':checkbox').each(function(){
            if($(this).is(':checked')){
                optionFiles.push($(this).attr('value'));
            }
        });
        $.ajax({
            url:url,
            type:'post',
            data:{'files':optionFiles},
            dataType:'json',
            success:function(data){
                if(data['code'] == '0'){
                    $(':checkbox').each(function(){
                        if($(this).is(':checked')){
                            $(this).parent().parent().parent().remove();
                        }
                    });
                }else{
                    alert(data['msg']);
                }
            }
        });
    });

    $('.tr-file').dblclick(function(){
        var item = '<input type="text" placeholder="输入新文件名">&nbsp;&nbsp;<button class="rename-yes">确定</button>&nbsp;&nbsp;<button class="rename-no">取消</button>';
        var baseUrl = $(this).attr('base-url');
        $(this).find('label').after(item);
        $(this).find('.rename-no').on('click',function(){
            $(this).parent().find(':text').remove();
            $(this).parent().find('.rename-yes').remove();
            $(this).remove();
        });
        $(this).find('.rename-yes').on('click',function(){
            var newName = $(this).parent().find(':text').val();
            var item1 = $(this).parent().find(':text');
            var item2 = $(this).parent().find('.rename-no');
            var item3 = $(this);
            var item4 = $(this).parent().find('label').children('.span-file-name');
            if(newName != ""){
                var url = baseUrl + '/index.php?r=file/rename';
                var recordId = $(this).parent().find(':checkbox').val();
                $.ajax({
                    url:url,
                    type:'post',
                    data:{'record_id':recordId,'new_name':newName},
                    dataType:'json',
                    success:function(result){
                        if(result['code'] == '0'){
                            item1.remove();
                            item2.remove();
                            item3.remove();
                            item4.text(result['file_name']);
                        }
                    }
                });

            }else{
                alert("请输入新文件名!");
            }
        });
    });

});