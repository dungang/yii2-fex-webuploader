<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/3/2
 * Time: 21:05
 */

namespace dungang\webuploader\actions;

use yii\base\Action;
use yii\helpers\Json;
use dungang\storage\Driver;
use dungang\storage\StorageAction;

class DelAction extends StorageAction
{
    public function run(){
        $result = [
            'jsonrpc'=>'2.0',
        ];
        if ($post = \Yii::$app->request->post()) {
            unset($post[\Yii::$app->request->csrfParam]);
            if(isset($post['fileObj'])) {
                $delObj = $post['fileObj'];
                unset($post['fileObj']);
                if ($this->driver->deleteUpload($delObj)) {
                    $result['result'] = $delObj;
                } else {
                    $result['error'] = [
                        'code'=> '110',
                        'message' => '文件删除失败',
                    ];
                }
            }
            $result['id'] = $post['id'];
        } else {
            $result['error'] = [
                'code'=> '400',
                'message' => '请求不合法',
            ];
        }
        return Json::encode($result);
    }
}