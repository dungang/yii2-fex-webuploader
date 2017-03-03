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

    public $saveDir = '/upload/webuploader';

    /**
     * @var string
     */
    public $uploaderDriver;

    public $accept = null;

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
            $post['class']=$this->uploaderDriver;
            $post['saveDir'] = $this->saveDir;
            $this->uploader = \Yii::createObject($post);
            $this->uploader->initFile();
            if ($this->uploader->file->error === 0) {

                if ($this->checkExtension($this->uploader->file)) {
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
                        'code'=> '112',
                        'message' => '不允许上传'.$this->uploader->file->extension.'格式的文件',
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

    /**
     * @param $file \yii\web\UploadedFile
     * @return bool
     */
    protected function checkExtension($file) {
        //如果是数组，则必须按照列表检查
        if (is_array($this->accept)) {
            if (in_array($file->extension,$this->accept)) {
                return true;
            }
            return false;
        }
        //如果不是数组，则不检查文件后缀
        return true;
    }
}