<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 15/11/15
 * Time: 18:45
 */
namespace app\models;

use yii\base\Exception;
use yii\debug\models\search\Log;

class FileService{
    public function uploadFile($fileName,$fileType,$fileSize,$file){

        $userFile = UserFile::findOne(['md5'=>md5_file($file)]);
        if($userFile == null){
            $userFile = new UserFile();
            $userFile->filename = $fileName;
            $userFile->filetype = $fileType;
            $userFile->filesize = $fileSize;
            $userFile->file = $file;
            if(!$userFile->save()){
                return '2';
            }
        }

        $disk = Disk::findOne(['user_id'=>$_SESSION['user']['user_id']]);


        try{
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
        $tran = \Yii::$app->db->beginTransaction();
        foreach($childs as $child){
            if($child->f_record_type == '2'){
                $this->deleteFolder($child->f_record_id);
            }
            if($child->f_record_type == '1'){
                $this->deleteFile($child->f_record_id);
            }
        }
        $folder->state = '1';
        if($folder->save()){
            $logService = new LogService();
            if($logService->removeLog($folder->f_record_id) == 'success'){
                $tran->commit();
                return 'success';
            }
            $tran->rollBack();
            return 'error';
        }else{
            $tran->rollBack();
            return 'error';
        }
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
                $logService = new LogService();
                if($logService->removeLog($fileRecord->f_record_id) != 'success'){
                    $tran->rollBack();
                    return 'error';
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
                        return '服务器错误';                                                                 //修改父目录信息错误
                    }
                }
                if($file->f_record_type == '2'){
                    $this->pasteFolder($record_id,$_SESSION['current_id']);
                }
                if($file->f_record_type == '1'){
                    $this->pasteFile($record_id,$_SESSION['current_id']);
                }
            }
            if($disk->save()){
                $tran->commit();
                return 'success';
            }
            $tran->rollBack();
            return '服务器错误';                                                                                //磁盘修改错误
        }catch (Exception $e){
            $tran->rollBack();
            return $e->getMessage();                                                                        //数据库错误
        }
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

    public function typeSize(){
        $conn = \Yii::$app->db;
        $sql = 'select sum(file_size) as size from file_record where user_id="'.$_SESSION['user']['user_id'].'" and state="0" and extension in("jpg","jpeg","png","gif")';
        $command = $conn->createCommand($sql);
        $result = $command->queryOne();
        if($result != null){
            $typeSize['picture'] = $result['size'];
        }else{
            $typeSize['picture'] = 0;
        }
        $sql = 'select sum(file_size) as size from file_record where user_id="'.$_SESSION['user']['user_id'].'" and state="0" and extension in("txt","doc","ppt","xls","pdf","docx","xlsx","pptx")';
        $command = $conn->createCommand($sql);
        $result = $command->queryOne();
        if($result != null){
            $typeSize['word'] =  $result['size'];
        }else{
            $typeSize['word'] = 0;
        }
        $sql = 'select sum(file_size) as size from file_record where user_id="'.$_SESSION['user']['user_id'].'" and state="0" and extension in("mp3","wav","wma","ogg","ape","acc")';
        $command = $conn->createCommand($sql);
        $result = $command->queryOne();
        if($result != null){
            $typeSize['music'] =  $result['size'];
        }else{
            $typeSize['music'] = 0;
        }
        $sql = 'select sum(file_size) as size from file_record where user_id="'.$_SESSION['user']['user_id'].'" and state="0" and extension in("avi","rm","rmvb","mov","wmv","mp4","mkv","mpeg")';
        $command = $conn->createCommand($sql);
        $result = $command->queryOne();
        if($result != null){
            $typeSize['film'] =  $result['size'];
        }else{
            $typeSize['film'] = 0;
        }
        $sql = 'select sum(file_size) as size from file_record where user_id="'.$_SESSION['user']['user_id'].'" and state="0" and extension not in("mp3","wav","wma","ogg","ape","acc","jpg","jpeg","png","gif",
            "txt","doc","ppt","xls","pdf","docx","xlsx","pptx","avi","rm","rmvb","mov","wmv","mp4","mkv","mpeg")';
        $command = $conn->createCommand($sql);
        $result = $command->queryOne();
        if($result != null){
            $typeSize['other'] =  $result['size'];
        }else{
            $typeSize['other'] = 0;
        }
        $conn->close();
        return $typeSize;
    }

    public function adminStatisticsSize(){
        $conn = \Yii::$app->db;
        $sql = 'select str_to_date(upload_date,"%Y-%m-%d") as date,sum(file_size) as size from file_record where state = "0" group by to_days(upload_date) order by upload_date desc limit 365';
        $command = $conn->createCommand($sql);
        $result = $command->queryAll();
        $data['add'] = $result;
        $sql = 'select str_to_date(upload_date,"%Y-%m-%d") as date,sum(file_size) as size from file_record where state = "1" group by to_days(upload_date) order by upload_date desc limit 365';
        $command = $conn->createCommand($sql);
        $result = $command->queryAll();
        $data['delete'] = $result;
        $conn->close();
        return $data;
    }

    public function countFile(){
        $conn = \Yii::$app->db;
        $sql = 'select count(*) as num from file_record where state="0"';
        $command = $conn->createCommand($sql);
        $result = $command->queryAll();
        $conn->close();
        return $result['0']['num'];
    }

    public function countFileSize(){
        $result = UserFile::find()->sum('filesize');
        return $result;
    }

    public function recycleFiles(){
        $conn = \Yii::$app->db;
        $sql = 'select * from file_record inner join remove_log on file_record.f_record_id = remove_log.f_record_id where
          f_record_type = "1" and file_record.state="1" and file_record.user_id="'.$_SESSION['user']['user_id'].'"
          and to_days(now()) - TO_DAYS(remove_log.remove_date) < 10 order by remove_log.remove_date desc ';
        $command = $conn->createCommand($sql);
        $result = $command->queryAll();
        $conn->close();
        return $result;
    }

    public function revertFiles($files){
        $tran = \Yii::$app->db->beginTransaction();
        try{
            foreach($files as $recordId){
                $fileRecord = FileRecord::findOne(['f_record_id'=>$recordId]);
                $disk = Disk::findOne(['user_id'=>$_SESSION['user']['user_id']]);
                if($disk->available_size < $fileRecord->file_size){
                    $tran->rollBack();
                    return '2';                                                                             //空间不足
                }
                $fileRecord->state = '0';
                $fileRecord->parent_id = $_SESSION['user']['user_id'];
                if(!$fileRecord->save()){
                    $tran->rollBack();
                    return '1';                                                                             //还原错误
                }
                $disk->available_size = $disk->available_size - $fileRecord->file_size;
                if(!$disk->save()){
                   $tran->rollBack();
                    return '1';                                                                             //还原错误
                }
                $removeLog = RemoveLog::findOne(['f_record_id'=>$fileRecord->f_record_id]);
                if(!$removeLog->delete()){
                    $tran->rollBack();
                    return '1';                                                                             //还原错误
                }
            }
            $tran->commit();
            return '0';
        }catch (Exception $e){
            $tran->rollBack();
            return '1';
        }
    }
}