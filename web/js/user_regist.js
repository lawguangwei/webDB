/**
 * Created by 211q1111111luoguangwei on 16/1/27.
$(document).ready(function(){
    /**$("#password2").blur(function(){
        var password1 = $("#password1").val();
        var password2 = $("#password2").val();
        if(password1 != password2){
            alert("wrong");
        }
    });
});

*/
var url_1 = "http://localhost/webdb/web/index.php?";
window.onload = function () {
    $('#text_email').blur(function(){
        var email = $(this).val();
        var csrf = $('#text_csrf').val();

        $.ajax({
            type:"POST",
            url:url_1+"r=user/register",
            data:{"email":email,"_csrf":csrf,"option":"1"},
            dataType:"json",
            success:function(result){
                if(result['exist'] == "1"){
                    $("#text_email").popover('show');
                }else{
                    $("#text_email").popover('hide');
                }
            },
            error:function(XMLHttpRequest, textStatus, errorThrown){
                console.log(XMLHttpRequest.status);//200客户端请求已成功
                console.log(XMLHttpRequest.readyState);//4响应内容解析完成，可以在客户端调用了
                console.log(XMLHttpRequest.responseText);
                console.log(textStatus);//parsererror
            }
        });
    });
}