<link href="css/user_index.css" rel="stylesheet" xmlns="http://www.w3.org/1999/html"/>


<div id="file-panel" class="row">
    <div class="col-md-12">
        <table id="file-table" class="table">
            <tr id="list-header">
                <td id="header-file-name">
                    文件名
                    <a id='delete-files-btn'class="file-option1" url="<?=\yii\helpers\Url::base().'/index.php?r=file/delete-files'?>"hidden>删除</a>
                </td>
                <td>大小</td>
                <td>修改日期</td>
            </tr>
            <?php
            if(isset($files)) {
                foreach ($files as $file) { ?>
                    <tr class="tr-file" base-url="<?=\yii\helpers\Url::base()?>">
                        <td>
                            <label class="checkbox-inline">
                                <input type="checkbox" value="<?=$file['f_record_id']?>">&nbsp;
                                <span class="glyphicon glyphicon-file"></span>
                                <span class="span-file-name"><?= $file['file_name']?></span>
                            </label>
                            <div class="td-btns" style="display: none">
                                <a class="btn-download" file-id="<?= $file['file_id'] ?>"
                                   url="<?= \yii\helpers\Url::base() . '/index.php?r=file/getfile'?>"
                                   csrf="<?= Yii::$app->request->csrfToken ?>">
                                    <span class="glyphicon glyphicon-download-alt"></span>
                                </a>&nbsp;
                                <a class="btn-delete" record-id="<?=$file['f_record_id']?>"
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
            ?>
        </table>
    </div>
</div>


