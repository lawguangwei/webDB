<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/2/16
 * Time: 15:48
 */
namespace app\models;
use yii\db\ActiveRecord;

/**
 * Class Disk
 * @package app\models
 * 用户空间
 */
class Disk extends ActiveRecord{
    private $disk_id;
    private $user_id;
    private $capacity;
    private $available_size;
    private $create_date;

    public static function tableName(){
        return 'disk';
    }

    public function rules(){
        return [
            ['disk_id','required','message'=>'硬盘id不能为空'],
            ['user_id','required','message'=>'用户id不能为空'],
            ['capacity','required','message'=>'用户容量不能为空'],
            ['available_size','required','message'=>'可用空间不能为空'],
            ['create_date','required','message'=>'创建日期不能为空']
        ];
    }
}