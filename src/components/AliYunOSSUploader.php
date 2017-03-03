<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/3/3
 * Time: 16:46
 */

namespace vendor\dungang\webuploader\components;


use dungang\webuploader\components\Uploader;
use yii\helpers\BaseFileHelper;

/**
 * @var $result \Aliyun\OSS\Models\InitiateMultipartUploadResult
 * @var $partResult  \Aliyun\OSS\Models\UploadPartResult
 */
class AliYunOSSUploader extends Uploader
{

    const CONTENT = 'Content';
    const BUCKET = 'Bucket';
    const KEY = 'Key';
    const UPLOAD_ID = 'UploadId';
    const PART_NUMBER = 'PartNumber';
    const PART_SIZE = 'PartSize';

    /**
     * @var \Aliyun\OSS\OSSClient
     */
    public $client;

    /**
     * @var string OSS Bucket
     */
    public $bucket;

    /**
     * @return bool|string
     */
    public function writeFile()
    {
        $fileName = md5($this->guid . $this->id);

        $file = $fileName . '.' . $this->file->extension;

        $dir = $this->saveDir .DIRECTORY_SEPARATOR. date('Y-m-d');

        $partNumber = 1 + $this->chunk;

        $key = BaseFileHelper::normalizePath($dir . DIRECTORY_SEPARATOR . $file);



        if($this->chunked) {
            if ($this->chunk === 0 ) {

                $result = $this->client->initiateMultipartUpload([
                    self::BUCKET =>$this->bucket,
                    self::KEY => $key
                ]);
                if ($result && $result->getUploadId() ) {
                    $this->extraData = $result->getUploadId();
                }
            }

            $objResult = $this->client->uploadPart([
                    self::BUCKET => $this->bucket,
                    self::KEY => $key,
                    self::UPLOAD_ID => $this->extraData,
                    self::CONTENT => file_get_contents($this->file->tempName),
                    self::PART_NUMBER => $partNumber,
                    self::PART_SIZE => $this->chunkFileSize
            ]);
            if ($objResult && $objResult->getETag()){
                return $key;
            }

        } else {

            $objResult = $this->client->putObject([
                self::BUCKET => $this->bucket,
                self::KEY => $key,
                self::CONTENT => file_get_contents($this->file->tempName),
            ]);

            if ($objResult && $objResult->getETag()){
                return $key;
            }
        }
        return false;
    }

    public function deleteFile($file)
    {
        $key = BaseFileHelper::normalizePath(ltrim($file,'/\\'));
        $this->client->deleteObject([
            self::BUCKET => $this->bucket,
            self::KEY => $key,
        ]);
        return true;
    }
}