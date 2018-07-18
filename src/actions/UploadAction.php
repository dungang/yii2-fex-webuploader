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
use dungang\storage\StorageAction;

class UploadAction extends StorageAction
{
    
    public $accept;

    public function run()
    {

        $this->driver->accept = $this->accept;
        $result = [
            'jsonrpc'=>'2.0',
        ];
        if ($post = \Yii::$app->request->post()) {
            unset($post[\Yii::$app->request->csrfParam]);
            $rst = $this->driver->chunkUpload();
            if ($rst->isOk) {
                $result['result'] = $rst->url;
            } else {
                $result['error'] = [
                    'code'=> 500,
                    'message' => $rst->error,
                ];
            }
            $result['uploadId'] = $rst->uploadId;
            $result['key'] = $rst->key;
            $result['extraData'] = json_encode($rst->extraData);
        } else {
            $result['error'] = [
                'code'=> '400',
                'message' => '请求不合法',
            ];
        }
        if (isset($result['error'])) {
            \Yii::$app->response->setStatusCode(500,$result['error']['message']);
        }
        return Json::encode($result);
    }

}