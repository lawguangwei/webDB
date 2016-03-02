<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/2
 * Time: 14:20
 */
namespace app\models;

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

    public function getLoginLog(){
        $conn = \Yii::$app->db;
        $sql = 'select * from login_log where user_id="'.$_SESSION['user']['user_id'].'" order by login_date desc limit 10';
        $command = $conn->createCommand($sql);
        $logs = $command->queryAll();
        return $logs;
    }

    function getIPLoc_QQ($queryIP){
        $url = 'http://ip.qq.com/cgi-bin/searchip?searchip1='.$queryIP;
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_ENCODING ,'gb2312');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        $result = curl_exec($ch);
        $result = mb_convert_encoding($result, "utf-8", "gb2312"); // 编码转换，否则乱码
        curl_close($ch);
        preg_match("@<span>(.*)</span></p>@iU",$result,$ipArray);
        $loc = $ipArray[1];
        return $loc;
    }
}