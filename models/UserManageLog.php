<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/6
 * Time: 19:32
 */
namespace app\models;

use yii\db\ActiveRecord;

class UserManageLog extends ActiveRecord{
    private $um_log_id;
    private $user_id;
    private $admin_id;
    private $um_manage_type;
    private $um_manage_info;
    private $create_date;

    public static function tableName(){
        return 'user_manage_log';
    }
}