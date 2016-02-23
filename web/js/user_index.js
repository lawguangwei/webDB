function setHeight(){
    var height = $(window).height();
    $('#lr-bar').css({'height':height});
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
        }
    });
    function progressHandlingFunction(e){
        //console.log(e.loaded*100/e.total+"%");
        $("#upload-progress").css("width",e.loaded*100/e.total+"%");
        //$("#upload-progress").attr({"aria-valuenow":e.loaded,"aria-valuemax":e.total});
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
});