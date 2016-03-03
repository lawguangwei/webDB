<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/2
 * Time: 14:44
 */
use app\assets\InfoAsset;
InfoAsset::register($this);
$this->title = '用户设置';
?>
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
if(isset($msg)){ ?>
    <div class="col-md-6 col-md-offset-3 alert alert-success alert-dismissible fade in">
        <a href="#" class="close" data-dismiss="alert">
            &times;
        </a>
        <strong>提醒</strong><?=$msg?>
    </div>
<?php }
?>


<div class="col-md-8 col-md-offset-2">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">账户信息<a class="btn btn-default" href="<?=\yii\helpers\Url::base().'/index.php?r=user/index'?>">返回</a></h3>
        </div>
        <div id='person-info' class="panel-body">
            <div class="row">
                <div class="col-md-2 text-info" style="text-align: right"><b>用户登录账户:</b></div>
                <div class="col-md-10" style="text-align: left"><?=$_SESSION['user']['user_email']?></div>
            </div>
            <div class="row">
                <div class="col-md-2 text-info" style="text-align: right"><b>用户名:</b></div>
                <div class="col-md-10" style="text-align: left"><?=$_SESSION['user']['user_name']?></div>
            </div>
            <div class="row">
                <div class="col-md-2 text-info" style="text-align: right"><b>创建日期:</b></div>
                <div class="col-md-10" style="text-align: left"><?=$_SESSION['user']['create_date']?></div>
            </div>
            <div class="row">
                <button class="btn btn-primary col-md-2 col-md-offset-6" data-toggle="modal" data-target="#modify-info">修改用户名</button>
                <button class="btn btn-primary col-md-2 col-md-offset-1" data-toggle="modal" data-target="#modify-password">修改密码</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modify-info" tabindex="-1" role="dialog" aria-labelledby="modifyLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modifyLabel">创建新文件夹</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id='form-modify-info' class="form-horizontal" action="<?=\yii\helpers\Url::base().'/index.php?r=user/modify-info'?>" method="post">
                        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
                        <div class="input-group col-md-12">
                            <label class="control-label col-md-2">用户名:</label>
                            <div class="col-md-8">
                                <input id="input-name" class="form-control" name="user_name" type="text" placeholder="<?=$_SESSION['user']['user_name']?>">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button id="name-sure" type="button" class="btn btn-primary">确定</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modify-password" tabindex="-1" role="dialog" aria-labelledby="modifyPassword">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modifyPassword">创建新文件夹</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id='form-modify-pass' class="form-horizontal" action="<?=\yii\helpers\Url::base().'/index.php?r=user/modify-password'?>" method="post">
                        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
                        <div class="input-group col-md-12">
                            <label class="control-label col-md-2">旧密码:</label>
                            <div class="col-md-8">
                                <input id="input-old-pass" class="form-control" name="old_password" type="password">
                            </div>
                        </div>
                        <br/>
                        <div class="input-group col-md-12">
                            <label class="control-label col-md-2">新密码:</label>
                            <div class="col-md-8">
                                <input id="input-new-pass" class="form-control" name="new_password" type="password">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button id='pass-sure' type="button" class="btn btn-primary">确定</button>
            </div>
        </div>
    </div>
</div>

