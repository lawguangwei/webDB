<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/3
 * Time: 22:41
 */
namespace app\controllers;
use app\models\FileRecord;
use app\models\RemoveLog;
use app\models\UserService;
use yii\web\Controller;
use app\models\LoginLog;
use app\models\LogService;
use app\models\User;
use app\models\Disk;
use Yii;

class TestController extends Controller{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionTest(){
        echo rand(0,1);
    }
    public function actionInsertLoginLog(){
        $start_time = '2015-08-01 00:00:00';
        $end_time = '2016-03-09 00:00:00';

        for($i=1;$i<=1000;$i++){
            $date = $this->rand_time($start_time,$end_time);
            $user_name = 'test'.$i;
            $user_email = $user_name.'@qq.com';
            $user=User::findOne(['user_email'=>$user_email]);

            $loginLog = new LoginLog();
            $loginLog->l_log_id = md5($user->user_id.$date.$i);
            $loginLog->user_id = $user->user_id;
            $loginLog->login_date = $date;
            $loginLog->login_ip = Yii::$app->request->userIP;
            $loginLog->ip_address = '未能获取';
            $loginLog->save();
        }
        echo rand(1,1000);
        //echo date('Y-m-d H:i:s');
    }

    public function actionInsertFileRecord(){
        $file_id = '56e009bee8a711f465b7acd9';
        $user_id = '72eb5a63e3a3584271d0cfc09beaf736';
        $start_time = '2015-01-01 00:00:00';
        $end_time = '2016-03-09 00:00:00';
        for($i=1001;$i<1500;$i++){
            $date = $this->rand_time($start_time,$end_time);
            $file = new FileRecord();
            $file->f_record_id = md5($user_id.$date.$i);
            $file->f_record_type = '1';
            $file->file_id = $file_id;
            $file->user_id = $user_id;
            $file->file_name = 'testData';
            $file->extension = 'mp3';
            $file->file_type = 'audio/mp3';
            $file->file_size = 445746179;
            $file->parent_id = $user_id;
            $file->upload_date =$date;
            $file->state = '1';
            $file->save();
            $removeLog = new RemoveLog();
            $removeLog->f_record_id = $file->f_record_id;
            $removeLog->remove_date = $date;
            $removeLog->save();
        }
        echo date('Y-m-d H:i:s');
    }

    public function actionInsertUser(){
        for($i=2;$i<1000;$i++){
            $user_name = 'test'.$i;
            $user_email = $user_name.'@qq.com';
            $this->userRegister($user_email,$user_name);
        }

        return date('Y-m-d H:i:s');
    }

    public function userRegister($email,$userName){
        $start_time = '2015-01-01 00:00:00';
        $end_time = '2016-03-04 00:00:00';
        $createDate = $this->rand_time($start_time,$end_time);

        $userId = md5($email.$createDate);
        $user = new User();
        $user->user_id = $userId;
        $user->user_email = $email;
        $user->user_password = md5('123456');
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
                    $fileRecord->extension = '';
                    $fileRecord->file_type = 'folder';
                    $fileRecord->file_size = 0;
                    $fileRecord->parent_id = '0';               //跟目录上级目录为0
                    $fileRecord->upload_date = $createDate;
                    $fileRecord->state = '0';                //记录状态0为正常
                    if($fileRecord->save()){
                        $tran->commit();
                        $_SESSION['user'] = $user;
                        return 'success';
                    }
                }
            }
        }catch (Exception $e){
            $tran->rollBack();
        }
    }

    function rand_time($start_time,$end_time){
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time);
        return date('Y-m-d H:i:s', mt_rand($start_time,$end_time));
    }

}
