<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 15/11/11
 * Time: 14:31
 */
namespace app\controllers;

use app\components\LoginFilter;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\UserFile;
use yii\web\UploadedFile;
use app\models\FileService;

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
            $fileExtend = $_FILES['file']['type'];
            $fileSize = $_FILES['file']['size'];
            $file = $_FILES['file']['tmp_name'];

            $fileService->uploadFile($fileName,$fileExtend,$fileSize,$file);

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
        $size2 = $size-1;
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


        header('Accept-Ranges:bytes');
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
        }

        $fp = $model->file->getResource();
        fseek($fp,$range);
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
        }
    }

    public function actionDeleteFile(){
        if(Yii::$app->request->isPost){
            $file_id = $_POST['file_id'];

            $fileService = new FileService();
            $result = $fileService->deleteFile($file_id);

            if($result == 'success'){
                return $this->redirect(Url::base().'/index.php?r=user/index');
            }else{
                return $this->redirect(Url::base().'/index.php?r=user/index'); //删除失败,测试
            }
        }
    }
}