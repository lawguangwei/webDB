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
                $tmps = explode('.' , $fileName);
                if(count($tmps) == 1){
                    $fileRecord->extension = '';
                }else{
                    $fileRecord->extension = end($tmps);
                }
                $fileRecord->file_type = $fileType;
                $fileRecord->file_size = $fileSize;
                $fileRecord->parent_id = $_SESSION['current_id'];

                $fileRecord->upload_date = $created_date;
                $fileRecord->state = "0";

                if($fileRecord->save()){
                    $disk->available_size = $disk->available_size - $fileSize;
                    if($disk->available_size < 0){
                        return '1';                 //空间不足
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
                        return FileRecord::find()->where(['f_record_id'=>$record_id])->asArray()->one();
                    }
                }else{
                    return '2';      //数据库错误
                }
            }else{
                return '2';
            }
        }catch (Exception $e){
            return '2';
        }

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
        $fileRecord->extension = '';
        $fileRecord->file_type = 'folder';
        $fileRecord->file_size = 0;
        $fileRecord->parent_id = $_SESSION['current_id'];
        $fileRecord->upload_date = $created_date;
        $fileRecord->state = '0';

        $tran = \Yii::$app->db->beginTransaction();
        if($fileRecord->save()){
            $tran->commit();
            return 'success';
        }else{
            $tran->rollBack();
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
        return 'success';
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
                    $this->pasteFolder($record_id,$_SESSION['current_id']);
                }
                if($file->f_record_type == '1'){
                    $this->pasteFile($record_id,$_SESSION['current_id']);
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

    public function pasteFolder($record_id,$parent_id){
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
        $newRecord->extension = $file->extension;
        $newRecord->file_size = $file->file_size;
        $newRecord->parent_id = $parent_id;
        $newRecord->upload_date = $date;
        $newRecord->state = $file->state;

        if($newRecord->save()){
            foreach($childs as $child){
                if($child->f_record_type == '2'){
                    $this->pasteFolder($child->f_record_id,$newRecord->f_record_id);
                }
                if($child->f_record_type == '1'){
                    $this->pasteFile($child->f_record_id,$newRecord->f_record_id);
                }
            }
        }else{
            throw new Exception('FileRecord error');
        }
    }

    public function pasteFile($record_id,$parent_id){
        $file = FileRecord::findOne(['f_record_id'=>$record_id]);
        $date = date('Y-m-d H:i:sa');
        $newRecord = new FileRecord();
        $newRecord->f_record_id = md5($file->f_record_id.$date);
        $newRecord->f_record_type = $file->f_record_type;
        $newRecord->file_id = $file->file_id;
        $newRecord->user_id = $file->user_id;
        $newRecord->file_name = $file->file_name;
        $newRecord->extension = $file->extension;
        $newRecord->file_type = $file->file_type;
        $newRecord->file_size = $file->file_size;
        $newRecord->parent_id = $parent_id;
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

    public function rename($record_id,$newName){
        $record = FileRecord::findOne(['f_record_id'=>$record_id]);
        $record->file_name = $newName.'.'.$record->extension;
        if($record->f_record_type == '1'){
            $file = UserFile::findOne($record->file_id);
            $file->filename = $newName.'.'.$record->extension;
            if($file->save()){
                if($record->save()){
                    return 'success';
                }else{
                    return $record->errors;
                }
            }else{
                return 'error';
            }
        }else{
            if($record->save()){
                return 'success';
            }else{
                return $record->errors;
            }
        }
    }

    public function selectFileByType($type){
        if($type == 'picture'){
            $sql = 'select * from file_record where user_id="'.$_SESSION['user']['user_id'].'" and state="0" and extension in("jpg","jpeg","png","gif")';
            $files = FileRecord::findBySql($sql)->all();
           // $files = FileRecord::find()->Where(['user_id'=>$_SESSION['user']['user_id'],'extension'=>'jpg'])->orWhere(['user_id'=>$_SESSION['user']['user_id'],'extension'=>'jpeg'])
              //  ->orWhere(['user_id'=>$_SESSION['user']['user_id'],'extension'=>'png'])->where(['user_id'=>$_SESSION['user']['user_id'],'extension'=>'gif'])->all();
        }
        if($type == 'word'){
            $sql = 'select * from file_record where user_id="'.$_SESSION['user']['user_id'].'" and state="0" and extension in("txt","doc","ppt","xls","pdf","docx","xlsx","pptx")';
            $files = FileRecord::findBySql($sql)->all();
        }
        if($type == 'film'){
            $sql = 'select * from file_record where user_id="'.$_SESSION['user']['user_id'].'" and state="0" and extension in("avi","rm","rmvb","mov","wmv","mp4","mkv","mpeg")';
            $files = FileRecord::findBySql($sql)->all();
        }
        if($type == 'music'){
            $sql = 'select * from file_record where user_id="'.$_SESSION['user']['user_id'].'" and state="0" and extension in("mp3","wav","wma","ogg","ape","acc")';
            $files = FileRecord::findBySql($sql)->all();
        }
        if($type == 'other'){
            $sql = 'select * from file_record where user_id="'.$_SESSION['user']['user_id'].'" and f_record_type = "1" and state="0" and extension not in("mp3","wav","wma","ogg","ape","acc","jpg","jpeg","png","gif",
            "txt","doc","ppt","xls","pdf","docx","xlsx","pptx","avi","rm","rmvb","mov","wmv","mp4","mkv","mpeg")';
            $files = FileRecord::findBySql($sql)->all();
        }
        return $files;
    }
}