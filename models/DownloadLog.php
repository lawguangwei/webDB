<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/7
 * Time: 21:45
 */
namespace app\models;
use yii\db\ActiveRecord;

class DownloadLog extends ActiveRecord{
    private $d_log_id;
    private $user_id;
    private $file_id;
    private $download_date;

    public static function tableName(){
        return 'download_log';
    }

    public function rules(){
        return [
            ['d_log_id','required','message'=>'id不能为空'],
            ['user_id','required','message'=>'用户不能为空'],
            ['file_id','required','message'=>'文件id不能为空'],
            ['download_date','required','message'=>'下载日期不能为空'],
        ];
    }
}