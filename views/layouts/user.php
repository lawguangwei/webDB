<?php

/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <script type="text/javascript" src="js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <link href="css/index_layout.css" rel="stylesheet">
    <script type="text/javascript" src="js/user_index.js"></script>
    <link href="css/user_index.css" rel="stylesheet"/>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<nav class="navbar navbar-inverse">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">&nbsp;WEB网盘</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <form class="navbar-form navbar-left" role="search">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="搜索你的文件">
                </div>
                <button type="submit" class="btn btn-default">搜索</button>
            </form>

            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=$_SESSION['user']['user_name']?> <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">个人资料</a></li>
                        <li><a href="#">设置</a></li>
                        <li><a href="#">容量统计</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="<?=\yii\helpers\Url::base().'/index.php?r=user/logout'?>">退出登录</a></li>
                    </ul>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>

<div id="main-panel" class="container-fluid">
    <div id="lr-div" class="col-md-2">
        <ul id="lr-bar" class="col-md-12" li-option="<?=$_SESSION['li_option']?>">
            <a href="<?=\yii\helpers\Url::base().'/index.php?r=user/index'?>"><li id="li-index"><span class="glyphicon glyphicon-th-large"></span>&nbsp;&nbsp;全部文件</li></a>
            <a href="<?=\yii\helpers\Url::base().'/index.php?r=file/select-file&type=picture'?>"><li id="li-picture"><span class="glyphicon glyphicon-picture"></span>&nbsp;&nbsp;图片</li></a>
            <a href="<?=\yii\helpers\Url::base().'/index.php?r=file/select-file&type=word'?>"><li id="li-word"><span class="glyphicon glyphicon-file"></span>&nbsp;&nbsp;文档</li></a>
            <a href="<?=\yii\helpers\Url::base().'/index.php?r=file/select-file&type=film'?>"><li id="li-film"><span class="glyphicon glyphicon-film"></span>&nbsp;&nbsp;视频</li></a>
            <a href="<?=\yii\helpers\Url::base().'/index.php?r=file/select-file&type=music'?>"><li id="li-music"><span class="glyphicon glyphicon-music"></span>&nbsp;&nbsp;音乐</li></a>
            <a href="<?=\yii\helpers\Url::base().'/index.php?r=file/select-file&type=other'?>"><li id="li-other"><span class="glyphicon glyphicon-option-horizontal"></span>&nbsp;&nbsp;其它</li></a>
            <li role="separator" class="divider"></li>
            <a href="<?=\yii\helpers\Url::base().'/index.php?r=file/select-file&type=delete'?>"><li id="li-delete"><span class="glyphicon glyphicon-trash"></span>&nbsp;&nbsp;回收站</li></a>
            <li role="separator" class="divider"></li>
        </ul>

        <div id='progress-div' class="col-md-12">
            <progress class="col-md-12" value="0" max="100"></progress>
            <button type="button" class="close"><span>&times</span></button>
        </div>
        <div id='upload-list-div' class="col-md-12">
        </div>

    </div>

    <div id="content-panel" class="col-md-10">
        <?=$content?>
    </div>
</div>

<!-- Modal Upload File-->
<div class="modal fade" id="modal-upload" tabindex="-1" role="dialog" aria-labelledby="uploadFileLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="uploadFileLabel">上传文件</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id='form-upload-file' class="form-horizontal" action="<?=\yii\helpers\Url::base().'/index.php?r=file/upload'?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
                        <input id="file-input" type="file" name="file" class="hidden">
                        确定上传文件:<br>
                        <span class="glyphicon glyphicon-file"></span><span id="span-filename"></span>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button id="cancel-upload-btn" type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button id='modal-upload-btn' type="button" class="btn btn-primary" csrf="<?=Yii::$app->request->csrfToken?>"
                    url="<?=\yii\helpers\Url::base().'/index.php?r=file/upload'?>"  data-dismiss="modal">确定</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Mkdir -->
<div class="modal fade" id="modal-mkdir" tabindex="-1" role="dialog" aria-labelledby="mkdirLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mkdirLabel">创建新文件夹</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id='form-mkdir' class="form-horizontal" action="<?=\yii\helpers\Url::base().'/index.php?r=file/mkdir'?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
                        <div class="input-group col-md-12">
                            <label class="control-label col-md-2">文件夹名:</label>
                            <div class="col-md-8">
                                <input class="form-control" name="dir-name" type="text" placeholder="新建文件夹">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button id='modal-mkdir-btn'type="button" class="btn btn-primary">确定</button>
            </div>
        </div>
    </div>
</div>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>


