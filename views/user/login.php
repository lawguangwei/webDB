<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 15/11/4
 * Time: 15:02
 */
$this->title = "用户登录";

use app\assets\LoginAsset;
LoginAsset::register($this);
?>
<div class="container-fluid">
    <?php
    if(isset($error)){ ?>
        <div class="row">
            <div class="col-md-6 col-md-offset-3 alert alert-danger alert-dismissible fade in">
                <a href="#" class="close" data-dismiss="alert">
                    &times;
                </a>
                <strong>警告！</strong><?=$error?>
            </div>
        </div>
    <?php }
    ?>

    <div class="row">
        <h3 class="text-info" style="text-align: center;text-shadow: 1px 1px 1px #999">用户登录</h3>
    </div>
    <div class="row div-form">
        <form id="login-form" action="<?=\yii\helpers\Url::base().'/index.php?r=user/login'?>"
              method="post" class="form-horizontal col-md-4 col-md-offset-4">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
            <div class="form-group">
                <div class="col-md-8 col-md-offset-2 input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
                    <input class="form-control" type="email" name="email" placeholder="请输入用户邮箱">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-8 col-md-offset-2 input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                    <input class="form-control" type="password" name="password" placeholder="请输入密码">
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-default col-md-8 col-md-offset-2">登录</button>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <p class="p-right"><a href="<?=\yii\helpers\Url::base()."?r=user/register"?>">注册</a></p>
                </div>
            </div>
        </form>
    </div>
</div>
