<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class LoginAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/user_login.css',
        'bootstrap/css/bootstrap.min.css',
    ];
    public $js = [
        'js/jquery-2.2.0.min.js',
        'bootstrap/js/bootstrap.min.js',
        'js/user_login.js'
    ];
    public $depends = [
    ];
}
