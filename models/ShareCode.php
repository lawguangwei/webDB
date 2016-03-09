<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/9
 * Time: 15:00
 */
namespace app\models;
use yii\db\ActiveRecord;

class ShareCode extends ActiveRecord{
    private $code_id;
    private $code;
    private $user_id;
    private $f_record_id;
    private $create_date;

    public static function tableName(){
        return 'share_code';
    }
}