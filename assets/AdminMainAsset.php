<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 16/3/2
 * Time: 22:46
 */
namespace app\assets;

use yii\web\AssetBundle;

class AdminMainAsset extends AssetBundle{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/admin_main.css',
        'bootstrap/css/bootstrap.min.css',
    ];
    public $js = [
        'js/jquery-2.2.0.min.js',
        'bootstrap/js/bootstrap.min.js',
        'Highcharts/js/highcharts.js',
        'Highcharts/js/highcharts-3d.js',
        'Highcharts/js/modules/exporting.js',
        'js/admin_index.js'
    ];
    public $depends = [
    ];
}