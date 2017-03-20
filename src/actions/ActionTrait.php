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
        $props = get_class_vars('dungang\webuploader\components\Uploader');

        $post['saveDir'] = $this->saveDir;

        $config = [];
        foreach($props as $prop=>$def) {
           if (isset($post[$prop])) {
               $config[$prop] = $post[$prop];
           } else {
               $config[$prop] = $def;
           }
        }
        if (is_array($this->uploaderDriver)){
            $this->uploaderDriver = array_merge($this->uploaderDriver,$config);
            $this->uploader = \Yii::createObject($this->uploaderDriver);
        } else {
            $config['class']=$this->uploaderDriver;
            $this->uploader = \Yii::createObject($config);
        }
    }
}