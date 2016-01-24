<?php

namespace app\models;
use yii\db\ActiveRecord;

class User extends ActiveRecord{

    private $user_id;
    private $user_name;
    private $user_email;
    private $user_password;
    private $create_date;
    private $state;                             //用户状态码;正常0,禁用1;

    public static function tableName(){
        return 'user';
    }

    public function rules(){

        return [
            ['user_email','required','message'=>'用户邮箱不能为空'],
            ['user_email','unique','message'=>'邮箱已注册'],
            ['user_email','email','message'=>'邮箱格式不正确'],
            ['user_name','required','message'=>'用户名不能为空'],
            ['user_name','string','length'=>[3,20],'message'=>'用户名为6-20位字符串'],
            ['user_password','required','message'=>'用户密码不能为空'],
            ['user_password','match','pattern'=>'/^[0-9a-zA-Z]{6,16}/','message'=>'密码为字母开头，长度在6~16之间，只能包含字符、数字']
        ];
    }
}