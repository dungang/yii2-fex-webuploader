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

class DelAction extends Action
{

    public $saveDir = '/upload/webuploader';

    /**
     * @var string
     */
    public $uploaderDriver;

    /**
     * @var \dungang\webuploader\components\Uploader
     */
    protected $uploader;

    public function run(){
        $result = [
            'jsonrpc'=>'2.0',
        ];
        if ($post = \Yii::$app->request->post()) {
            $delObj = $post['fileObj'];
            unset($post[\Yii::$app->request->csrfParam]);
            unset($post['fileObj']);
            $post['class']=$this->uploaderDriver;
            $post['saveDir'] = $this->saveDir;
            $this->uploader = \Yii::createObject($post);
            if ($this->uploader->deleteFile($delObj)) {
                $result['result'] = $delObj;
            } else {
                $result['error'] = [
                    'code'=> '110',
                    'message' => '文件删除失败',
                ];
            }
            $result['id'] = $this->uploader->id;
        } else {
            $result['error'] = [
                'code'=> '400',
                'message' => '请求不合法',
            ];
        }
        return Json::encode($result);
    }
}