<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/2
 * Time: 22:46
 */
namespace app\assets;

use yii\web\AssetBundle;

class ShareAsset extends AssetBundle{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'bootstrap/css/bootstrap.min.css',
    ];
    public $js = [
        'js/jquery-2.2.0.min.js',
        'bootstrap/js/bootstrap.min.js',
        'js/share-file.js'
    ];
    public $depends = [
    ];
}