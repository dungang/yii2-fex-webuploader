<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/3/4
 * Time: 9:00
 */

namespace dungang\webuploader\components;


use yii\base\Event;

class UploaderEvent extends Event
{
    /**
     * @var string
     */
    public $file;

}