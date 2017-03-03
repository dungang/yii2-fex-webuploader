<?php
/**
 * Created by PhpStorm.
 * User: dungang
 * Date: 2017/3/2
 * Time: 9:23
 */

namespace dungang\webuploader\controllers;

use yii\web\Controller;

class FileController extends Controller
{
    /**
     * @var \dungang\webuploader\Module
     */
    public $module;

    public function actions()
    {
        $module = $this->module;
        return [
            'upload'=>[
                'class'=>'dungang\webuploader\actions\UploadAction',
                'uploaderDriver'=>$module->driver,
                'saveDir' => $module->saveDir,
                'accept' => $module->accept
            ],
            'delete' => [
                'class' => 'dungang\webuploader\actions\DelAction',
                'uploaderDriver'=>$module->driver,
                'saveDir' => $module->saveDir,
            ]
        ];
    }
}