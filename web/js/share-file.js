/**
 * Created by luoguangwei on 16/3/9.
 */
$(function(){
    setTableCode();

    $('#btn-get-file').on('click',function(){
        var code = $('#input-code').val();
        $.ajax({
            type:'post',
            url:'index.php?r=file/get-code-file',
            data:{'code':code},
            dataType:'json',
            success:function(data){
                if(data['code'] == '0'){
                    var fileId= data['file_id'];
                    window.open('index.php?r=file/getfile&file_id='+fileId);
                }else{
                    alert(data['msg']);
                }
            }
        });
    });
})

function setTableCode(){
    $.ajax({
        type:'get',
        url:'index.php?r=file/share-file-list',
        data:{},
        dataType:'json',
        success:function(data){
            setCodeList(data);
        }
    });
}

function setCodeList(codes){
    var i=1;
    $('table').find('.tr-code').remove();
    codes.forEach(function(code){
        var content = '<tr class="tr-code">' +
            '<td>'+ i++ +'</td>' +
            '<td>'+code['file_name']+'</td>' +
            '<td>'+code['file_type']+'</td>' +
            '<td>'+Math.round((code['file_size']/(1024*1024))*100)/100+'MB</td>' +
            '<td>'+code['code']+'</td>' +
            '<td>'+code['create_date']+'</td>' +
            '<td><button class="btn-delete-code btn-danger" code-id="'+code['code_id']+'">删除</button></td>' +
            '</tr>';
        $('table').append(content);
    });
    $('.btn-delete-code').off('click');
    $('.btn-delete-code').on('click',function(){
        var codeId = $(this).attr('code-id');
        $.ajax({
            type:'post',
            url:'index.php?r=file/delete-share-code',
            data:{'code_id':codeId},
            dataType:'json',
            success:function(data){
                if(data['code'] == '0'){
                    setTableCode();
                }else{
                    alert('删除失败');
                }
            }
        });
    });
}