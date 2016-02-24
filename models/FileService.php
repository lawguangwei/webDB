<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 15/11/15
 * Time: 18:45
 */
namespace app\models;

use Faker\Provider\File;
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
                    if($disk->available_size < 0){
                        return '空间不足';
                        $tran->rollBack();
                    }
                    if($disk->save()){
                        $parent_folder = FileRecord::findOne(['f_record_id'=>$_SESSION['current_id']]);
                        while($parent_folder->parent_id != '0'){
                            $parent_folder->file_size = $parent_folder->file_size + $fileSize;
                            if($parent_folder->save()){
                                $parent_folder = FileRecord::findOne(['f_record_id'=>$parent_folder->parent_id]);
                            }else{
                                $tran->rollBack();
                                return 'error';
                            }
                        }
                        $tran->commit();
                    }
                }else{
                    return 'error';
                }
            }else{
                return 'error';
            }
        }catch (Exception $e){
            return 'error';
        }
    }

    public function getFileListByPath($path){
        return FileRecord::find()->where(['parent_path'=>$path,'user_id'=>$_SESSION['user']['user_id'],'state'=>'0'])->orderBy('file_name')->asArray()->all();
    }

    public function getFileListByParentid($id){
        return FileRecord::find()->where(['parent_id'=>$id,'state'=>'0'])->orderBy('file_name')->asArray()->all();
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
        $childs = FileRecord::findAll(['parent_id'=>$folderId]);
        foreach($childs as $child){
            if($child->f_record_type == '2'){
                $this->deleteFolder($child->f_record_id);
            }
            if($child->f_record_type == '1'){
                $this->deleteFile($child->f_record_id);
            }
        }
        $folder->state = '1';
        $folder->save();
    }

    public function deleteFile($recordId){
        $fileRecord = FileRecord::find()->where(['f_record_id'=>$recordId])->one();
        $fileSize = $fileRecord->file_size;
        $disk = Disk::findOne(['user_id'=>$_SESSION['user']['user_id']]);
        $tran = \Yii::$app->db->beginTransaction();

        try{
            $fileRecord->state = '1';
            if($fileRecord->save()){
                $disk->available_size = $disk->available_size + $fileSize;
                $parent_folder = FileRecord::findOne(['f_record_id'=>$fileRecord->parent_id]);
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
        }catch (Exception $e){
            $tran->rollBack();
            return 'error';
        }
    }


    public function pasteFiles($files){
        $tran = \Yii::$app->db->beginTransaction();
        $disk = Disk::findOne(['user_id'=>$_SESSION['user']['user_id']]);
        try{
            foreach($files as $record_id){
                $file = FileRecord::findOne(['f_record_id'=>$record_id]);
                $disk->available_size = $disk->available_size - $file->file_size;
                if($disk->available_size < 0){
                    $tran->rollBack();
                    return '空间不足';
                }
                $parent_folder = FileRecord::findOne(['f_record_id'=>$_SESSION['current_id']]);
                while($parent_folder->parent_id != '0'){
                    $parent_folder->file_size = $parent_folder->file_size + $file->file_size;
                    if($parent_folder->save()){
                        $parent_folder = FileRecord::findOne(['f_record_id'=>$parent_folder->parent_id]);
                    }else{
                        $tran->rollBack();
                        return 'error';
                    }
                }
                if($file->f_record_type == '2'){
                    $this->pasteFolder($record_id,$_SESSION['current_id'],$_SESSION['current_path']);
                }
                if($file->f_record_type == '1'){
                    $this->pasteFile($record_id,$_SESSION['current_id'],$_SESSION['current_path']);
                }
            }
        }catch (Exception $e){
            $tran->rollBack();
            return $e->getMessage();
        }
        if($disk->save()){
            $tran->commit();
            return 'success';
        }
        $tran->rollBack();
        return 'error2';
    }

    public function pasteFolder($record_id,$parent_id,$parent_path){
        $file = FileRecord::findOne(['f_record_id'=>$record_id]);
        $childs = FileRecord::findAll(['parent_id'=>$record_id]);

        $date = date('Y-m-d H:i:sa');
        $newRecord = new FileRecord();
        $newRecord->f_record_id = md5($file->f_record_id.$date);
        $newRecord->f_record_type = $file->f_record_type;
        $newRecord->file_id = $file->file_id;
        $newRecord->user_id = $file->user_id;
        $newRecord->file_name = $file->file_name;
        $newRecord->file_type = $file->file_type;
        $newRecord->file_size = $file->file_size;
        $newRecord->parent_id = $parent_id;
        $newRecord->parent_path = $parent_path;
        $newRecord->upload_date = $date;
        $newRecord->state = $file->state;

        if($newRecord->save()){
            foreach($childs as $child){
                if($child->f_record_type == '2'){
                    $this->pasteFolder($child->f_record_id,$newRecord->f_record_id,$newRecord->parent_path.'/'.$newRecord->file_name);
                }
                if($child->f_record_type == '1'){
                    $this->pasteFile($child->f_record_id,$newRecord->f_record_id,$newRecord->parent_path.'/'.$newRecord->file_name);
                }
            }
        }else{
            throw new Exception('FileRecord error');
        }
    }

    public function pasteFile($record_id,$parent_id,$parent_path){
        $file = FileRecord::findOne(['f_record_id'=>$record_id]);
        $date = date('Y-m-d H:i:sa');
        $newRecord = new FileRecord();
        $newRecord->f_record_id = md5($file->f_record_id.$date);
        $newRecord->f_record_type = $file->f_record_type;
        $newRecord->file_id = $file->file_id;
        $newRecord->user_id = $file->user_id;
        $newRecord->file_name = $file->file_name;
        $newRecord->file_type = $file->file_type;
        $newRecord->file_size = $file->file_size;
        $newRecord->parent_id = $parent_id;
        $newRecord->parent_path = $parent_path;
        $newRecord->upload_date = $date;
        $newRecord->state = $file->state;

        if($newRecord->save()){
            return;
        }else{
            throw new Exception('FileRecord error');
        }
    }

    public function deleteFiles($files){
        try{
            foreach($files as $record_id){
                $file = FileRecord::findOne(['f_record_id'=>$record_id]);
                $f_record_type = $file->f_record_type;
                if($f_record_type == '2'){
                    $this->deleteFolder($file->f_record_id);
                }
                if($f_record_type == '1'){
                    $this->deleteFile($file->f_record_id);
                }
            }
            return 'success';
        }catch (Exception $e){
            return $e->getMessage();
        }
    }
}