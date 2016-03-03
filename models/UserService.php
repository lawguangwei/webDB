<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 15/11/8
 * Time: 14:45
 */
namespace app\models;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class UserService{

    /**
     * @param $email
     * @param $password
     * @return int|null|static
     * 用户登录
     * 帐号不存在返回0
     * 登录成功返回1
     * 密码错误返回2
     * loginRecord写入错误返回3
     */
    public function userLogin($email,$password){
        $user = User::findOne(['user_email'=>$email]);
        if($user == null){
            return 0;                                        //0:帐号不存在;1:登录成功;2:密码不正确;
        }
        $password = md5($password);
        if($user->user_password == $password){
            $userId = $user->user_id;
            $logService = new LogService();
            $msg = $logService->login($userId);
            if($msg == 'success'){
                $_SESSION['user'] = $user;
                return 1;                                   //0:帐号不存在;1:登录成功;2:密码不正确;
            }else{
                return 3;
            }                                               //loginRecord写入错误
        }else{
            return 2;                               //0:帐号不存在;1:登录成功;2:密码不正确;
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

        $tran = \Yii::$app->db->beginTransaction();
        try{
            if($user->save()){
                $disk = new Disk();
                $disk->disk_id = md5($userId.$createDate);    //创建用户空间
                $disk->user_id = $userId;
                $disk->capacity = 21474836480; //20GB
                $disk->available_size = 21474836480;
                $disk->create_date = $createDate;
                if($disk->save()){                            //初始化用户跟目录
                    $fileRecord = new FileRecord();
                    $fileRecord->f_record_id = $userId;
                    $fileRecord->f_record_type = '2';         //f_record_type:2,目录类型
                    $fileRecord->file_id = '0';               //目录类型文件id为0
                    $fileRecord->user_id = $userId;
                    $fileRecord->file_name = '我的网盘';
                    $fileRecord->file_size = 0;
                    $fileRecord->parent_id = '0';               //跟目录上级目录为0
                    $fileRecord->parent_path = '0';
                    $fileRecord->upload_date = $createDate;
                    $fileRecord->state = '0';                //记录状态0为正常
                    if($fileRecord->save()){
                        $tran->commit();
                        $_SESSION['user'] = $user;
                        return 'success';
                    }else{
                        $errors = $fileRecord->errors;
                        $tran->rollBack();
                        return $errors;
                    }
                }else{
                    $errors = $disk->errors;
                    $tran->rollBack();
                    return $errors;
                }
            }else{
                $errors = $user->errors;
                $tran->rollBack();
                return $errors;
            }
        }catch (Exception $e){
            $errors = $e->getMessage();
            $tran->rollBack();
            return $errors;
        }
    }

    /**
     * @param $email
     * @return bool
     * 返回账户是否存在
     */
    public function isEmailExist($email){
        $user = User::findOne(['user_email'=>$email]);
        if($user != null) {
            return true;
        }
        return false;
    }

    public function modifyUserName($name){
        $user = User::findOne(['user_id'=>$_SESSION['user']['user_id']]);
        $user->user_name = $name;
        if($user->save()){
            $_SESSION['user'] = $user;
            return 'success';
        }else{
            $errors = $user->errors;
            return $errors;
        }
    }

    public function modifyPassword($oldPass,$newPass){
        $user = User::findOne(['user_id'=>$_SESSION['user']['user_id']]);
        $oldPass = md5($oldPass);
        if($user->user_password !=$oldPass){
            return '1';
        }
        $user->user_password = md5($newPass);
        if($user->save()){
            return '0';
        }else{
            $errors = $user->errors;
            return $errors;
        }
    }

}