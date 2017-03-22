<?php
/**
 * Created by PhpStorm.
 * User: dungang
 * Date: 2017/3/22
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
    public $driver;

    /**
     * @var \dungang\storage\Driver
     */
    protected $driverInstance;

    /**
     * @param $post array
     */
    public function instanceDriver($post)
    {
        $props = get_class_vars('\dungang\storage\Driver');
        $post['saveDir'] = $this->saveDir;
        $config = [];
        foreach($props as $prop=>$def) {
           if (isset($post[$prop])) {
               $config[$prop] = $post[$prop];
           } else {
               $config[$prop] = $def;
           }
        }
        if (is_array($this->driver)){
            $this->driver = array_merge($this->driver,$config);
            $this->driverInstance = \Yii::createObject($this->driver);
        } else {
            $config['class']=$this->driver;
            $this->driverInstance = \Yii::createObject($config);
        }
    }
}