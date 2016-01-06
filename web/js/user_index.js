function setHeight(){
    var height = $(window).height();
    $('#lr-bar').css({'height':height});
    $('#content-panel').css({'height':height});
}

$(function(){
    setHeight();

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

    $('#modal-upload-btn').click(function(){
        $('#form-upload-file').submit();
    });
    $('#modal-mkdir-btn').click(function(){
        $('#form-mkdir').submit();
    });

    $('.btn-download').click(function(){
        var file_id = $(this).attr('file-id');
        $('#download-id').val(file_id);
        $('#download-form').submit();
        /*var csrf = $(this).attr('csrf');
        $.ajax({
            type:'post',
            url:url,
            data:{'_csrf':csrf,'file_id':file_id},
            dataType:'json',
            success:function(){}
        });*/
    });

    $('.btn-delete').click(function(){
        var file_id = $(this).attr('file-id');
        $('#delete-id').val(file_id);
        $('#delete-form').submit();
    })
});