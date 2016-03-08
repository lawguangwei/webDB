<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/8
 * Time: 21:11
 */
namespace app\models;
use yii\db\ActiveRecord;

class FileManageLog extends ActiveRecord{
    private $fm_log_id;
    private $file_id;
    private $admin_id;
    private $fm_manage_type;
    private $fm_manage_info;
    private $create_date;

    public static function tableName(){
        return 'file_manage_log';
    }

    public function rules(){
        return [
            ['fm_log_id','required','message'=>'id不能为空'],
            ['file_id','required','message'=>'文件id不能为空'],
            ['admin_id','required','message'=>'管理员id不能为空'],
            ['fm_manage_type','required','message'=>'操作类型不能为空'],
            ['create_date','required','message'=>'创建日期不能为空'],
            ['fm_log_id','unique','message'=>'记录已存在']
        ];
    }
}