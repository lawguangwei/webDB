<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/3
 * Time: 20:22
 */
namespace app\models;

use yii\db\ActiveRecord;

class Admin extends ActiveRecord{
    private $admin_id;
    private $admin_account;
    private $admin_password;
    private $state;

    public static function tableName(){
        return 'admin';
    }
}