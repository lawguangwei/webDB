<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 15/11/11
 * Time: 14:31
 */
namespace app\controllers;

use app\components\LoginFilter;
use app\models\FileRecord;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\UserFile;
use yii\web\UploadedFile;
use app\models\FileService;
use app\models\Disk;

if(!Yii::$app->session->open()){
    Yii::$app->session->open();
}

/**
 * Class FileController
 * @package app\controllers
 *
 */
class FileController extends Controller{


    public $enableCsrfValidation = false;  //关闭csrf验证

    /**
     * @return array
     * 用户登录过滤器
     */
    public function behaviors(){
        return [
            [
                'class' => LoginFilter::className(),
                'except' => ['login', 'register','logout'],
            ],
        ];
    }

    /**
     * @return array
     *
     */
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
     * Action upload
     * upload file
     * 文件上传action
     */
    public function actionUpload(){
        if(Yii::$app->request->isPost){
            $fileName = $_FILES['file']['name'];
            $fileType = $_FILES['file']['type'];
            $fileSize = $_FILES['file']['size'];
            $file = $_FILES['file']['tmp_name'];

            $fileService = new FileService();
            $fileService->uploadFile($fileName,$fileType,$fileSize,$file);
        }
    }

    /**
     * Action getfile
     * download file
     * 文件下载action
     */
    public function actionGetfile(){
        if(Yii::$app->request->isPost){
            $file_id = $_POST['file_id'];
            $model = UserFile::findOne($file_id);


            Header("Content-Disposition:  attachment;  filename=".$model->filename);
            header("Content-Transfer-Encoding:binary");
            header('Content-Length:'.$model->filesize);
            header('Content-type:'.$model->filetype);
            header('Expires:0');
            header('Content-Type:application-x/force-download');

            $fp = $model->file->getResource();
            fseek($fp,0);
            while(!feof($fp)){
                set_time_limit(0);
                print(fread($fp,1024));
                flush();
                ob_flush();
            }
            fclose($fp);
        }
    }

    /**
     * @return \yii\web\Response
     * 创建文件夹action
     */
    public function actionMkdir(){
        if(Yii::$app->request->isPost){
            $dirname = $_POST['dir-name'];
            $fileService = new FileService();
            $fileService->mkdir($dirname);
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * @return string
     * 切换文件夹action
     */
    public function actionCd(){
        $this->layout = "user";

        if(Yii::$app->request->isGet){
            $f_id = $_GET['f_id'];
            $fileService = new FileService();
            $files = $fileService->getFileListByParentid($f_id);
            $current = FileRecord::findOne(['f_record_id'=>$f_id]);
            if($f_id == $_SESSION['user']['user_id']){
                $_SESSION['current_path'] = 'root';
            }else{
                $_SESSION['current_path'] = $current->parent_path.'/'.$current->file_name;
            }
            $_SESSION['current_id'] = $f_id;
            $_SESSION['parent_id'] = $current->parent_id;
            $disk = Disk::findOne(['user_id'=>$_SESSION['user']['user_id']]);
            return $this->render('index',['files'=>$files,'disk'=>$disk]);
        }
    }

    /**
     * @return \yii\web\Response
     * 删除文件action
     */
    public function actionDeleteFile(){
        if(Yii::$app->request->isPost){
            $file_id = $_POST['file_id'];

            $fileService = new FileService();
            $result = $fileService->deleteFile($file_id);

            if($result == 'success'){
                return $this->redirect(Yii::$app->request->referrer);
            }else{
                return $this->redirect(Url::base().'/index.php?r=user/index'); //删除失败,测试
            }
        }
    }

    /**
     * @return \yii\web\Response
     * 删除文件夹action
     */
    public function actionDeleteFolder(){
        if(Yii::$app->request->isPost){
            $folder_id = $_POST['file_id'];
            $fileService = new FileService();
            $fileService->deleteFolder($folder_id);
            return $this->redirect(Yii::$app->request->referrer);
        }
    }
}