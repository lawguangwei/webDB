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
        $userFile->filetype = $fileType;
        $userFile->file = $file;

        $disk = Disk::findOne(['user_id'=>$_SESSION['user']['user_id']]);


        try{
            if($userFile->save()){
                $tran = \Yii::$app->db->beginTransaction();

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
                $fileRecord->parent_id = $_SESSION['current_id'];
                $fileRecord->parent_path = $_SESSION['current_path'];
                $fileRecord->upload_date = $created_date;
                $fileRecord->state = "0";

                if($fileRecord->save()){
                    $disk->available_size = $disk->available_size - $fileSize;
                    $parent_folder = FileRecord::findOne(['f_record_id'=>$_SESSION['current_id']]);
                    while($parent_folder->parent_id != '0'){
                        $parent_folder->file_size = $parent_folder->file_size + $fileSize;
                        if($parent_folder->save()){
                            $parent_folder = FileRecord::findOne(['f_record_id'=>$parent_folder->parent_id]);
                        }else{
                            $tran->rollBack();
                            echo 'error';
                        }
                    }
                    if($disk->save()){
                        $tran->commit();
                        echo 'success';
                    }else{
                        $tran->rollBack();
                        echo '空间不足';
                    }
                }else{
                    echo 'error';
                }
            }else{
                echo 'error';
            }
        }catch (Exception $e){
            echo 'error';
        }
    }

    public function getFileListByPath($path){
        return FileRecord::find()->where(['parent_path'=>$path,'user_id'=>$_SESSION['user']['user_id']])->orderBy('file_name')->asArray()->all();
    }

    public function getFileListByParentid($id){
        return FileRecord::find()->where(['parent_id'=>$id])->orderBy('file_name')->asArray()->all();
    }

    public function mkdir($dirname){
        $created_date = date('Y-m-d H:i:sa');
        $user_id = $_SESSION['user']['user_id'];

        $record_id = md5($user_id.$dirname.$created_date);
        $fileRecord = new FileRecord();
        $fileRecord->f_record_id = $record_id;
        $fileRecord->f_record_type = '2';
        $fileRecord->file_id = '0';
        $fileRecord->user_id = $user_id;
        $fileRecord->file_name = $dirname;
        $fileRecord->file_type = 'folder';
        $fileRecord->file_size = 0;
        $fileRecord->parent_id = $_SESSION['current_id'];
        $fileRecord->parent_path = $_SESSION['current_path'];
        $fileRecord->upload_date = $created_date;
        $fileRecord->state = '0';

        if($fileRecord->save()){
            return 'success';
        }else{
            return 'error';
        }
    }

    public function deleteFolder($folderId){
        $folder = FileRecord::findOne(['f_record_id'=>$folderId]);
        $parent = FileRecord::findOne(['f_record_id'=>$folder->parent_id]);
        $parent->file_size = $parent->file_size - $folder->file_size;
        $childs = FileRecord::findAll(['parent_id'=>$folderId]);
        foreach($childs as $child){
            if($child->f_record_type == '2'){
                $this->deleteFolder($child->f_record_id);
            }
            if($child->f_record_type == '1'){
                $this->deleteFile($child->file_id);
            }
        }
        $folder->delete();
    }

    public function deleteFile($fileId){
        $modal = UserFile::findOne($fileId);
        $fileRecord = FileRecord::find()->where(['file_id'=>$fileId])->one();
        $fileSize = $fileRecord->file_size;
        $disk = Disk::findOne(['user_id'=>$_SESSION['user']['user_id']]);
        $tran = \Yii::$app->db->beginTransaction();
        if($modal->delete()){
            if($fileRecord->delete()){
                $disk->available_size = $disk->available_size + $fileSize;
                $parent_folder = FileRecord::findOne(['f_record_id'=>$_SESSION['current_id']]);
                while($parent_folder->parent_id != '0'){
                    $parent_folder->file_size = $parent_folder->file_size - $fileSize;
                    if($parent_folder->save()){
                        $parent_folder = FileRecord::findOne(['f_record_id'=>$parent_folder->parent_id]);
                    }else{
                        $tran->rollBack();
                        return 'error';
                    }
                }
                if($disk->save()){
                    $tran->commit();
                    return 'success';
                }
            }
        }
        $tran->rollBack();
        return 'error';
    }
}