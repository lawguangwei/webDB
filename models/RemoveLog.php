<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/6
 * Time: 10:35
 */
namespace app\models;

use yii\db\ActiveRecord;

class RemoveLog extends ActiveRecord{
    private $f_record_id;
    private $remove_date;

    public static function table(){
        return 'remove_log';
    }


    public function rules(){
        return [
            ['f_record_id','required','message'=>'文件id不能为空'],
            ['remove_date','required','message'=>'删除日期不能为空']
        ];
    }
}