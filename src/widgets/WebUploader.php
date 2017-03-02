<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/3/2
 * Time: 9:46
 */

namespace dungang\webuploader\widgets;


use dungang\webuploader\assets\JQueryWebUploaderAsset;
use dungang\webuploader\assets\WebUploaderAsset;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;

class WebUploader extends Widget
{
    public $server;

    public function run()
    {
        $webUploader = WebUploaderAsset::register($this->getView());
        JQueryWebUploaderAsset::register($this->getView());
        $pick = $this->id . '-filePicker';
        $this->clientOptions['options']['swf'] = $webUploader->baseUrl . '/Uploader.swf';
        $this->clientOptions['options']['pick'] = $pick;
        $this->clientOptions['options']['url'] = $this->server;
        $this->registerPlugin('webuploader');
        return Html::tag('div',
            '<div  class="uploader-list"></div><div id="'.$pick.'">选择图片</div>',
            $this->options);
    }
}