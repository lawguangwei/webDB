<link href="css/user_index.css" rel="stylesheet" xmlns="http://www.w3.org/1999/html"/>

<div id="tool-bar" class="row">
    <button id="upload-btn" class="btn btn-primary">
        <span class="glyphicon glyphicon-open"></span>&nbsp;&nbsp;上传文件
    </button>
    <button id="mkdir-btn" class="btn btn-default">
        <span class="glyphicon glyphicon-folder-open"></span>&nbsp;&nbsp;新建文件夹
    </button>
    <div id="tool-bar-left">
        <button class="btn btn-primary">
            <span class="glyphicon glyphicon-th-large"></span>
        </button>
        <button class="btn btn-default">
            <span class="glyphicon glyphicon-align-justify"></span>
        </button>
    </div>
</div>

<div id="location" class="row">
    <div class="col-md-12">
        <ol class="breadcrumb">
            <li>
                <?php
                if(isset($_SESSION['parent_id'])&&$_SESSION['parent_id']!='0'){?>
                    <a href="<?=\yii\helpers\Url::base().'/index.php?r=file/cd&f_id='.$_SESSION['parent_id']?>"><span class="glyphicon glyphicon-chevron-left"></span></a>
                <?php }
                ?>
            </li>
            <?php
            if(isset($_SESSION['current_path'])){
                $paths = $_SESSION['current_path'];
                while(next($paths)){
                    $path = array_pop($paths);
                    echo "<li><a href='".\yii\helpers\Url::base()."/index.php?r=file/cd&f_id=".$path['f_record_id']."'>".$path['name']."</a></li>";
                }
                echo "<li class='active'>".array_pop($paths)['name']."</li>";
            }
            ?>
        </ol>
    </div>
    <div class="col-md-8">
        <div id="webdb-size" capacity="<?=$disk['capacity']?>" available-size="<?=$disk['available_size']?>" class="progress">
            <div class="progress-bar progress-bar-info progress-bar-striped">
            </div>
        </div>
    </div>
    <p class="col-md-4"><b>总容量: </b><?=round($disk['capacity']/(1024*1024*1024))?>GB&nbsp;&nbsp;<b>剩余空间: </b><?=round($disk['available_size']/(1024*1024*1024),2)?>GB</p>
</div>

<div id="file-panel" class="row">
    <div class="col-md-12">
        <table id="file-table" class="table">
            <tr id="list-header">
                <td id="header-file-name">
                    文件名
                    <a id="copy-btn" class="file-option1" url="<?=\yii\helpers\Url::base().'/index.php?r=file/copy-files'?>"hidden>复制</a>
                    <a id="cut-btn" class="file-option1" url="<?=\yii\helpers\Url::base().'/index.php?r=file/copy-files'?>"hidden>剪切</a>
                    <a id='delete-files-btn'class="file-option1" url="<?=\yii\helpers\Url::base().'/index.php?r=file/delete-files'?>"hidden>删除</a>
                    <?php
                    if(isset($_SESSION['copy_files'])){ ?>
                        <a id="paste-btn" class="file-option2" url="<?=\yii\helpers\Url::base().'/index.php?r=file/paste-files'?>">粘贴</a>
                    <?php }
                    ?>
                </td>
                <td>大小</td>
                <td>修改日期</td>
            </tr>
            <?php
            if(isset($files)) {
                foreach ($files as $file) {
                    if ($file['f_record_type'] == '2') { ?>
                        <tr class="tr-file" base-url="<?=\yii\helpers\Url::base()?>">
                            <td>
                                <label class="checkbox-inline">
                                    <input type="checkbox" value="<?=$file['f_record_id']?>">&nbsp;
                                    <span class="glyphicon glyphicon-folder-open"></span>
                                    <a href="<?=\yii\helpers\Url::base().'/index.php?r=file/cd&f_id='.$file['f_record_id']?>"><?= $file['file_name']?></a>
                                </label>

                                <div class="td-btns" style="display: none">
                                    <a class="btn-delete" file-id="<?=$file['f_record_id']?>"
                                       url="<?=\yii\helpers\Url::base().'/index.php?r=file/delete-folder'?>"
                                       csrf="<?=Yii::$app->request->csrfToken?>">
                                        <span class="glyphicon glyphicon-remove"></span>
                                    </a>
                                </div>
                            </td>
                            <td><?= round($file['file_size']/(1024*1024),2) ?>MB</td>
                            <td><?= $file['upload_date'] ?></td>
                        </tr>
                    <?php } else {
                        ?>
                        <tr class="tr-file" base-url="<?=\yii\helpers\Url::base()?>">
                            <td>
                                <label class="checkbox-inline">
                                    <input type="checkbox" value="<?=$file['f_record_id']?>">&nbsp;
                                    <?php
                                    if($file['file_type'] == 'image/jpeg'){ ?>
                                        <span class="glyphicon glyphicon-picture"></span>
                                        <?php
                                    }else{ ?>
                                        <span class="glyphicon glyphicon-file"></span>
                                    <?php }
                                    ?>
                                    <?= $file['file_name'] ?>
                                </label>
                                <div class="td-btns" style="display: none">
                                    <a class="btn-download" file-id="<?= $file['file_id'] ?>"
                                       url="<?= \yii\helpers\Url::base() . '/index.php?r=file/getfile'?>"
                                       csrf="<?= Yii::$app->request->csrfToken ?>">
                                        <span class="glyphicon glyphicon-download-alt"></span>
                                    </a>&nbsp;
                                    <a class="btn-delete" file-id="<?=$file['f_record_id']?>"
                                       url="<?=\yii\helpers\Url::base().'/index.php?r=file/delete-file'?>"
                                       csrf="<?=Yii::$app->request->csrfToken?>">
                                        <span class="glyphicon glyphicon-remove"></span>
                                    </a>
                                </div>
                            </td>
                            <td><?= round($file['file_size']/(1024*1024),2) ?>MB</td>
                            <td><?= $file['upload_date'] ?></td>
                        </tr>
                    <?php }
                }
            }
            ?>
        </table>
    </div>
</div>

<form id="download-form" class="hidden" method="post" action="<?=\yii\helpers\Url::base().'/index.php?r=file/getfile'?>">
    <input id="download-id" type="hidden" name="file_id">
</form>

<form id="delete-form" class="hidden" method="post">
    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
    <?header('Accept-Ranges: bytes')?>
    <input id="delete-id" type="hidden" name="file_id">
</form>


