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
use dungang\storage\Driver;
use dungang\storage\StorageEvent;

class UploadAction extends Action
{

    use ActionTrait;

    public $accept;

    public function run()
    {
        $result = [
            'jsonrpc'=>'2.0',
        ];
        if ($post = \Yii::$app->request->post()) {
            unset($post[\Yii::$app->request->csrfParam]);
            $this->instanceDriver($post);
            $this->driverInstance->initFile();
            $file = $this->driverInstance->file;
            if ($file->error === 0) {
                if ($this->checkExtension($file)) {
                    $event = new StorageEvent();
                    $this->driverInstance->trigger(Driver::EVENT_BEFORE_WRITE_FILE,$event);
                    if ($file = $this->driverInstance->writeFile()){
                        $result['result'] = $file;
                        $event->file = $file;
                        $this->driverInstance->trigger(Driver::EVENT_AFTER_WRITE_FILE,$event);
                    } else {
                        $result['error'] = [
                            'code'=> '100',
                            'message' => '上传失败',
                        ];
                    }
                } else {
                    $result['error'] = [
                        'code'=> '401',
                        'message' => '不允许上传'.$file->extension.'格式的文件',
                    ];
                }

            } else {
                $result['error'] = [
                    'code'=> 100 + $file->error,
                    'message' => $this->driverInstance->message($file->error),
                ];
            }
            $result['id'] = $this->driverInstance->id;
            $result['chunk'] = $this->driverInstance->chunk;
            $result['chunks'] = $this->driverInstance->chunks;
            $result['extraData'] = json_encode($this->driverInstance->extraData);
        } else {
            $result['error'] = [
                'code'=> '400',
                'message' => '请求不合法',
            ];
        }
        if (isset($result['error'])) {
            \Yii::$app->response->setStatusCode(500);
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