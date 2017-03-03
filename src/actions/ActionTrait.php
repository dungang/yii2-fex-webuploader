<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/3/3
 * Time: 18:26
 */

namespace dungang\webuploader\actions;


trait ActionTrait
{

    /**
     * @var string
     */
    public $saveDir = 'upload/webuploader';

    /**
     * @var string|array
     */
    public $uploaderDriver;

    /**
     * @var \dungang\webuploader\components\Uploader
     */
    protected $uploader;

    /**
     * @param $post array
     */
    public function instanceDriver($post)
    {
        $post['saveDir'] = $this->saveDir;
        if (is_array($this->uploaderDriver)){
            $this->uploaderDriver = array_merge($this->uploaderDriver,$post);
            $this->uploader = \Yii::createObject($this->uploaderDriver);
        } else {
            $post['class']=$this->uploaderDriver;
            $this->uploader = \Yii::createObject($post);
        }
    }
}