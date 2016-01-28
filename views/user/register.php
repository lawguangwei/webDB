<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 15/11/5
 * Time: 20:11
 */
$this->title = "用户注册";

use app\assets\RegisterAsset;
RegisterAsset::register($this);

?>
<div class="container-fluid">


    <?php
    if(isset($errors)){
        foreach($errors as $errs){
            foreach($errs as $err){?>
                <div class="col-md-6 col-md-offset-3 alert alert-danger alert-dismissible fade in">
                    <a href="#" class="close" data-dismiss="alert">
                        &times;
                    </a>
                    <strong>警告！</strong><?=$err?>
                </div>
        <?php }
        }
    }
    ?>

    <div class="row">
        <form id="login-form" action="<?=\yii\helpers\Url::base()."/index.php?r=user/register"?>" method="POST" class="form-horizontal col-md-4 col-md-offset-4">
            <input id="text_csrf" type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
            <div class="form-group">
                <div class="col-md-8 col-md-offset-2 input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon glyphicon-envelope"></span></span>
                    <input id="text_email" url="<?=\yii\helpers\Url::base()."/index.php?r=user/check-email"?>"
                           class="form-control" type="email" name="user_email" placeholder="请输入邮箱">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-8 col-md-offset-2 input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                    <input class="form-control" name="user_name" type="text" placeholder="用户名">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-8 col-md-offset-2 input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                    <input id="password1" class="form-control" name="password1" type="password" placeholder="请输入密码">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-8 col-md-offset-2 input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                    <input id="password2" class="form-control" name="password2" type="password" placeholder="请再输入密码">
                </div>
            </div>
            <div class="form-group">
                <button id="btn-register" class="btn btn-default col-md-8 col-md-offset-2">注册</button>
            </div>
        </form>
    </div>
</div>

