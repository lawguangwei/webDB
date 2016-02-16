<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 15/10/29
 * Time: 14:28
 */
namespace app\controllers;

use app\components\LoginFilter;
use app\models\FileService;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\UserService;
use app\models\UserFile;

if(!Yii::$app->session->open()){
    Yii::$app->session->open();
}

class UserController extends Controller
{

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
     */
    public function actionIndex()
    {
        $this->layout = "user";

        $_SESSION['current_path'] = 'root';

        //$fileService = new FileService();
        //$files = $fileService->getFileListByPath('root');
        /*
        $dsn = 'mongodb://localhost:27017';
        $connection = new \yii\mongodb\Connection([
            'dsn' => $dsn,
        ]);
        $connection->open();
        $database = $connection->getDatabase('test');
        $collection = $database->getCollection('customer');
        $collection->insert(['name' => 'John Smith', 'status' => 1]);
        $connection->close();
        $user = new UserFile();
        $user->email = 'test';
        $user->name = 'test';
        $user->insert();
        */

        return $this->render('index');
    }

    public function actionLogin()
    {
        $this->layout = "login";

        if(Yii::$app->request->isPost){
            $email = $_POST['email'];
            $password = $_POST['password'];

            $userService = new UserService();
            $result = $userService->userLogin($email,$password);

            //$result->0:帐号不存在;1:密码不正确
            if($result == '0'){
                $errors['user_email']['0'] = '该邮箱未注册';
                return $this->render('login',['errors'=>$errors]);
            }
            if($result == '2'){
                $errors['user_password']['0'] = '密码不正确';
                return $this->render('login',['errors'=>$errors]);
            }
            if($result == '3'){
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


    public function actionLogout(){
        unset($_SESSION['user']);
        return $this->redirect(Url::base().'/index.php?r=user/login');
    }


    public function actionRegister(){
        $this->layout = "login";

        if(Yii::$app->request->isPost){

            //option:1,ajax请求,验证邮箱是否已经注册
            if(isset($_POST['option'])&& $_POST['option'] == "1"){
                $email = $_POST['email'];
                $result = $this->checkEmail($email);
                return $result;
            }
            //获取表单数据
            $email = $_POST['user_email'];
            $userName = $_POST['user_name'];
            $password1 = $_POST['password1'];
            $password2 = $_POST['password2'];

            //验证两次输入密码
            if($password1 != $password2){
                $errors['password']['0'] = '两次输入密码不匹配';
                return $this->render('register',['errors'=>$errors]);
            }

            $userService = new UserService();

            $result = $userService->userRegister($email,$userName,$password1);

            if($result == '1'){
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

        /**

        $email = $_POST['email'];
        if($email != null){
            $userService = new UserService();
            if($userService->isEmailExist($email)){
                $data = ["result"=>"0"];
                echo json_encode($data);//账户存在
                return;
            }
        }
        $data = ["result"=>"1"];
        echo json_encode($data);   //账户部存在
        return;**/
    }
}
