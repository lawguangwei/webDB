/**
 * Created by luoguangwei on 16/3/3.
 */
$(function(){
    $('#name-sure').click(function(){
        if($('#input-name').val() == ''){
            alert('请输入用户名');
        }else{
            $('#form-modify-info').submit();
        }
    });

    $('#pass-sure').click(function(){
        if($('#input-new-pass').val() == ''){
            return alert('请输入新密码');
        }
        if($('#input-old-pass').val() == ''){
            return alert('请输入原密码');
        }
        $('#form-modify-pass').submit();
    });
})