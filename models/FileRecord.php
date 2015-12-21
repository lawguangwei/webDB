<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 15/11/15
 * Time: 18:31
 */
namespace app\models;
use yii\db\ActiveRecord;

class FileRecord extends ActiveRecord{
    private $record_id;
    private $user_id;
    private $file_id;
    private $file_path;
    private $file_name;
    private $file_type;
    private $file_extend;
    private $file_size;
    private $created_date;

    public static function tableName(){
        return 'fileRecord';
    }



}