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
use dungang\storage\ActionTrait;

class UploadAction extends Action
{

    use ActionTrait;

    public function run()
    {

        $result = [
            'jsonrpc'=>'2.0',
        ];
        if ($post = \Yii::$app->request->post()) {
            unset($post[\Yii::$app->request->csrfParam]);
            $this->instanceDriver($post);
            $rst = $this->driverInstance->save();
            if ($rst['code'] == 0) {
                $result['result'] = $rst['object']->url;
            } else {
                $result['error'] = [
                    'code'=> $rst['code'],
                    'message' => $rst['message'],
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
            \Yii::$app->response->setStatusCode(500,$result['error']['message']);
        }
        return Json::encode($result);
    }

}