<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/3/2
 * Time: 10:09
 */

namespace dungang\webuploader\assets;


use yii\web\AssetBundle;

class JQueryWebUploaderAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . '/js';

    public $js = [
        'jquery-webuploader.js',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset'
    ];
}