<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/3
 * Time: 20:08
 */
namespace app\controllers;
use app\models\Disk;
use app\models\FileRecord;
use app\models\FileService;
use app\models\User;
use app\models\UserService;
use Yii;

use app\models\Admin;
use app\models\LogService;
use yii\debug\models\search\Log;
use yii\helpers\Url;
use yii\web\Controller;
use app\components\AdminLoginFilter;
use app\models\UserFile;
if(!Yii::$app->session->open()){
    Yii::$app->session->open();
}


class AdminController extends Controller{

    public $enableCsrfValidation = false;  //关闭csrf验证

    public function behaviors(){
        return [
            [
                'class' => AdminLoginFilter::className(),
                'except' => ['login','logout','create-admin'],
            ],
        ];
    }

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

    public function actionIndex(){
        $this->layout = "admin_main";
        return $this->render('index');
    }

    public function actionCreateAdmin(){
        $admin = Admin::findOne(['admin_account'=>'admin']);
        if($admin == null){
            $admin = new Admin();
            $admin->admin_id = md5('admin');
            $admin->admin_account = 'admin';
            $admin->admin_password = md5('123456');
            $admin->state = '0';
            $admin->save();
            echo 'success';
        }
        echo 'exists';
    }

    public function actionLogin(){
        $this->layout = 'login';
        if(\Yii::$app->request->isPost){
            $account = $_POST['admin_account'];
            $password = $_POST['password'];

            $admin = Admin::findOne(['admin_account'=>$account]);

            if($admin == null){
                return $this->render('login',['error'=>'账户不存在']);
            }
            if($admin->admin_password != md5($password)){
                return $this->render('login',['error'=>'密码错误']);
            }
            $logService = new LogService();
            $msg = $logService->adminLogin($admin->admin_id);
            if($msg == 'success'){
                $_SESSION['admin'] = $admin;
                return $this->redirect(Url::base().'/index.php?r=admin/index');
            }
            return $this->render('login',['error'=>'登录失败']);
        }
        return $this->render('login');
    }

    public function actionSetBasicInfo(){
        $userService = new UserService();
        $fileService = new FileService();

        $data['user'] = $userService->countUser();
        $data['file_num'] = $fileService->countFile();
        $data['file_size'] = $fileService->countFileSize();

        return json_encode($data);

    }

    public function actionLoginStatistics(){
        $logService = new LogService();
        $data = $logService->loginStatistics();
        return json_encode($data);
    }

    public function actionStatisticsSize(){
        $fileService = new FileService();
        $data = $fileService->adminStatisticsSize();
        return json_encode($data);
    }

    public function actionStatisticsUser(){
        $userService = new UserService();
        $data = $userService->statisticsUser();
        return json_encode($data);
    }

    public function actionGetAdminLoginLog(){
        $logService = new LogService();
        $data = $logService->adminLoginLog();
        return json_encode($data);
    }

    public function actionQueryUser(){
        if(Yii::$app->request->isPost){
            $user_email = $_POST['user_email'];
            $user = User::find()->where(['user_email'=>$user_email])->asArray()->one();
            if($user == null){
                $data['user'] = '1';
                return json_encode($data);
            }
            $data['user'] = $user;
            $disk = Disk::find()->where(['user_id'=>$user['user_id']])->asArray()->one();
            $data['disk'] = $disk;
            $logService = new LogService();
            $data['um_logs'] = $logService->getUserManagerLog($user['user_id']);
            $data['admin'] = $_SESSION['admin']['admin_account'];
            return json_encode($data);
        }
    }

    public function actionSetUser(){
        if(Yii::$app->request->isPost){
            $user_email = $_POST['user_email'];
            $info = $_POST['info'];
            $userService = new UserService();
            if($userService->setUser($user_email,$info) == 'success'){
                $data['code'] = '0';
            }else{
                $data['code'] = '1';
            }
            return json_encode($data);
        }
    }

    public function actionSetUserSize(){
        if(Yii::$app->request->isPost){
            $user_email = $_POST['user_email'];
            $size = $_POST['size'];
            $info = $_POST['info'];
            $userService = new UserService();
            if($userService->setUserSize($user_email,$size,$info) == 'success'){
                $data['code'] = '0';
            }else{
                $data['code'] = '1';
            }
            return json_encode($data);
        }
    }

    public function actionMostDownFiles(){
        $logService = new LogService();
        $data = $logService->mostDownFiles();
        return json_encode($data);
    }

    public function actionMostUserFiles(){
        $logService = new LogService();
        $data = $logService->mostUserFiles();
        return json_encode($data);
    }

    public function actionGetfile(){
        if(Yii::$app->request->isGet){
            $file_id = $_GET['file_id'];
            $file = UserFile::findOne($file_id);

            Header ( "Content-type: ".$file->filetype );
            Header ( "Accept-Ranges: bytes" );
            Header ( "Accept-Length: " .$file->length);
            Header ( "Content-Disposition: attachment; filename=" . $file->filename);

            /*
            Header("Content-Disposition:  attachment;  filename=".$model->filename);
            header("Content-Transfer-Encoding:binary");
            header('Content-Length:'.$model->filesize);
            header('Content-type:'.$model->filetype);
            header('Expires:0');
            header('Content-Type:application-x/force-download');*/

            $fp = $file->file->getResource();
            fseek($fp,0);
            while(!feof($fp)){
                set_time_limit(0);
                echo(fread($fp,1024));
                flush();
                ob_flush();
            }
            fclose($fp);
        }
    }

    public function actionSetFile(){
        if(Yii::$app->request->isPost){
            $fileId = $_POST['file_id'];
            $info = $_POST['info'];

            $logService = new LogService();
            $msg = $logService->logManageFile($fileId,$info);
            if($msg == 'success'){
                $data['code'] = '0';
            }else{
                $data['code'] = '1';
                $data['msg'] = $msg;
            }

            return json_encode($data);
        }
    }

}