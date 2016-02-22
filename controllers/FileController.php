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
class FileController extends Controller
{
    public $enableCsrfValidation = false;

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
     */
    public function actionUpload(){
        if(Yii::$app->request->isPost){
            $fileService = new FileService();

            $fileName = $_FILES['file']['name'];
            $fileType = $_FILES['file']['type'];
            $fileSize = $_FILES['file']['size'];
            $file = $_FILES['file']['tmp_name'];

            $fileService->uploadFile($fileName,$fileType,$fileSize,$file);

            /*
            $userFile = new UserFile();

            $fileName = $_FILES['file']['name'];
            $fileSize = $_FILES['file']['size'];
            $fileType = $_FILES['file']['type'];

            $userFile->filename = $fileName;
            $userFile->fileSize = $fileSize;
            $userFile->fileType = $fileType;
            $userFile->file = $_FILES['file']['tmp_name'];

            if($userFile->save()){
                echo $userFile->_id;
            }
            */
        }
    }

    /**
     * Action getfile
     * download file
     */
    public function actionGetfile(){


        if(Yii::$app->request->isPost){
            $file_id = $_POST['file_id'];
            $model = UserFile::findOne($file_id);
            $this->download($model);
            /*
            //Header("Content-Disposition:  attachment;  filename=".$model->fileName);
            header("Content-Transfer-Encoding:binary");
            header('Content-type:'.$model->fileType);
            header('Expires:0');
            header("Content-Disposition:  attachment;filename=".$model->filename);
            header('Content-Type:application-x/force-download');
            //Yii::$app->response->sendStreamAsFile($model->file->getBytes(),$model->fileName,['mime-type'=>$model->fileType,'fileSize'=>$model->fileSize]);
            echo $model->file->getBytes();
            */
        }
    }

    public function download($model){

        $size = $model->file->getSize();
        /*$size2 = $size-1;
        $range = 0;
        if(isset($_SERVER['HTTP_RANGE'])){
            header('HTTP /1.1 206 Partial Content');
            $range = str_replace('=','-',$_SERVER['HTTP_RANGE']);
            $range = explode('-',$range);
            $range = trim($range[1]);
            header('Content-Length:'.$size);
            header('Content-Range: bytes'.$range.'-'.$size2.'/'.$size);
        }else{
            header('Content-Length:'.$size);
            header('Content-Range: bytes 0-'.$size2.'/'.$size);
        }

        header('Content-Length:'.$size);
        header('application/octet-stream');
        header('Cache-control:public');
        header("Pragma:public");

        //解决在IE中下载时中文乱码问题
        header('Content-Disposition:attachment;filename='.$model->filename);
        $ua = $_SERVER['HTTP_USER_AGENT'];
        if(preg_match('/MSIE/',$ua)){
            $ie_filename = str_replace('+','%20',urlencode($model->filename));
            header('Content-Disposition:attachment;filename='.$ie_filename);
        }else{
            header('Content-Disposition:attachment;filename='.$model->filename);
        }*/

        Header("Content-Disposition:  attachment;  filename=".$model->filename);
        header("Content-Transfer-Encoding:binary");
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

    public function actionMkdir(){
        if(Yii::$app->request->isPost){
            $dirname = $_POST['dir-name'];
            $fileService = new FileService();
            $fileService->mkdir($dirname);
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

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

    public function actionDeleteFolder(){
        if(Yii::$app->request->isPost){
            $folder_id = $_POST['file_id'];
            $fileService = new FileService();
            $fileService->deleteFolder($folder_id);
            return $this->redirect(Yii::$app->request->referrer);
        }
    }
}