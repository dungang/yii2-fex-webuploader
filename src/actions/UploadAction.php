<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/3/2
 * Time: 20:35
 */

namespace dungang\webuploader\actions;

use yii\base\Action;
use yii\helpers\Json;

class UploadAction extends Action
{
    /**
     * @var \dungang\webuploader\controllers\FileController
     */
    public $controller;

    public $saveDir = '/upload/webuploader';

    /**
     * @var string
     */
    public $uploaderDriver;

    /**
     * @var \dungang\webuploader\components\Uploader
     */
    protected $uploader;

    public function run()
    {
        $result = [
            'jsonrpc'=>'2.0',
        ];
        if ($post = \Yii::$app->request->post()) {
            unset($post[\Yii::$app->request->csrfParam]);
            $post['class']=$this->controller->module->driver;
            $post['saveDir'] = $this->saveDir;
            $this->uploader = \Yii::createObject($post);
            $this->uploader->initFile();
            if ($this->uploader->file->error === 0) {

                if ($file = $this->uploader->writeFile()){
                    $result['result'] = $file;
                } else {
                    $result['error'] = [
                        'code'=> '100',
                        'message' => '上传失败',
                    ];
                }

            } else {
                $result['error'] = [
                    'code'=> 100 + $this->uploader->file->error,
                    'message' => $this->uploader->message($this->uploader->file->error),
                ];
            }
            $result['id'] = $this->uploader->id;
            $result['chunk'] = $this->uploader->chunk;
            $result['chunks'] = $this->uploader->chunks;
        } else {
            $result['error'] = [
                'code'=> '400',
                'message' => '请求不合法',
            ];
        }
        return Json::encode($result);
    }
}