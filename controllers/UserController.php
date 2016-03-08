<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 15/10/29
 * Time: 14:28
 */
namespace app\controllers;

use app\components\LoginFilter;
use app\models\FileRecord;
use app\models\FileService;
use app\models\LogService;
use Yii;
use yii\debug\models\search\Log;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\UserService;
use app\models\UserFile;
use app\models\Disk;

if(!Yii::$app->session->open()){
    Yii::$app->session->open();
}

class UserController extends Controller
{
    //用户登录过滤器
    public function behaviors(){
        return [
            [
                'class' => LoginFilter::className(),
                'except' => ['login', 'register','logout'],
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

    /**
     * @return string
     * 用户主页
     */
    public function actionIndex()
    {
        return $this->redirect(Url::base().'/index.php?r=file/cd&f_id='.$_SESSION['user']['user_id']);
    }

    /**
     * @return string|\yii\web\Response
     * 用户登录action
     */
    public function actionLogin()
    {
        $this->layout = "login";

        if(Yii::$app->request->isPost){
            $email = $_POST['email'];
            $password = $_POST['password'];

            $userService = new UserService();
            $result = $userService->userLogin($email,$password);

            if($result == '0'){                                         //$result->0:帐号不存在;1:密码不正确
                $errors['user_email']['0'] = '该邮箱未注册';
                return $this->render('login',['errors'=>$errors]);
            }
            if($result == '2'){
                $errors['user_password']['0'] = '密码不正确';
                return $this->render('login',['errors'=>$errors]);
            }
            if($result == '3'){                                        //登录日志写入错误
                $errors['user_password']['0'] = '登录失败,请重试';
                return $this->render('login',['errors'=>$errors]);
            }
            if($result == '1'){
                return $this->redirect(Url::base().'/index.php?r=user/index');
            }
            $errors['unknown']['0'] = '未知错误';
            return $this->render('login',['errors'=>$errors]);
        }
        return $this->render('login');
    }

    /**
     * @return \yii\web\Response
     * 用户退出
     */
    public function actionLogout(){
        unset($_SESSION['user']);
        Yii::$app->session->close();
        return $this->redirect(Url::base().'/index.php?r=user/login');
    }

    /**
     * @return array|int|string|\yii\web\Response
     * 用户注册action
     */
    public function actionRegister(){
        $this->layout = "login";

        if(Yii::$app->request->isPost){
            if(isset($_POST['option'])&&$_POST['option'] == "1"){       //option:1,ajax请求,验证邮箱是否已经注册
                $email = $_POST['email'];
                $result = $this->checkEmail($email);
                return $result;
            }

            $email = $_POST['user_email'];
            $userName = $_POST['user_name'];
            $password1 = $_POST['password1'];
            $password2 = $_POST['password2'];

            if($password1 != $password2){
                $errors['password']['0'] = '两次输入密码不匹配';
                return $this->render('register',['errors'=>$errors]);
            }

            $userService = new UserService();
            $result = $userService->userRegister($email,$userName,$password1);
            if($result == 'success'){
                return $this->redirect(Url::base()."/index.php?r=user/index");
            }else{
                $errors = $result;
                return $this->render('register',['errors'=>$errors]);
            }
        }
        return $this->render('register');
    }

    /**
     * @return int
     * 检查账户是否存在
     */
    public static function checkEmail($email){

        $userService = new UserService();
        $userExist = $userService->isEmailExist($email);
        if($userExist == true){
            $result['exist'] = "1";
        }else{
            $result['exist'] = "0";
        }
        return json_encode($result);
    }

    public function actionPersonInfo(){
        $this->layout = 'person_info';
        $userId = $_SESSION['user']['user_id'];
        $disk = Disk::findOne(['user_id'=>$userId]);
        $fileService = new FileService();
        $typeSize = $fileService->typeSize($userId);
        $logService = new LogService();
        $logs = $logService->getLoginLog();
        return $this->render('person_info',['disk'=>$disk,'typeSize'=>$typeSize,'logs'=>$logs]);
    }

    public function actionSetInfo(){
        $this->layout = 'person_info';
        return $this->render('set_info');
    }

    public function actionModifyInfo(){
        $this->layout = 'person_info';
        if(Yii::$app->request->isPost){
            $newName = $_POST['user_name'];
            $userService = new UserService();
            $msg = $userService->modifyUserName($newName);
            if($msg == 'success'){
                return $this->redirect(Url::base().'/index.php?r=user/set-info');
            }else{
                return $this->render('set_info',['errors'=>$msg]);
            }
        }
    }

    public function actionModifyPassword(){
        $this->layout = 'person_info';
        if(Yii::$app->request->isPost){
            $oldPass = $_POST['old_password'];
            $newPass = $_POST['new_password'];
            $userService = new UserService();
            $msg = $userService->modifyPassword($oldPass,$newPass);
            if($msg == '1'){
                return $this->render('set_info',['msg'=>'原密码不正确']);
            }
            if($msg == '0'){
                return $this->render('set_info',['msg'=>'密码修改成功']);
            }
            return $this->render('set_info',['errors'=>$msg]);
        }
    }
}
