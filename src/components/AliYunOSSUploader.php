<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/3/3
 * Time: 16:46
 */

namespace dungang\webuploader\components;


use yii\helpers\BaseFileHelper;

/**
 * @var $result \Aliyun\OSS\Models\InitiateMultipartUploadResult
 * @var $partResult  \Aliyun\OSS\Models\UploadPartResult
 * @var $clientClass  string| \Aliyun\OSS\OSSClient
 */
class AliYunOSSUploader extends Uploader
{

    const CONTENT = 'Content';
    const BUCKET = 'Bucket';
    const KEY = 'Key';
    const UPLOAD_ID = 'UploadId';
    const PART_NUMBER = 'PartNumber';
    const PART_SIZE = 'PartSize';

    const ACCESS_KEY_ID = 'AccessKeyId';
    const ACCESS_KEY_SECRET = 'AccessKeySecret';
    const ENDPOINT = 'Endpoint';

    public $paramKey = 'oss';

    protected $config;

    /**
     * @var \Aliyun\OSS\OSSClient
     */
    protected $client;

    /**
     * @var string OSS Bucket
     */
    public $bucket;

    public function init()
    {
        parent::init();

        $this->config = \Yii::$app->params[$this->paramKey];
        $JohnLuiOSS = '\JohnLui\AliyunOSS\AliyunOSS';
        $clientClass= '\Aliyun\OSS\OSSClient';
        //由于JohnLuiOSS的ossClient不是公开属性，所以先实例化，加载文件
        $JohnLuiOSS::boot(
            $this->config['EndPoint'],
            $this->config['AccessKeyId'],
            $this->config['AccessKeySecret']
        );
        //再次实例化 OssClient
        $this->client = $clientClass::factory([
            self::ENDPOINT => $this->config['EndPoint'],
            self::ACCESS_KEY_ID       => $this->config['AccessKeyId'],
            self::ACCESS_KEY_SECRET   => $this->config['AccessKeySecret'],
        ]);

        $this->bucket = $this->config['Bucket'];

    }

    /**
     * @return bool|string
     */
    public function writeFile()
    {
        $fileName = md5($this->guid . $this->id);

        $file = $fileName . '.' . $this->file->extension;

        $dir = $this->saveDir .DIRECTORY_SEPARATOR. date('Y-m-d');

        $partNumber = 1 + $this->chunk;
        //aliyun oss 路径分隔符用'/'
        $key = BaseFileHelper::normalizePath($dir . DIRECTORY_SEPARATOR . $file,'/');

        //除了最后一块Part，其他Part的大小不能小于100KB，否则会导致在调用CompleteMultipartUpload接口的时候失败
        $minChunkSize = 100 * 1024;
        if($this->size >= $minChunkSize && $this->chunked) {
            if ($this->chunk === 0 ) {
                $result = $this->client->initiateMultipartUpload([
                    self::BUCKET =>$this->bucket,
                    self::KEY => $key
                ]);
                if ($result && $result->getUploadId() ) {
                    $this->extraData = $result->getUploadId();
                }
            }
            $handle = fopen($this->file->tempName, 'r');
            $objResult = $this->client->uploadPart([
                    self::BUCKET => $this->bucket,
                    self::KEY => $key,
                    self::UPLOAD_ID => $this->extraData,
                    self::CONTENT => $handle,
                    self::PART_NUMBER => $partNumber,
                    self::PART_SIZE => $this->chunkFileSize
            ]);
            fclose($handle);

            if ($objResult && $objResult->getETag()){
                return $key;
            }

        } else {
            $handle = fopen($this->file->tempName, 'r');
            $objResult = $this->client->putObject([
                self::BUCKET => $this->bucket,
                self::KEY => $key,
                self::CONTENT => $handle,
                'ContentLength' => $this->chunkFileSize,
            ]);
            fclose($handle);

            if ($objResult && $objResult->getETag()){
                return $key;
            }
        }
        return false;
    }

    public function deleteFile($file)
    {
        $key = BaseFileHelper::normalizePath(ltrim($file,'/\\'),'/');
        $this->client->deleteObject([
            self::BUCKET => $this->bucket,
            self::KEY => $key,
        ]);
        return true;
    }
}