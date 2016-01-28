<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/1/26
 * Time: 21:55
 */
namespace app\models;
use yii\db\ActiveRecord;

class LoginRecord extends ActiveRecord{
    private $l_record_id;
    private $user_id;
    private $login_date;
    private $login_ip;

    public function rules(){
        return [
            ['l_record_id','required','message'=>'id不能为空'],
            ['user_id','required','message'=>'用户不能为空'],
            ['login_date','required','message'=>'登录时间不能为空']
        ];
    }
}