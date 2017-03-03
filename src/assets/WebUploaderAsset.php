<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/3/2
 * Time: 9:39
 */

namespace dungang\webuploader\assets;


use yii\web\AssetBundle;

class WebUploaderAsset extends AssetBundle
{
    public $sourcePath = '@bower/fex-webuploader/dist';
    public $css = [
        'webuploader.css'
    ];
    public $js = [
        'webuploader.js'
    ];
    public $depends = [
        'yii\web\YiiAsset'
    ];

}