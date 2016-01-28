/**
 * Created by 211q1111111luoguangwei on 16/1/27.
 */
$(document).ready(function(){
    /**$("#password2").blur(function(){
        var password1 = $("#password1").val();
        var password2 = $("#password2").val();
        if(password1 != password2){
            alert("wrong");
        }
    });*/
    $('#text_email').blur(function(){
        var url_1 = $(this).attr('url');
        var email = $(this).val();
        var csrf = $("#text_csrf").val();
        jQuery.ajax({
            type:"POST",
            url:url_1,
            data:{"email":email,"_csrf":csrf},
            dataType:"json",
            success : function(data){
                if(data["result"] == '0'){
                    alert('0');
                }else{
                    alert('1');
                }
            }
        });
    });
});