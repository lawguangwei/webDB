<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/9
 * Time: 14:21
 */
use app\assets\ShareAsset;
ShareAsset::register($this);
?>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">文件分享</h3>
    </div>
    <div class="panel-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-1">
                    <a href="<?=\yii\helpers\Url::base().'/index.php?r=user/index'?>" class="btn btn-success">返回</a>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <input id="input-code" type="text" class="form-control" placeholder="输入提取码">
                        <span class="input-group-btn">
                            <button id="btn-get-file" class="btn btn-default" type="button">提取文件</button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <label class="text-info"><h3>分享列表</h3></label>
                    <table class="table table-bordered">
                        <tr class="active">
                            <td>#</td>
                            <td>文件名</td>
                            <td>文件类型</td>
                            <td>文件大小</td>
                            <td>分享码</td>
                            <td>创建日期</td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
