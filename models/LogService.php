<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/2
 * Time: 14:20
 */
namespace app\models;
use Yii;

class LogService{
    public function login($userId){
        $log = new LoginLog();
        $log->l_log_id = md5($userId.date('Y-m-d H:i:s'));
        $log->user_id = $userId;
        $log->login_date = date('Y-m-d H:i:s');
        $log->login_ip = \Yii::$app->request->userIP;
        $log->ip_address = $this->getIPLoc_QQ(\Yii::$app->request->userIP);
        if($log->save()){
            return 'success';
        }else{
            return 'error';
        }
    }

    public function adminLogin($adminId){
        $log = new LoginLog();
        $log->l_log_id = md5($adminId.date('Y-m-d H:i:s'));
        $log->user_id = $adminId;
        $log->login_date = date('Y-m-d H:i:s');
        $log->login_ip  = \Yii::$app->request->userIP;
        $log->ip_address =$this->getIPLoc_QQ(\Yii::$app->request->userIP);
        if($log->save()){
            return 'success';
        }else{
            return 'error';
        }
    }

    public function getLoginLog(){
        $conn = \Yii::$app->db;
        $sql = 'select * from login_log where user_id="'.$_SESSION['user']['user_id'].'" order by login_date desc limit 10';
        $command = $conn->createCommand($sql);
        $logs = $command->queryAll();
        $conn->close();
        return $logs;
    }

    function getIPLoc_QQ($queryIP){
        $url = 'http://ip.qq.com/cgi-bin/searchip?searchip1='.$queryIP;
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_ENCODING ,'gb2312');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        $result = curl_exec($ch);
        if($result == null){
           return '服务器未联网';
        }
        $result = mb_convert_encoding($result, "utf-8", "gb2312"); // 编码转换，否则乱码
        curl_close($ch);
        preg_match("@<span>(.*)</span></p>@iU",$result,$ipArray);
        $loc = $ipArray[1];
        return $loc;
    }

    public function loginStatistics(){
        $conn = \Yii::$app->db;
        $sql = 'select str_to_date(login_date,"%Y-%m-%d") as date,count(*) as num from login_log group by to_days(login_date) order by login_date desc limit 365';
        $command = $conn->createCommand($sql);
        $data = $command->queryAll();
        $conn->close();
        return $data;
    }

    public function removeLog($recordId){
        $removeLog = new RemoveLog();
        $removeLog->f_record_id = $recordId;
        $removeLog->remove_date = date('Y-m-d H:i:s');
        if($removeLog->save()){
            return 'success';
        }else{
            return 'error';
        }
    }

    public function adminLoginLog(){
        $conn = \Yii::$app->db;
        $sql = 'select * from login_log where user_id="'.$_SESSION['admin']['admin_id'].'" order by login_date desc limit 20';
        $command = $conn->createCommand($sql);
        $loginLogs = $command->queryAll();
        $conn->close();
        return $loginLogs;
    }

    public function disableUser($userId,$info,$flag){
        $log = new UserManageLog();
        $log->um_log_id = md5($userId.$_SESSION['admin']['admin_id'].date('Y-m-d H:i:s'));
        $log->user_id = $userId;
        $log->admin_id = $_SESSION['admin']['admin_id'];
        if($flag){
            $log->um_manage_type = '0';
        }else{
            $log->um_manage_type = '1';
        }
        $log->um_manage_info = $info;
        $log->create_date = date('Y-m-d H:i:s');
        if($log->save()){
            return 'success';
        }else{
            return 'error';
        }
    }

    public function setUserSize($userId,$info){
        $log = new UserManageLog();
        $log->um_log_id = md5($userId.$_SESSION['admin']['admin_id'].date('Y-m-d H:i:s'));
        $log->user_id = $userId;
        $log->admin_id = $_SESSION['admin']['admin_id'];
        $log->um_manage_type = '2';
        $log->um_manage_info = $info;
        $log->create_date = date('Y-m-d H:i:s');
        if($log->save()){
            return 'success';
        }else{
            return 'error';
        }
    }

    public function getUserManagerLog($userId){
        $conn = \Yii::$app->db;
        $sql = 'select * from user_manage_log where user_id="'.$userId.'" order by create_date desc limit 10';
        $command = $conn->createCommand($sql);
        $result = $command->queryAll();
        $conn->close();
        return $result;
    }

    public function downloadLog($fileId){
        $log = new DownloadLog();
        $log->d_log_id = md5($fileId.$_SESSION['user']['user_id'].date('Y-m-d H:i:s'));
        $log->user_id = $_SESSION['user']['user_id'];
        $log->file_id = $fileId;
        $log->download_date = date('Y-m-d H:i:s');

        if($log->save()){
            return 'success';
        }else{
            return $log->errors;
        }
    }

    public function mostDownFiles(){
        $conn = \Yii::$app->db;
        $sql = 'select file_id,count(*) as num from download_log
                where file_id not in(select file_manage_log.file_id as file_id from file_manage_log)
                group by file_id order by num desc limit 20';
        $command = $conn->createCommand($sql);
        $data = $command->queryAll();
        for($i=0;$i<count($data);$i++){
            $file = UserFile::findOne($data[$i]['file_id']);
            $data[$i]['file_type'] = $file->filetype;
            $data[$i]['file_size'] = round($file->filesize/(1024*1024),2);
        }
        return $data;
    }

    public function mostUserFiles(){
        $conn = \Yii::$app->db;
        $sql = 'select file_id,count(distinct user_id) as num from download_log
                where file_id not in(select file_manage_log.file_id as file_id from file_manage_log)
                group by file_id order by num desc limit 20';
        $command = $conn->createCommand($sql);
        $data = $command->queryAll();
        for($i=0;$i<count($data);$i++){
            $file = UserFile::findOne($data[$i]['file_id']);
            $data[$i]['file_type'] = $file->filetype;
            $data[$i]['file_size'] = round($file->filesize/(1024*1024),2);
        }
        return $data;
    }

    public function disabledFiles($page){
        $conn = Yii::$app->db;
        $sql = 'select count(*) as counts from file_manage_log';
        $command = $conn->createCommand($sql);
        $result = $command->queryOne();
        $pages = floor($result['counts']/20)+1;

        $sql = 'select * from file_manage_log order by create_date desc limit '.($page*(20-1)).',20';
        $command = $conn->createCommand($sql);
        $files = $command->queryAll();
        for($i=0;$i<count($files);$i++){
            $file = UserFile::findOne($files[$i]['file_id']);
            $files[$i]['file_type'] = $file->filetype;
            $files[$i]['file_size'] = round($file->filesize/(1024*1024),2);
            $admin = Admin::findOne(['admin_id'=>$files[$i]['admin_id']]);
            $files[$i]['admin'] = $admin->admin_account;
        }
        $conn->close();
        $data['pages'] = $pages;
        $data['page'] = $page;
        $data['files'] = $files;
        return $data;
    }

    public function logManageFile($fileId,$info){
        $tran = \Yii::$app->db->beginTransaction();

        $records = FileRecord::findAll(['file_id'=>$fileId]);
        $fileService = new FileService();
        foreach($records as $record){
            if($record->state == '0'){
                $fileService->deleteFile($record->f_record_id,$record->user_id);
            }
        }
        $conn = \Yii::$app->db;
        $sql = 'update file_record set state="2" where file_id="'.$fileId.'"';
        $command = $conn->createCommand($sql);
        if(!$command->execute()){
            $tran->rollBack();
            return 'error';
        }
        $log = new FileManageLog();
        $log->fm_log_id = md5($fileId.$_SESSION['admin']['admin_id'].date('Y-m-d H:i:s'));
        $log->file_id = $fileId;
        $log->admin_id = $_SESSION['admin']['admin_id'];
        $log->fm_manage_type = '1';                         //禁用文件
        $log->fm_manage_info = $info;
        $log->create_date = date('Y-m-d H:i:s');

        if($log->save()){
            $tran->commit();
            return 'success';
        }else{
            $tran->rollBack();
            return $log->errors;
        }
    }
}