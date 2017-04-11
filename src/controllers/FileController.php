<?php
/**
 * Created by PhpStorm.
 * User: dungang
 * Date: 2017/3/2
 * Time: 9:23
 */

namespace dungang\webuploader\controllers;

use dungang\storage\ControllerTrait;
use yii\web\Controller;

class FileController extends Controller
{
    use ControllerTrait;

    public function actions()
    {
        $module = $this->module;
        return [
            'upload'=>[
                'class'=>'dungang\webuploader\actions\UploadAction',
                'accept' => $module->accept
            ],
            'delete' => [
                'class' => 'dungang\webuploader\actions\DelAction',
            ]
        ];
    }
}