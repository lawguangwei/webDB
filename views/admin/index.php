<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/3
 * Time: 20:41
 */
?>

<nav class="navbar navbar-inverse" style="margin-bottom: 0px;">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">WEBDB 后台管理系统</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=$_SESSION['admin']['admin_account']?> <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?=\yii\helpers\Url::base().'/index.php?r=admin/logout'?>">退出</a></li>
                    </ul>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>

<div class="panel-group" id="admin-index" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingBasic">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#admin-index" href="#base-info" aria-expanded="true" aria-controls="base-info">
                    网站基本运营信息
                </a>
            </h4>
        </div>
        <div id="base-info" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingBasic">
            <div class="panel-body">
                <div class="col-md-12 well">
                    <div class="col-md-4">
                        <h4>用户数 <span id="span-user-num" class="label label-info"></span></h4>
                    </div>
                    <div class="col-md-4">
                        <h4>文件数量 <span id="span-file-num" class="label label-info"></span></h4>
                    </div>
                    <div class="col-md-4">
                        <h4>文件库大小 <span id="span-file-size" class="label label-info"></span></h4>
                    </div>
                </div>
                <div class="col-md-12">
                    <div id="statistics-btn-group" class="row">
                        <button class="btn btn-success col-md-2 col-md-offset-3" aria-statistics="1">网站热度</button>
                        <button class="btn col-md-2" aria-statistics="2">容量变动</button>
                        <button class="btn col-md-2" aria-statistics="3">用户统计</button>
                    </div>
                    <div class="row well">
                        <div id="statistics" class="col-md-10 col-md-offset-1"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingLoginLog">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#admin-index" href="#login-log" aria-expanded="false" aria-controls="collapseTwo">
                   登录记录
                </a>
            </h4>
        </div>
        <div id="login-log" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingLoginLog">
            <table class="table table-hover">
                <tr>
                    <td>#</td>
                    <td>登录ip</td>
                    <td>登录地点</td>
                    <td>登录时间</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingThree">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#admin-index" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                用户设置
                </a>
            </h4>
        </div>
        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
            <div class="panel-body">
                <div id="div-query-user" class="col-md-12">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <input type="text" id="input-email" class="form-control" placeholder="输入用户账号">
                        </div>
                        <button id="btn-query-user" class="btn btn-success">查询</button>
                        <button class="btn btn-success user-option" data-toggle="modal" data-target="#modal-set-user">禁用/启用</button>
                        <button class="btn btn-success user-option" data-toggle="modal" data-target="#modal-set-size">空间升级</button>
                    </div>
                    <div id="div-user-info" class="col-md-12" style="margin-top: 8px">
                        <div class="col-md-6">
                            <div class="col-md-12 well">
                                <div class="row">
                                    <label class="col-md-4 text-right" style="font-size:18px">账号:</label>
                                    <p class="col-md-8 text-left text-info p-email" style="font-size: 18px"></p>
                                </div>
                                <div class="row">
                                    <label class="col-md-4 text-right" style="font-size:18px">用户名:</label>
                                    <p id="p-name" class="col-md-8 text-left text-info" style="font-size: 18px"></p>
                                </div>
                                <div class="row">
                                    <label class="col-md-4 text-right" style="font-size:18px">创建日期:</label>
                                    <p id="p-date" class="col-md-8 text-left text-info" style="font-size: 18px"></p>
                                </div>
                                <div class="row">
                                    <label class="col-md-4 text-right" style="font-size:18px">空间大小:</label>
                                    <p id="p-size" class="col-md-8 text-left text-info" style="font-size: 18px"></p>
                                </div>
                                <div class="row">
                                    <label class="col-md-4 text-right" style="font-size:18px">账号状态:</label>
                                    <p id="p-state" class="col-md-8 text-left" style="font-size: 18px"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <table id="table-um-user" class="table table-condensed table-striped">
                                <tr>
                                    <td>#</td>
                                    <td>操作类型</td>
                                    <td>管理员</td>
                                    <td>时间</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingManageFile">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#admin-index" href="#manage-file" aria-expanded="true" aria-controls="manage-file">
                    文件管理
                </a>
            </h4>
        </div>
        <div id="manage-file" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingManageFile">
            <div class="panel-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#file-1" aria-controls="user-1" role="tab" data-toggle="tab">最多次下载文件</a></li>
                    <li role="presentation"><a href="#file-2" aria-controls="profile" role="tab" data-toggle="tab">最多人下载文件</a></li>
                    <li role="presentation"><a href="#file-3" aria-controls="profile" role="tab" data-toggle="tab">已处理文件</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="file-1">
                        <table id="table-file-1" class="table table-striped">
                            <tr>
                                <td>#</td>
                                <td>文件id</td>
                                <td>文件类型</td>
                                <td>文件大小</td>
                                <td>下载次数</td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="file-2">
                        <table id="table-file-2" class="table table-striped">
                            <tr>
                                <td>#</td>
                                <td>文件id</td>
                                <td>文件类型</td>
                                <td>文件大小</td>
                                <td>人数</td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="file-3">
                        <table id="table-file-3" class="table table-striped">
                            <tr>
                                <td>#</td>
                                <td>文件id</td>
                                <td>文件类型</td>
                                <td>文件大小</td>
                                <td>管理员id</td>
                                <td>设置时间</td>
                            </tr>
                        </table>
                        <ul id="file-3-page" class="pagination">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>








<!-- Modal -->
<div class="modal fade" id="modal-set-user" tabindex="-1" role="dialog" aria-labelledby="setUserLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="disableUserLabel">账户</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="control-label">用户账户</label>
                            <p id="p-email" class="text-info p-email"></p>
                        </div>
                        <div class="col-md-12">
                            <label class="control-label">信息</label>
                            <textarea id="set-user-info" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-set-size" tabindex="-1" role="dialog" aria-labelledby="setSizeLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="setSizeLabel">用户空间升级</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="control-label">用户账户</label>
                            <p id="p-size-email" class="text-info p-email"></p>
                        </div>
                        <div class="col-md-12">
                            <label class="control-label">增加空间大小(GB)</label>
                            <input id="input-size" type="text" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="control-label">信息</label>
                            <textarea id="set-size-info" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-set-file" tabindex="-1" role="dialog" aria-labelledby="setFileLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="setFileLabel">禁用文件</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="control-label">文件id</label>
                            <p id="p-file-id" class="text-info p-file-id"></p>
                        </div>
                        <div class="col-md-12">
                            <label class="control-label">文件类型</label>
                            <p id="p-file-type" class="text-info p-file-type"></p>
                        </div>
                        <div class="col-md-12">
                            <label class="control-label">文件大小</label>
                            <p id="p-file-size" class="text-info p-file-size"></p>
                        </div>
                        <div class="col-md-12">
                            <label class="control-label">信息</label>
                            <textarea id="set-file-info" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
            </div>
        </div>
    </div>
</div>
