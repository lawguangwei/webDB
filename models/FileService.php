<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 15/11/15
 * Time: 18:45
 */
namespace app\models;

class FileService{
    public function uploadFile($fileName,$fileExtend,$fileSize,$file){
        $userFile = new UserFile();

        $userFile->filename = $fileName;
        $userFile->fileSize = $fileSize;
        $userFile->fileType = $fileExtend;
        $userFile->file = $file;

        if($userFile->save()){
            $created_date = date('Y-m-d H:i:sa');
            $user_id = $_SESSION['user']['user_id'];
            $record_id = md5($user_id.$fileName.$created_date);

            $fileRecord = new FileRecord();
            $fileRecord->record_id = $record_id;
            $fileRecord->user_id = $user_id;
            $fileRecord->file_id = $userFile->_id;
            $fileRecord->file_path = $_SESSION['current_path'];
            $fileRecord->file_name = $fileName;
            $fileRecord->file_type = 1;
            $fileRecord->file_extend = $fileExtend;
            $fileRecord->file_size = $fileSize;
            $fileRecord->created_date = $created_date;

            if($fileRecord->save()){
                echo 'success';
            }else{
                echo 'error';
            }
        }else{
            echo 'error';
        }
    }

    public function getFileListByPath($path){
        return FileRecord::find()->where(['file_path'=>$path])->asArray()->all();
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
}