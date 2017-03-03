# yii2 fex-webuploader

![效果图](example.png)

## 使用方法

> 下载验证码包

```

composer require dungang/geetest

```

> 配置模块 config/web.php


```

'webuploader'=>[
    'class'=>'dungang\webuploader\Module',
//下面是默认配置    
//    [
        /**
         * @var string 上传文件的驱动
         */
//        'driver' => 'dungang\webuploader\components\LocalUploader',
    
        /**
         * @var string 上传文件保存的相对路径
         */
//        'saveDir' => '/upload/webuploader',
    
        /**
         * @var array 接受的文件类型
         */
//        'accept' => ['gif','jpg','png','bmp','docx','doc','ppt','xsl','rar','zip','7z']
//    ]
],
    
```

> 配置widget

```
<?= $form->field($model, 'file')->widget('\dungang\webuploader\widgets\WebUploader',[
    'chunked'=>true,
    'name' =>'pack'
]) ?>

<?= \dungang\webuploader\widgets\WebUploader::widget([
    'chunked'=>true,
    'name' =>'pack'
])?>

```

> 配置参数如下

```
    /**
     * @var string 文件上传入口
     */
    'uploadPoint'=> null,

    /**
     * @var string 删除文件入口
     */
    'delPoint'=>null,

    /**
     * @var bool 是否自动开启上传
     */
    'auto' => true,

    /**
     * @var bool 分片上传大文件
     */
    'chunked' => false,

    /**
     * @var int 上传文件数量限制
     */
    'fileNumLimit' => 1,

    /**
     * @var int 单个文件大小限制
     */
    'fileSingleSizeLimit' => 100 * 1024 * 1024,

    /**
     * @var int 分片的大小
     */
    'chunkSize' => 5 * 1024 * 1024,

    /**
     * @var array 额外的表单数据
     */
    'formData' => [],
    
    /**
     * @var array 初始化 webuploader options
     */
    'clientOptions' => []
```

## 扩展驱动

可以扩展oss，qiniu等等，本地扩展驱动试例如下

```
/**
 * Created by PhpStorm.
 * User: dungang
 * Date: 2017/3/2
 * Time: 11:24
 */

namespace dungang\webuploader\components;


use yii\helpers\BaseFileHelper;

class LocalUploader extends Uploader
{
    /**
     * @return bool|string
     */
    public function writeFile()
    {
        $fileName = md5($this->guid . $this->id);

        $file = $fileName . '.' . $this->file->extension;

        $dir = $this->saveDir .DIRECTORY_SEPARATOR. date('Y-m-d');

        $path = \Yii::getAlias('@webroot') . $dir;

        $position = 0;

        if (BaseFileHelper::createDirectory($path))
        {
            $targetFile = $path . DIRECTORY_SEPARATOR . $file;
            if($this->chunked) {
                if ($this->chunked === 0 ) {
                    $position = 0;
                    if (file_exists($targetFile)) {
                        @unlink($targetFile);
                    }
                } else {
                    $position = $this->chunkSize * $this->chunk;
                }
            }
            if($out = @fopen($targetFile,'a+b')){
                fseek($out,$position);
                if ( flock($out, LOCK_EX) ) {
                    if ($in = fopen($this->file->tempName, 'rb')) {
                        while ($buff = fread($in, 4096)) {
                            fwrite($out, $buff);
                        }
                        @fclose($in);
                        @unlink($this->file->tempName);
                    }
                    flock($out, LOCK_UN);
                }
                @fclose($out);
                return $dir . DIRECTORY_SEPARATOR . $file;
            }

        }
        return false;
    }

    public function deleteFile($file)
    {
        $file = ltrim($file,'/\\');
        $dir = ltrim($this->saveDir,'/\\');
        $prefix = substr($file,0,strlen($dir));
        if (strcasecmp($prefix,$dir)==0) {
            $path = \Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . $file;
            return @unlink($path);
        }
        return false;
    }
}
```

## 协议

MIT License

Copyright (c) 2017 dungang

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.