
function setHeight(){
    var height = $(window).height();
    $('#lr-div').css({'height':height});
    $('#content-panel').css({'height':height});
}
function setWebdbSize(){
    var capacity = $('#webdb-size').attr('capacity');
    var avail = $('#webdb-size').attr('available-size');
    var present = ((capacity-avail)/capacity)*100;
    $('#webdb-size > div').css('width',present+"%");
}

$(function(){
    setHeight();
    setWebdbSize();

    $('#lr-bar > li').hover(function(){
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

    var myXhr;
    $('#modal-upload-btn').click(function(){
        //$('#form-upload-file').submit();
        $(this).hide();
        var formData = new FormData($("#form-upload-file")[0]);
        var csrf = $(this).attr("csrf");
        var url = $(this).attr("url");
        var item = '<div class="col-md-12">' +
            '<p class="col-md-12" style="word-break: break-all">'+$('#file-input').val()+'</p>' +
            '<button type="button" class="close"><span>&times</span></button>' +
            '<progress id="upload-progress2" class="col-md-12" value="0" max="100"></progress>';
        $('#lr-bar').after(item);
        $('#lr-div').find('button').on('click',function(){
            myXhr.abort();
            $(this).parent().remove();
            $('#modal-upload-btn').show();
            $('#upload-progress').css('width',"0");
            $('#upload-progress2').attr({'value':'0','max':100});
        });
        $('#file-input').val('');
        $.ajax({
            url:url,
            type:'post',
            xhr:function(){
                myXhr = $.ajaxSettings.xhr();
                if(myXhr.upload){
                    myXhr.upload.addEventListener('progress',progressHandlingFunction, false);
                }
                return myXhr;
            },
            data:formData,
            error:function(){
                alert("error");
            },
            success:function(){
                location.reload();
            },
            cache:false,
            contentType:false,
            processData:false
        });
    });
    $("#cancel-upload-btn").click(function(){
        if(myXhr != null){
            myXhr.abort();
            $('#modal-upload-btn').show();
            $('#upload-progress').css('width',"0");
            $('#upload-progress2').attr({'value':'0','max':100});
        }
    });
    function progressHandlingFunction(e){
        //console.log(e.loaded*100/e.total+"%");
        $("#upload-progress").css("width",e.loaded*100/e.total+"%");
        $("#upload-progress2").attr({"value":e.loaded,"max":e.total});
    }



    $('#modal-mkdir-btn').click(function(){
        $('#form-mkdir').submit();
    });

    $('.btn-download').click(function(){
        var file_id = $(this).attr('file-id');
        $('#download-id').val(file_id);
        $('#download-form').submit();
    });


    $('.btn-delete').click(function(){
        var file_id = $(this).attr('file-id');
        var url = $(this).attr('url');
        $('#delete-id').val(file_id);
        $("#delete-form").attr('action',url);
        $('#delete-form').submit();
    })

    var optionFiles = new Array();
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
                location.reload();
            }
        });
    });

    $('#delete-files-btn').click(function(){
        var url = $(this).attr('url');
        optionFiles = new Array;
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
                location.reload();
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
                            location.reload();
                        }
                    }
                });

            }else{
                alert("请输入新文件名!");
            }
        });
    });

});