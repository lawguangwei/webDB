<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 15/11/15
 * Time: 18:31
 */
namespace app\models;
use yii\db\ActiveRecord;

/**
 * Class FileRecord
 * @package app\models
 * 文件记录
 *
 */
class FileRecord extends ActiveRecord{
    private $f_record_id;
    private $f_record_type;
    private $user_id;
    private $file_id;
    private $file_name;
    private $extension;
    private $file_type;
    private $file_size;
    private $parent_id;
    private $upload_date;
    private $state;

    public static function tableName(){
        return 'file_record';
    }

    public function rules(){
        return [
            ['f_record_id','required','message'=>'文件记录id不能为空'],
            ['f_record_type','required','message'=>'记录类型不能为空'],
            ['user_id','required','message'=>'用户id不能为空'],
            ['file_id','required','message'=>'文件id不能为空'],
            ['file_name','required','message'=>'文件名不能为空'],
            ['file_type','required','message'=>'文件类型不能为空'],
            ['file_size','required','message'=>'文件大小不能为空'],
            ['parent_id','required','message'=>'父目录id不能为空'],
            ['upload_date','required','message'=>'上传日期不能为空'],
            ['state','required','message'=>'记录状态不能为空']
        ];
    }
}