<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 15/11/8
 * Time: 14:45
 */
namespace app\models;
use yii\helpers\ArrayHelper;

class UserService{

    /**
     * @param $email
     * @param $password
     * @return int|null|static
     * 用户注册
     * 帐号不存在返回0
     * 登录成功返回1
     * 密码错误返回2
     */
    public function userLogin($email,$password){
        $user = User::findOne(['user_email'=>$email]);
        if($user == null){
            return 0; //0:帐号不存在;1:登录成功;2:密码不正确;
        }
        $password = md5($password);
        if($user->user_password == $password){
            $_SESSION['user'] = $user;
            return 1; //0:帐号不存在;1:登录成功;2:密码不正确;
        }else{
            return 2; //0:帐号不存在;1:登录成功;2:密码不正确;
        }
    }

    /**
     * @param $email
     * @param $userName
     * @param $password
     * @return array|int
     * 用户注册
     * 成功返回1
     * 错误返回错误信息数组
     */
    public function userRegister($email,$userName,$password){
        $createDate = date('Y-m-d H:i:sa');
        $userId = md5($email.$createDate);

        $user = new User();
        $user->user_id = $userId;
        $user->user_email = $email;
        $user->user_password = md5($password);
        $user->user_name = $userName;
        $user->create_date = $createDate;

        if($user->validate()){
            $user->save();
            $_SESSION['user'] = $user;
            return 1;
        }else{
            $errors = $user->errors;
            $errors = ArrayHelper::toArray($errors);
            return $errors;
        }
    }
}