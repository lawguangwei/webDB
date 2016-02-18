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
    private $f_record_id;
    private $f_record_type;
    private $user_id;
    private $file_id;
    private $file_name;
    private $file_type;
    private $file_size;
    private $parent_path;
    private $upload_date;
    private $state;

    public static function tableName(){
        return 'file_record';
    }

}