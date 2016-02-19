<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 15/11/15
 * Time: 18:45
 */
namespace app\models;

use yii\base\Exception;

class FileService{
    public function uploadFile($fileName,$fileType,$fileSize,$file){
        $userFile = new UserFile();
        $userFile->filename = $fileName;
        $userFile->fileSize = $fileSize;
        $userFile->fileType = $fileType;
        $userFile->file = $file;

        $disk = Disk::findOne(['user_id'=>$_SESSION['user']['user_id']]);

        $tran = \Yii::$app->db->beginTransaction();

        try{
            if($userFile->save()){
                $created_date = date('Y-m-d H:i:sa');
                $user_id = $_SESSION['user']['user_id'];
                $record_id = md5($user_id.$fileName.$created_date);

                $fileRecord = new FileRecord();
                $fileRecord->f_record_id = $record_id;
                $fileRecord->f_record_type = "1";
                $fileRecord->file_id = $userFile->_id;
                $fileRecord->user_id = $user_id;
                $fileRecord->file_name = $fileName;
                $fileRecord->file_type = $fileType;
                $fileRecord->file_size = $fileSize;
                $fileRecord->parent_path = $_SESSION['current_path'];
                $fileRecord->upload_date = $created_date;
                $fileRecord->state = "0";

                if($fileRecord->save()){
                    $disk->available_size = $disk->available_size - $fileSize;
                    if($disk->save()){
                        $tran->commit();
                        echo 'success';
                    }else{
                        $tran->rollBack();
                        echo '空间不左';
                    }
                }else{
                    $tran->rollBack();
                    echo 'error';
                }
            }else{
                $tran->rollBack();
                echo 'error';
            }
        }catch (Exception $e){
            $tran->rollBack();
            echo 'error';
        }
    }

    public function getFileListByPath($path){
        return FileRecord::find()->where(['parent_path'=>$path])->asArray()->all();
    }

    public function mkdir($dirname){
        $created_date = date('Y-m-d H:i:sa');
        $user_id = $_SESSION['user']['user_id'];

        $record_id = md5($user_id.$dirname.$created_date);
        $fileRecord = new FileRecord();
        $fileRecord->record_id = $record_id;
        $fileRecord->user_id = $user_id;
        $fileRecord->file_id = '0';
        $fileRecord->file_path = $_SESSION['current_path'];
        $fileRecord->file_name = $dirname;
        $fileRecord->file_type = 2;
        $fileRecord->file_extend = 'dir';
        $fileRecord->file_size = 0;
        $fileRecord->created_date = $created_date;

        if($fileRecord->save()){
            echo 'success';
        }else{
            echo 'error';
        }
    }

    public function deleteFile($fileId){
        $modal = UserFile::findOne('56c7285ee8a7114b51b7b5');
        $fileRecord = FileRecord::find()->where(['file_id'=>$fileId])->one();
        $fileSize = $fileRecord->file_size;
        $disk = Disk::findOne(['user_id'=>$_SESSION['user']['user_id']]);
        if($modal->delete()){
            if($fileRecord->delete()){
                $disk->available_size = $disk->available_size + $fileSize;
                if($disk->save()){
                    return 'success';
                }
            }
        }
        return 'error';
    }
}