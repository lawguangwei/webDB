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
use app\models\LogService;
use app\models\ShareCode;
use Faker\Provider\File;
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
            $userId = $_SESSION['user']['user_id'];
            $currentId = $_SESSION['current_id'];
            $msg = $fileService->uploadFile($fileName,$fileType,$fileSize,$file,$userId,$currentId);
            if($msg == '1'){
                $result['code'] = '1';
                $result['msg'] = '空间不足';
            }else if($msg == '2'){
                $result['code'] = '2';
                $result['msg'] = '数据库错误';
            }else if($msg == '3'){
                $result['code'] = '3';
                $result['msg'] = '违规文件,禁止上传';
            }else{
                $result['code'] = '0';
                $result['msg'] = 'success';
                $result['file'] = $msg;
            }
            $disk = Disk::find()->where(['user_id'=>$_SESSION['user']['user_id']])->asArray()->one();
            $result['disk'] =  $disk;
            echo json_encode($result);
        }
    }

    /**
     * Action getfile
     * download file
     * 文件下载action
     */
    public function actionGetfile(){
        if(Yii::$app->request->isGet){
            $file_id = $_GET['file_id'];
            $file = UserFile::findOne($file_id);


            $logService = new LogService();
            $logService->downloadLog($file_id);


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

    /**
     * @return \yii\web\Response
     * 创建文件夹action
     */
    public function actionMkdir(){
        if(Yii::$app->request->isPost){
            $dirname = $_POST['dir-name'];
            $currentId = $_SESSION['current_id'];
            $fileService = new FileService();
            $fileService->mkdir($dirname,$currentId);
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
            $current = FileRecord::findOne(['f_record_id'=>$f_id,'state'=>'0']);
            if($current == null){
                return $this->redirect(Url::base().'/index.php?r=user/index');
            }

            unset($paths);
            $paths = array();
            $tmp = $current;
            $index = 0;
            while($tmp->parent_id != '0'){
                $paths[$index]['name'] = $tmp->file_name;
                $paths[$index++]['f_record_id'] = $tmp->f_record_id;
                $tmp = FileRecord::findOne(['f_record_id'=>$tmp->parent_id,'state'=>'0']);
            }
            $paths[$index]['name'] = $tmp->file_name;
            $paths[$index++]['f_record_id'] = $tmp->f_record_id;
            /**
            if($f_id == $_SESSION['user']['user_id']){
                $_SESSION['current_path'] = '我的网盘';
            }else{
                $_SESSION['current_path'] = $current->parent_path.'/'.$current->file_name;
            }
             */
            $_SESSION['current_path']=$paths;
            $_SESSION['current_id'] = $f_id;
            $_SESSION['parent_id'] = $current->parent_id;
            $_SESSION['li_option'] = 'index';
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
            $userId = $_SESSION['user']['user_id'];
            $record_id = $_POST['record_id'];
            $fileService = new FileService();
            $result['code'] = '0';
            $result['info'] = $fileService->deleteFile($record_id,$userId);
            $result['disk'] = Disk::find()->where(['user_id'=>$userId])->asArray()->one();
            return json_encode($result);
            /**
            if($result == 'success'){
                return $this->redirect(Yii::$app->request->referrer);
            }else{
                return $this->redirect(Url::base().'/index.php?r=user/index'); //删除失败,测试
            }*/
        }
    }

    /**
     * @return \yii\web\Response
     * 删除文件夹action
     */
    public function actionDeleteFolder(){
        if(Yii::$app->request->isPost){
            $userId = $_SESSION['user']['user_id'];
            $folder_id = $_POST['record_id'];
            $fileService = new FileService();
            $result['code'] = '0';
            $result['info'] = $fileService->deleteFolder($folder_id,$userId);
            $result['disk'] = Disk::find()->where(['user_id'=>$userId])->asArray()->one();
            return json_encode($result);
        }
    }

    /**
     * @return string
     * 复制文件
     * 保存文件id
     */
    public function actionCopyFiles(){
        if(Yii::$app->request->isPost){
            if(isset($_POST['option'])){
                if($_POST['option'] == 'copy'){
                    $files = $_POST['files'];
                    $files = array_unique($files);
                    $files = array_merge($files);
                    $_SESSION['copy_files'] = $files;
                    $_SESSION['copy_option'] = 'copy';
                    return json_encode($files);
                }
                if($_POST['option'] == 'cut'){
                    $files = $_POST['files'];
                    $files = array_unique($files);
                    $files = array_merge($files);
                    $_SESSION['copy_files'] = $files;
                    $_SESSION['copy_option'] = 'cut';
                    return json_encode($files);
                }
            }
        }
    }

    /**
     * 粘贴文件
     */
    public function actionPasteFiles(){
        if(Yii::$app->request->isGet){
            if(isset($_SESSION['copy_files'])){
                $files = $_SESSION['copy_files'];
                $files = array_unique($files);
                $files = array_merge($files);

                $userId = $_SESSION['user']['user_id'];
                $currentId = $_SESSION['current_id'];
                $fileServices = new FileService();
                $msg = $fileServices->pasteFiles($files,$userId,$currentId);
                if($msg == 'success'){
                    if($_SESSION['copy_option'] == 'copy'){
                        $result['msg'] = $msg;
                        unset($_SESSION['copy_files']);
                        unset($_SESSION['copy_option']);
                        return json_encode($result);
                    }
                    if($_SESSION['copy_option'] == 'cut'){
                        $msg = $fileServices->deleteFiles($files,$userId);
                        $result['msg'] = $msg;
                        unset($_SESSION['copy_files']);
                        unset($_SESSION['copy_option']);
                        return json_encode($result);
                    }
                }else{
                    $result['msg'] = $msg;
                    return json_encode($result);
                }
            }
        }
    }

    /**
     * 删除多个文件
     */
    public function actionDeleteFiles(){
        if(Yii::$app->request->isPost){
            $userId = $_SESSION['user']['user_id'];
            $files = $_POST['files'];
            $files = array_unique($files);
            $files = array_merge($files);
            $fileService = new FileService();
            $msg = $fileService->deleteFiles($files,$userId);
            if($msg == 'success'){
                $result['code'] = '0';
                $result['disk'] = Disk::find()->where(['user_id'=>$userId])->asArray()->one();
                echo json_encode($result);
            }else{
                $result['code'] = '1';
                $result['msg'] = $msg;
                echo json_encode($result);
            }
        }
    }

    public function actionRename(){
        if(Yii::$app->request->isPost){
            $record_id = $_POST['record_id'];
            $newName = $_POST['new_name'];
            $fileService = new FileService();
            $msg = $fileService->rename($record_id,$newName);
            if($msg == 'success'){
                $record = FileRecord::findOne(['f_record_id'=>$record_id]);
                $result['code'] = '0';
                $result['file_name'] = $record->file_name;
                echo json_encode($result);
            }else{
                $result['code'] = '1';
                $result['msg'] = $msg;
                echo json_encode($result);
            }
        }
    }

    public function actionSelectFile(){
        $this->layout = 'user';
        if(Yii::$app->request->isGet){
            $type = $_GET['type'];
            $userId = $_SESSION['user']['user_id'];
            $fileSerivce = new FileService();
            $files = $fileSerivce->selectFileByType($type,$userId);
            $_SESSION['li_option'] = $type;
            return $this->render('select_type',['files'=>$files]);
        }
    }

    public function actionRecycle(){
        $this->layout = 'user';
        $fileService = new FileService();
        $userId = $_SESSION['user']['user_id'];
        $files =$fileService->recycleFiles($userId);
        return $this->render('recycle',['files'=>$files]);
    }

    public function actionRevert(){
        if(Yii::$app->request->isPost){
            $files = $_POST['files'];
            $files = array_unique($files);
            $files = array_merge($files);
            $filesService = new FileService();
            $userId = $_SESSION['user']['user_id'];
            $result = $filesService->revertFiles($files,$userId);
            switch($result){
                case '0' : $data['code'] = '0';break;
                case '1' : $data['code'] = '1';$data['msg'] = '还原错误';break;
                case '2' : $data['code'] = '2';$data['msg'] = '空间不足';break;
                default : $data['code'] = '1';$data['msg'] = '还原错误';
            }
            return json_encode($data);
        }
    }

    public function actionUserShareFiles(){
        $this->layout = 'person_info';
        return $this->render('share');
    }

    public function actionShareFile(){
        if(Yii::$app->request->isPost){
            $recordId = $_POST['record_id'];
            $userId = $_SESSION['user']['user_id'];
            $fileService = new FileService();
            $msg = $fileService->createShareCode($recordId,$userId);
            switch($msg){
                case '1' : $data['code'] = '1';$data['msg'] = '分享码已存在';break;
                case '2' : $data['code'] = '2';$data['msg'] = '分享文件数量已满';break;
                case '3' : $data['code'] = '3';$data['msg'] = '分享码生成失败';break;
                default : $data['code'] = '0';$data['msg'] = $msg;break;
            }
            return json_encode($data);
        }
    }

    public function actionShareFileList(){
        $userId = $_SESSION['user']['user_id'];
        $fileService = new FileService();
        $data = $fileService->getUserShareFile($userId);
        return json_encode($data);
    }

    public function actionDeleteShareCode(){
        if(Yii::$app->request->isPost){
            $codeId = $_POST['code_id'];
            $shareCode = ShareCode::findOne(['code_id'=>$codeId]);
            if($shareCode->delete()){
                $data['code'] = '0';
            }else{
                $data['code'] = '1';
                $data['msg'] = $shareCode->errors;
            }
            return json_encode($data);
        }
    }

    public function actionGetCodeFile(){
        if(Yii::$app->request->isPost){
            $code = $_POST['code'];
            $fileService = new FileService();
            $msg = $fileService->getFileByCode($code);
            switch($msg){
                case '1':$data['code'] = '1';$data['msg'] = '提取码不存在';break;
                case '2':$data['code'] = '2';$data['msg'] = '文件已删除';break;
                default:$data['code'] = '0';$data['file_id'] = $msg;break;
            }
            return json_encode($data);
        }
    }
}