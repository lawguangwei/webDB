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
            //Header("Content-Disposition:  attachment;  filename=".$model->fileName);
            header("Content-Transfer-Encoding:binary");
            header('Content-type:'.$model->fileType);
            header('Expires:0');
            header("Content-Disposition:  attachment;filename=".$model->filename);
            header('Content-Type:application-x/force-download');
            //Yii::$app->response->sendStreamAsFile($model->file->getBytes(),$model->fileName,['mime-type'=>$model->fileType,'fileSize'=>$model->fileSize]);
            echo $model->file->getBytes();
        }
    }

    public function actionMkdir(){
        if(Yii::$app->request->isPost){
            $dirname = $_POST['dir-name'];
            $fileService = new FileService();
            $fileService->mkdir($dirname);
        }
    }
}