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
     * @var array 访问角色 默认是登录用户才可以
     */
    public $role = ['@'];
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'dungang\webuploader\controllers';

    /**
     * @var string|array 上传文件的驱动
     */
    public $driver = 'dungang\storage\driver\Local';

    /**
     * @var string 上传文件保存的相对路径
     */
    public $saveDir = 'upload/webuploader';

    /**
     * @var array 接受的文件类型
     */
    public $accept = ['gif','jpg','png','bmp','docx','doc','ppt','xsl','rar','zip','7z'];

}