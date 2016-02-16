<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/2/16
 * Time: 15:45
 */

namespace app\models;
use yii\db\ActiveRecord;

class LoginLog extends ActiveRecord{
    private $l_log_id;
    private $user_id;
    private $login_date;
    private $login_ip;

    public static function tableName(){
        return 'login_log';
    }

    public function rules(){
        return [
            ['l_record_id','required','message'=>'id不能为空'],
            ['user_id','required','message'=>'用户不能为空'],
            ['login_date','required','message'=>'登录时间不能为空']
        ];
    }

}