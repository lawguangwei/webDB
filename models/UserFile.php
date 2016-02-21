<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 15/11/11
 * Time: 09:53
 */
namespace app\models;
use yii\mongodb\file\ActiveRecord;

class UserFile extends ActiveRecord{

    public static function collectionName (){
        return 'files';
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['filetype']);
    }

}