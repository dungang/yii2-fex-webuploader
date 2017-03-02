<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/3/2
 * Time: 11:19
 */

namespace dungang\webuploader\components;


use yii\base\Component;
use yii\web\UploadedFile;

abstract class Uploader extends Component
{
    /**
     * @var string 文件表单字段名称
     */
    public $fieldName = 'file';

    /**
     * @var string 文件名称
     */
    public $name = '';

    /**
     * @var string 文件类型
     */
    public $type = '';

    /**
     * @var string 文件最后修改的时间
     */
    public $lastModifiedDate = '';

    /**
     * @var bool 是否分片上传文件
     */
    public $chunked = false;

    /**
     * @var int chunk总数量
     */
    public $chunks = 1;

    /**
     * @var int 当前chunk编号
     */
    public $chunk = 0;

    /**
     * @var int chunk的大小
     */
    public $chunkSize = 5 * 1024 * 1024;

    /**
     * @var int 当前文件大小
     */
    public $size = 0;

    /**
     * @var int chunk实际上传大小
     */
    public $chunkFileSize = 0;

    /**
     * @var string 回话id
     */
    public $guid = '';

    /**
     * @var string 文件唯一id WU_FILE_0
     */
    public $id;

    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * @var string 文件保存路径
     */
    public $saveDir = '/upload/webuploader';

    public function initFile()
    {
        $this->file = UploadedFile::getInstanceByName($this->fieldName);
    }


    public function message($code)
    {
        switch($code){
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $err =  '上传的文件大小超出了限制';
                break;
            case UPLOAD_ERR_PARTIAL:
                $err = '文件未完整上传';
                break;
            case UPLOAD_ERR_NO_FILE:
                $err = '没有找到上传文件';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $err = "找不到临时文件夹";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $err = "文件写入失败";
                break;
            case UPLOAD_ERR_EXTENSION:
                $err = "文件停止了上传";
                break;
            default:
                $err = '不明错误！';
        }
        return $err;
    }

    /**
     * @return mixed
     */
    abstract public function writeFile();

    /**
     * @param $file string
     * @return mixed
     */
    abstract public function deleteFile($file);

    /**
     * @return string 生产guid
     */
    static public function guid() {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
            .substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12)
            .chr(125);// "}"
        return $uuid;
    }
}