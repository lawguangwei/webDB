<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/6
 * Time: 13:05
 */
?>


<div id="file-panel" class="row">
    <div class="col-md-12">
        <table id="file-table" class="table">
            <tr id="list-header">
                <td id="header-file-name">
                    文件名
                    <a id='recycle-files-btn'class="file-option1" url="<?=\yii\helpers\Url::base().'/index.php?r=file/revert'?>" hidden>还原</a>
                </td>
                <td>大小</td>
                <td>删除日期</td>
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
                        </td>
                        <td><?= round($file['file_size']/(1024*1024),2) ?>MB</td>
                        <td><?= $file['remove_date'] ?></td>
                    </tr>
                <?php }
            }
            ?>
        </table>
    </div>
</div>
