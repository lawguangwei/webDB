<link href="css/user_index.css" rel="stylesheet"/>
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
    <p>全部文件</p>
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
                if ($file['file_type'] == '2') { ?>
                    <tr class="tr-file">
                        <td>
                            <label class="checkbox-inline">
                                <input type="checkbox" id="inlineCheckbox1"
                                       value="option1">&nbsp;
                                <span class="glyphicon glyphicon-folder-open"></span>
                                <a href=""><?= $file['file_name'] ?></a>
                            </label>

                            <div class="td-btns" style="display: none">
                                <a class="btn-download" file-id="<?= $file['file_id'] ?>"
                                   url="<?= \yii\helpers\Url::base() . '/index.php?r=file/getfile' ?>"
                                   csrf="<?= Yii::$app->request->csrfToken ?>">
                                    <span class="glyphicon glyphicon-download-alt"></span>
                                </a>&nbsp;
                                <a class="btn-delete" file-id="<?=$file['file_id']?>"
                                    url="<?=\yii\helpers\Url::base().'/index.php?r=file/delete-file'?>"
                                    csrf="<?=Yii::$app->request->csrfToken?>">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>
                            </div>
                        </td>

                        <td><?= floor($file['file_size'] / 1024) ?>KB</td>
                        <td><?= $file['created_date'] ?></td>
                    </tr>
                <?php } else {
                    ?>
                    <tr class="tr-file">
                        <td>
                            <label class="checkbox-inline">
                                <input type="checkbox" id="inlineCheckbox1"
                                       value="option1">&nbsp;
                                <?php
                                if($file['file_extend'] == 'image/jpeg'){ ?>
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
                                   url="<?= \yii\helpers\Url::base() . '/index.php?r=file/getfile' ?>"
                                   csrf="<?= Yii::$app->request->csrfToken ?>">
                                    <span class="glyphicon glyphicon-download-alt"></span>
                                </a>&nbsp;
                                <a class="btn-delete" file-id="<?=$file['file_id']?>"
                                   url="<?=\yii\helpers\Url::base().'/index.php?r=file/delete-file'?>"
                                   csrf="<?=Yii::$app->request->csrfToken?>">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>
                            </div>
                        </td>
                        <td><?= floor($file['file_size'] / 1024) ?>KB</td>
                        <td><?= $file['created_date'] ?></td>
                    </tr>
            <?php }
            }
        }
        ?>
    </table>
</div>

<form id="download-form" class="hidden" action="<?=\yii\helpers\Url::base().'/index.php?r=file/getfile'?>" method="post">
    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
    <input id="download-id" type="hidden" name="file_id">
</form>

<form id="delete-form" class="hidden" action="<?=\yii\helpers\Url::base().'/index.php?r=file/delete-file'?>" method="post">
    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
    <?header('Accept-Ranges: bytes')?>
    <input id="delete-id" type="hidden" name="file_id">
</form>


