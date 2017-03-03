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
use yii\bootstrap\InputWidget;
use yii\helpers\Url;
use yii\web\Request;

class WebUploader extends InputWidget
{
    /**
     * @var string 文件上传入口
     */
    public $uploadPoint;

    /**
     * @var string 删除文件入口
     */
    public $delPoint;

    /**
     * @var bool 是否自动开启上传
     */
    public $auto = true;

    /**
     * @var bool 分片上传大文件
     */
    public $chunked = false;

    /**
     * @var int 上传文件数量限制
     */
    public $fileNumLimit = 1;

    /**
     * @var int 单个文件大小限制
     */
    public $fileSingleSizeLimit = 100 * 1024 * 1024;


    public $chunkSize = 5 * 1024 * 1024;

    /**
     * @var array
     */
    public $formData = [];


    public function run()
    {

        $size = $this->string2bytes(ini_get('upload_max_filesize'));

        if ($size < $this->chunkSize) {

            $this->chunkSize = $size;
        }

        $request = \Yii::$app->getRequest();
        if ($request instanceof Request && $request->enableCsrfValidation) {
            $this->formData[$request->csrfParam]=$request->getCsrfToken();
        }
        if (empty($this->uploadPoint)) {
            $this->uploadPoint = Url::to(['/webuploader/file/upload']);
        }
        if (empty($this->delPoint)) {
            $this->delPoint = Url::to(['/webuploader/file/delete']);
        }
        if ($this->chunked){
            $this->formData['chunkSize'] = $this->chunkSize;
        }

        $webUploader = WebUploaderAsset::register($this->getView());
        JQueryWebUploaderAsset::register($this->getView());
        $pick = $this->id . '-filePicker';
        $this->clientOptions['delPoint'] = $this->delPoint;
        $this->clientOptions['options']['swf'] = $webUploader->baseUrl . '/Uploader.swf';
        $this->clientOptions['options']['pick'] = '#'.$pick;
        $this->clientOptions['options']['server'] = $this->uploadPoint;
        $this->clientOptions['options']['auto'] = $this->auto;
        $this->clientOptions['options']['chunked'] = $this->chunked;
        $this->clientOptions['options']['chunkSize'] = $this->chunkSize;
        $this->clientOptions['options']['fileNumLimit'] = $this->fileNumLimit;
        $this->clientOptions['options']['fileSingleSizeLimit'] = $this->fileSingleSizeLimit;
        $this->clientOptions['options']['formData'] = $this->formData;
        $this->registerPlugin('webuploader');
        $input = $this->renderInput($this->id);
        return Html::tag('div', $input .
            '<div  class="list-group uploader-list"></div><div id="'.$pick.'">选择图片</div>',
            $this->options);
    }

    protected function renderInput($id)
    {
        if ($this->hasModel()) {
            return Html::activeHiddenInput($this->model, $this->attribute, ['id' => $id . $this->attribute]);
        } else {
            return Html::hiddenInput($this->name, $this->value, ['id' => $id . $this->name]);
        }
    }

    protected function string2bytes($size)
    {
        $units = [
            'B'=>0,
            'K'=>1,
            'KB'=>1,
            'M'=>2,
            'MB'=>2,
            'G'=>3,
            'GB'=>3,
            'T'=>4,
            'TB'=>4,
            'P'=>5,
            'PB'=>5,
        ];
        if (preg_match('/(\d+)(\w*)/i',$size,$matches)) {
            if (!empty($matches[2])) {
                $unit = strtoupper($matches[2]);
                if (isset($units[$unit])) {
                    return intval($matches[1]) * pow(1024,$units[$unit]);
                }
            }
            return intval($matches[1]);
        }
        return 0;
    }
}