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

    /**
     * @var string 上传文件的驱动
     */
    public $driver = 'dungang\webuploader\components\LocalUploader';

    /**
     * @var string 上传文件保存的相对路径
     */
    public $saveDir = '/upload/webuploader';

    /**
     * @var array 接受的文件类型
     */
    public $accept = ['gif','jpg','png','bmp','docx','doc','ppt','xsl','rar','zip','7z'];

}