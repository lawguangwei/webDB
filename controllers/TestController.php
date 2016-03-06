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

    public function actionInsertLoginLog(){
        $user_id = 'a0f9a13c27bfc8377beedfd5cf53317f';
        /*
        for($day=10;$day<=30;$day++){
            $date = '2015-11-'.$day.' H:i:s';
            for($i=0;$i<mt_rand(50,200);$i++){
                $loginLog = new LoginLog();
                $loginLog->l_log_id = md5($user_id.date($date).$i);
                $loginLog->user_id = $user_id;
                $loginLog->login_date = date($date);
                $loginLog->login_ip = Yii::$app->request->userIP;
                $loginLog->ip_address = '未能获取';
                $loginLog->save();
            }
        }*/
        for($i=0;$i<mt_rand(50,200);$i++){
            $loginLog = new LoginLog();
            $loginLog->l_log_id = md5($user_id.date('2016-03-01 H:i:s').$i);
            $loginLog->user_id = $user_id;
            $loginLog->login_date = date('2016-03-01 H:i:s');
            $loginLog->login_ip = Yii::$app->request->userIP;
            $loginLog->ip_address = '未能获取';
            $loginLog->save();
        }

        echo 'success';
    }

    public function actionInsertFileRecord(){
        $file_id = '56dbaa09e8a711bd54b7acd9';
        $user_id = '9fc058c08ef343c7d160be4e80a8d13c';
        $start_time = '2016-02-20 00:00:00';
        $end_time = '2016-03-01 00:00:00';
        for($i=0;$i<20;$i++){
            $date = $this->rand_time($start_time,$end_time);
            $file = new FileRecord();
            $file->f_record_id = md5($user_id.$date.$i);
            $file->f_record_type = '1';
            $file->file_id = $file_id;
            $file->user_id = $user_id;
            $file->file_name = 'testData';
            $file->extension = 'zip';
            $file->file_type = 'application/zip';
            $file->file_size = 363802;
            $file->parent_id = $user_id;
            $file->upload_date =$date;
            $file->state = '1';
            $file->save();
            $log = new RemoveLog();
            $log->f_record_id = $file->f_record_id;
            $log->remove_date = $date;
            $log->save();
        }
        echo date('Y-m-d H:i:s');
    }

    public function actionInsertUser(){
        $start_time = '2015-01-01 00:00:00';
        $end_time = '2016-03-04 00:00:00';
        for($i=10000;$i<12000;$i++){
            $user_name = 'test'.$i;
            $user_email = $user_name.'@qq.com';
            $user = new User();
            $user->user_id = md5($user_email);
            $user->user_email = $user_email;
            $user->user_name = $user_name;
            $user->user_password = md5('123456');
            $user->create_date= $this->rand_time($start_time,$end_time);
            $user->state = '0';
            $user->save();
        }
        return date('Y-m-d H:i:s');
    }

    function rand_time($start_time,$end_time){
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time);
        return date('Y-m-d H:i:s', mt_rand($start_time,$end_time));
    }

}
