<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/3/2
 * Time: 11:00
 */

namespace dungang\webuploader;


class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'dungang\webuploader\controllers';

    public $driver = 'dungang\webuploader\components\LocalUploader';

    public $saveDir = '/upload/webuploader';

}