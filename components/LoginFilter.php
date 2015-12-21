<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 15/11/8
 * Time: 15:39
 */
namespace app\components;

use Yii;
use yii\base\ActionFilter;
use yii\helpers\Url;


class LoginFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        if(!isset($_SESSION['user'])){
            Yii::$app->controller->redirect(Url::base().'/index.php?r=user/login');
        }
        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        return parent::afterAction($action, $result);
    }
}