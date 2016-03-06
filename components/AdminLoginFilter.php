<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/3
 * Time: 20:10
 */
namespace app\components;

use Yii;
use yii\base\ActionFilter;
use yii\helpers\Url;


class AdminLoginFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        if(!isset($_SESSION['admin'])){
            Yii::$app->controller->redirect(Url::base().'/index.php?r=admin/login');
        }
        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        return parent::afterAction($action, $result);
    }
}