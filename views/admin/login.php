<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/3
 * Time: 20:15
 */
$this->title = "管理员登录";

use app\assets\LoginAsset;
LoginAsset::register($this);

?>

<div class="container-fluid">
    <?php
    if(isset($error)){ ?>
        <div class="col-md-6 col-md-offset-3 alert alert-danger alert-dismissible fade in">
            <a href="#" class="close" data-dismiss="alert">
                &times;
            </a>
            <strong>警告！</strong><?=$error?>
        </div>
    <?php }
    ?>

    <div class="row">
        <form id="login-form" action="<?=\yii\helpers\Url::base().'/index.php?r=admin/login'?>"
              method="post" class="form-horizontal col-md-4 col-md-offset-4">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
            <div class="form-group">
                <div class="col-md-8 col-md-offset-2 input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
                    <input class="form-control" type="text" name="admin_account" placeholder="管理员账户">
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
                    <p class="p-left"><a href="#">忘记密码</a></p>
                    <p class="p-right"><a href="<?=\yii\helpers\Url::base()."?r=user/register"?>">注册</a></p>
                </div>
            </div>
        </form>
    </div>
</div>

