<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/2
 * Time: 14:44
 */
use app\assets\PersonAsset;
PersonAsset::register($this);
$this->title = '用户信息';
?>

<div class="col-md-8 col-md-offset-2 well">
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title">账户信息<a class="btn btn-default" href="<?=\yii\helpers\Url::base().'/index.php?r=user/index'?>">返回</a></h3>
        </div>
        <div id='person-info' class="panel-body">
            <div class="row">
                <div class="col-md-3 text-info" style="text-align: right"><b>用户登录账户:</b></div>
                <div class="col-md-9" style="text-align: left"><?=$_SESSION['user']['user_email']?></div>
            </div>
            <div class="row">
                <div class="col-md-3 text-info" style="text-align: right"><b>用户名:</b></div>
                <div class="col-md-9" style="text-align: left"><?=$_SESSION['user']['user_name']?></div>
            </div>
            <div class="row">
                <div class="col-md-3 text-info" style="text-align: right"><b>创建日期:</b></div>
                <div class="col-md-9" style="text-align: left"><?=$_SESSION['user']['create_date']?></div>
            </div>

        </div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">容量统计<a class="btn btn-default" href="<?=\yii\helpers\Url::base().'/index.php?r=user/index'?>">返回</a></h3>
        </div>
        <div id='static' class="panel-body">
            <div id="static1" class="col-md-6" capacity="<?=$disk['capacity']?>" available="<?=$disk['available_size']?>"></div>
            <div id="static2" class="col-md-6" picture="<?=$typeSize['picture']?>" word="<?=$typeSize['word']?>"
                 film="<?=$typeSize['film']?>" music="<?=$typeSize['music']?>" other="<?=$typeSize['other']?>"></div>
        </div>
    </div>

    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title">登录记录<a class="btn btn-default" href="<?=\yii\helpers\Url::base().'/index.php?r=user/index'?>">返回</a></h3>
        </div>
        <table class="table table-hover">
            <tr class="warning">
                <td>#</td>
                <td>登录ip</td>
                <td>登录地址</td>
                <td>登录日期</td>
            </tr>
            <?php
            $i =1;
            foreach($logs as $log){?>
                <tr>
                    <td><?=$i++?></td>
                    <td><?=$log['login_ip']?></td>
                    <td><?=$log['ip_address']?></td>
                    <td><?=$log['login_date']?></td>
                </tr>
            <?php }
            ?>
        </table>
    </div>
</div>
