<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '//fonts.googleapis.com/css?family=Open+Sans:400,400i,300,700',
//        'css/site.css', old(origin) styles
        'css/stylesheet.css',
    ];
    public $js = [
        'js/common.js',
    ];
    public $depends = [
        'frontend\assets\FontAwesomeAsset',
        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
