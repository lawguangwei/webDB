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
     * loginRecord写入错误返回3
     */

    public function userLogin($email,$password){
        $user = User::findOne(['user_email'=>$email]);
        if($user == null){
            return 0; //0:帐号不存在;1:登录成功;2:密码不正确;
        }
        $password = md5($password);
        if($user->user_password == $password){
            $_SESSION['user'] = $user;

            $userId = $user->user_id;
            $loginDate = date('Y-m-d H:i:sa');
            $ip = \Yii::$app->request->getUserIP();
            $loginRecordId = md5($userId.$loginDate);


            $loginRecord = new LoginRecord();
            $loginRecord->l_record_id = $loginRecordId;
            $loginRecord->user_id = $userId;
            $loginRecord->login_date = $loginDate;
            $loginRecord->login_ip = $ip;

            if($loginRecord->save()){
                return 1; //0:帐号不存在;1:登录成功;2:密码不正确;
            }
            return 3;  //loginRecord写入错误
        }else{
            return 2; //0:帐号不存在;1:登录成功;2:密码不正确;
        }
    }

    /**
    // 获取IP地址（摘自discuz）
    function getIp(){
        $ip='未知IP';
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            return $this->is_ip($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:$ip;
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            return $this->is_ip($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$ip;
        }else{
            return $this->is_ip($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:$ip;
        }
    }

    function is_ip($str){
        $ip=explode('.',$str);
        for($i=0;$i<count($ip);$i++){
            if($ip[$i]>255){
                return false;
            }
        }
        return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$str);
    }
     **/



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

        $disk = new Disk();
        $disk->disk_id = md5($userId.$createDate);
        $disk->user_id = $userId;
        $disk->capacity = 20480;  //20G
        $disk->available_size = 20480;
        $disk->create_date = $createDate;
        $tran = \Yii::$app->db->beginTransaction();
        if($user->validate()){
            $user->save();
            if($disk->validate()){
                $disk->save();
                $tran->commit();
                $_SESSION['user'] = $user;
                return 1;
            }else{
                $errors = $disk->errors;
                $errors = ArrayHelper::toArray($errors);
                $tran->rollBack();
                return $errors;
            }
        }else{
            $errors = $user->errors;
            $errors = ArrayHelper::toArray($errors);
            $tran->rollBack();
            return $errors;
        }
    }

    /**
     * @param $email
     * @return bool
     */
    public function isEmailExist($email){
        $user = User::findOne(['user_email'=>$email]);
        if($user != null) {
            return true;
        }
        return false;
    }
}