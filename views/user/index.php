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
    <div class="col-md-8">
        <div id="webdb-size" capacity="<?=$disk['capacity']?>" available-size="<?=$disk['available_size']?>" class="progress">
            <div class="progress-bar progress-bar-info progress-bar-striped">
            </div>
        </div>
    </div>
    <p class="col-md-2"><b>总容量: </b><?=round($disk['capacity']/(1024*1024*1024))?>GB&nbsp;&nbsp;<b>剩余空间: </b><?=round($disk['available_size']/(1024*1024*1024),4)?>GB</p>
    <p class="col-md-12">
        <?php
        if($_SESSION['current_path'] != 'root'){?>
            <a href="<?=\yii\helpers\Url::base().'/index.php?r=file/cd&f_id='.$_SESSION['parent_id']?>"><span class="glyphicon glyphicon-chevron-left"></span></a>
        <?php }
        ?>
        当前位置: <?=$_SESSION['current_path']?>
    </p>
</div>

<div class="row">

</div>

<div id="file-panel" class="row">
    <table id="file-table" class="table">
        <tr id="list-header">
            <td id="header-file-name">
                <label class="checkbox-inline">
                    <input type="checkbox" id="inlineCheckbox1" value="option1">&nbsp;文件名
                </label>
            </td>
            <td>大小</td>
            <td>修改日期</td>
        </tr>
        <?php
        if(isset($files)) {
            foreach ($files as $file) {
                if ($file['f_record_type'] == '2') { ?>
                    <tr class="tr-file">
                        <td>
                            <label class="checkbox-inline">
                                <input type="checkbox" id="inlineCheckbox1"
                                       value="option1">&nbsp;
                                <span class="glyphicon glyphicon-folder-open"></span>
                                <a href="<?=\yii\helpers\Url::base().'/index.php?r=file/cd&f_id='.$file['f_record_id']?>"><?= $file['file_name'] ?></a>
                            </label>

                            <div class="td-btns" style="display: none">
                                <a class="btn-delete" file-id="<?=$file['f_record_id']?>"
                                   url="<?=\yii\helpers\Url::base().'/index.php?r=file/delete-folder'?>"
                                   csrf="<?=Yii::$app->request->csrfToken?>">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>
                            </div>
                        </td>

                        <td><?= round($file['file_size']/(1024*1014),2) ?>MB</td>
                        <td><?= $file['upload_date'] ?></td>
                    </tr>
                <?php } else {
                    ?>
                    <tr class="tr-file">
                        <td>
                            <label class="checkbox-inline">
                                <input type="checkbox" id="inlineCheckbox1"
                                       value="option1">&nbsp;
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
                                   url="<?= \yii\helpers\Url::base().'/index.php?r=file/getfile&file_id='.$file['file_id']?>">
                                    <span class="glyphicon glyphicon-download-alt"></span>
                                </a>&nbsp;
                                <a class="btn-delete" file-id="<?=$file['file_id']?>"
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

<form id="download-form" class="hidden" method="get">
    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
    <input id="download-id" type="hidden" name="file_id">
</form>

<form id="delete-form" class="hidden" method="post">
    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
    <?header('Accept-Ranges: bytes')?>
    <input id="delete-id" type="hidden" name="file_id">
</form>


