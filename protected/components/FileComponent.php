<?php
/**
 * 文件上传组件
 * @author tivon
 * @date 2015-04-24
 */
use Qiniu\Auth;
use Qiniu\Processing;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;
class FileComponent extends CApplicationComponent
{
    /**
     * @var boolean 是否启用云存储，默认使用本地存储
     */
    public $enableCloudStorage = false;
    /**
     * @var string 云存储AK
     */
    public $accessKey = '6f3nbctnEWwbqQF42mOgpKk7KGVoP6f8mvSWxH2f';
    /**
     * @var string 云存储SK
     */
    public $secretKey = 'rMBFg1xsoQ6IkxwU2Pb4Iza8iWKXboRmRoDbTCjS';
    /**
     * @var string 云存储属主空间(bucket)名
     */
    public $bucket = 'ztspace';
    /**
     * @var int 授权凭证有效时间（单位：秒），默认300秒
     */
    public $authExpire = 300;
    /**
     * @var string 本地存储时，图片根目录访问地址。可以是目录路径如'/upload/'，也可以是域名如'http://qiniu.com/'。
     */
    public $_localHost = '/upload/';
    /**
     * @var string 云存储访问域名主机
     */
    private $_cloudHost = 'http://okwfe8mj2.bkt.clouddn.com';
    /**
     * @var string 本地存储路径根目录，支持Yii路径别名。需要将{@link endableCloudStorage}设置为false。
     */
    private $_root = 'webroot.upload';
    /**
     * @var array 相对于存储根目录的路径，采用本地存储时相对于{@link $root}，采用云存储时相对于根域名{@link $cloudHost}。
     * 每个元素作为一个文件夹名，如array('dir1','dir2','dir3')代表文件夹/root/dir1/dir2/dir3/或http://qiniu.com/dir1/dir2/dir3/
     */
    private $_path = array();
    /**
     * @var array 允许上传的文件类型
     */
    private $_fileType = array('jpg','png','gif','jpeg','bmp','JPG','PNG','JPEG','BMP','GIF');
    /**
     * @var boolean 是否多文件上传
     */
    private $_multi=false;

    /**
     * 获得随机文件名，用于要上传的文件
     * @return string 文件名
     */
    public function getRandFileName()
    {
        return str_replace('.', '', microtime(1)) . rand(100000,999999);
    }

    /**
     * 云抓取
     * 使用七牛的fetch服务，提供图片URL地址、bucket和文件路径key，七牛会抓取到图片并存储
     * @param  string $url    图片地址
     * @return boolean        上传成功返回图片相对地址，false上传失败
     */
    public function cloudFetch($url)
    {
        //生成存储的key
        $path = $this->getFilePath();
        $filename = $this->getRandFileName();
        if(($pos=strrpos($url,'.'))!==false)
            $ext = (string)substr($url,$pos+1);
        if(empty($ext)) return false;
        $key = $path . $filename .'.'. $ext;

        $auth = new Auth($this->accessKey, $this->secretKey);
        $bucketMgr = new BucketManager($auth);
        $r = $bucketMgr->fetch($url, $this->bucket, $key);
        // var_dump($r);exit;
        if(!empty($r[0]['hash'])&&!empty($r[0]['key']))
            return $key;
        else
            return false;
    }

    /**
     * 抓取到本地服务器，注意与{@see cloudFetch()}方法的区别，一个是抓取到云，一个是抓取到本地
     * @param  string $url 图片地址
     * @return boolean        上传成功返回图片相对地址，false上传失败
     */
    public function localFetch($url)
    {
        $header = get_headers($url);
        if(strpos($header[0],'200')===false)
        {
            return false;
        }
        if(strpos($url,'hualongxiang')!==false)
        {
            return $url;
        }
        $ext = substr($url, strrpos($url,'.')+1);
        // var_dump($ext);die;
        $root = $this->root;
        //创建目录
        $path = $this->getFilePath();
        //保存文件
        rename:
        $filename = $this->getRandFileName();
        if(file_exists($root.$path.$filename.'.'.$ext))
        {
            goto rename;
        }
        else
        {
            ob_start();
            readfile($url);
            $img = ob_get_contents();
            ob_end_clean();
            // $size = strlen($img); //文件大小
            // echo $root.$path.$filename.'.'.$ext;die;
            $fp2=@fopen($root.$path.$filename.'.'.$ext, "a");
            $r = fwrite($fp2,$img);
            fclose($fp2);
            if($r===false)
                return false;
            else
                return $path.$filename.'.'.$ext;
        }
        return false;
    }

    /**
     * 抓取到本地或云服务器
     * @param  string $url 图片地址
     * @return boolean     上传成功返回图片相对地址，false上传失败
     */
    public function fetch($url)
    {
        if($this->enableCloudStorage)
            return $this->cloudFetch($url);
        else
            return $this->localFetch($url);
    }

    /**
     * 上传单个文件
     * @param CUploadedFile $file 一个CUploadedFile实例，包含上传的文件的信息
     * @return string|null   上传文件失败时返回null，上传单个文件成功时返回文件相对路径string
     */
    public function fileUpload($file)
    {
        $root = $this->root;
        //创建目录
        $path = $this->getFilePath();

        $filename = $this->getRandFileName();
        $ext = $file->extensionName;
        if(!in_array($ext, $this->fileType)) return false;

        if($file->saveAs($root.$path.$filename.'.'.$ext))
            return $path.$filename.'.'.$ext;

        return null;

    }

    /**
     * 上传多个文件
     * @param CUploadedFile[] $file 一个数组，包含CUploadedFile实例
     * @return array 每个数组元素对应一个文件路径，如array('a/b/filename1.jpg','a/b/filename2.jpg')，若所有文件都没上传成功则返回一个空数组
     */
    public function filesUpload($files)
    {
        $data = array();
        foreach($files as $file)
        {
            if(!in_array($file->extensionName, $this->fileType)) continue;
            $root = $this->root;
            //创建目录
            $path = $this->getFilePath();
            $filename = $this->getRandFileName();
            $ext = $file->extensionName;

            if($file->saveAs($root.$path.$filename.'.'.$ext))
               $data[] = $path.$filename.'.'.$ext;
        }
        return empty($data) ? array() : $data;
    }

    public function createQiniuKey($file=null)
    {
        $path = $this->getFilePath();
        $filename = $this->getRandFileName();
        $ext = $file ? ('.'. $file->extensionName) : '$(ext)';
        $key = $path . $filename . $ext;
        return $key;
    }

    /**
     * 云存储单个文件
     * @param CUploadedFile[] $file 一个数组，包含CUploadedFile实例
     * @return string|null   上传文件失败时返回null，上传单个文件成功时返回文件相对路径string
     */
    public function QiniuFileUpload($file)
    {
        //云存储的key
        $path = $this->getFilePath();
        $filename = $this->getRandFileName();
        $ext = $file->extensionName;
        $key = $path . $filename .'.'. $ext;

        $auth = new Auth($this->accessKey, $this->secretKey);
        $token = $auth->uploadToken($this->bucket,null,$this->authExpire);
        $uploadMgr = New UploadManager();
        list($ret, $err) = $uploadMgr->putFile($token, $key, $file->tempName);
        
        if( empty($err) )
            return $key;
        else
            return null;
    }

    /**
     * 云存储多个文件
     * @param CUploadedFile[] $file 一个数组，包含CUploadedFile实例
     * @return array 每个数组元素对应一个文件路径，如array('a/b/filename1.jpg','a/b/filename2.jpg')，若所有文件都没上传成功则返回一个空数组
     */
    public function QiniuFilesUpload($files)
    {
        $data = array();
        foreach($files as $file)
        {
            if($key = $this->QiniuFileUpload($file))
                $data[] = $key;
        }
    }

    /**
     * 上传文件
     * @param  $name 文件表单的name值。当上传单个文件时，表单如<input type='file' name='file'>，此时$name为'file'；当上传多个文件时，表单如<input type='file' name='files[]'>，此时$name为'files'，而不是'files[]'
     * @return mixed 上传多个文件时，返回array，每个数组元素对应一个文件路径，如array('a/b/filename1.jpg','a/b/filename2.jpg')，若所有文件都没上传成功则返回一个空数组；上传单个文件时，上传文件失败时返回null，上传单个文件成功时返回文件相对路径string
     */
    public function upload($name)
    {

        if(is_array($_FILES[$name]['name']))
            $this->setMulti(true);

        $file = $this->multi ? CUploadedFile::getInstancesByName($name) : CUploadedFile::getInstanceByName($name);
        if($this->multi)
            return $this->enableCloudStorage ? $this->QiniuFilesUpload($file) : $this->filesUpload($file);
        else
            return $this->enableCloudStorage ? $this->QiniuFileUpload($file) : $this->fileUpload($file);

    }

    /**
     * 七牛图片获取图片信息
     * @param  string $key 七牛存储的图片key
     * @return 见文档http://developer.qiniu.com/code/v6/api/kodo-api/image/imageinfo.html
     */
    public function getInfo($key)
    {
        $format = $colorModel ='';
        $width = $height = 0;
        $url = $this->host . $key . '?imageInfo';
        $response = HttpHelper::get($url);
        if($response['code']==200){
            $imgInfo = $response['content'];
            if(is_string($imgInfo) && CJSON::decode($imgInfo)){
                list($format, $width, $height, $colorModel) = array_values(CJSON::decode($imgInfo));
            }
        }
        return array(
            'format' => $format,
            'colorModel' => $colorModel,
            'width' => $width,
            'height' => $height,
        );

    }

    /**
     * 给指定key的图片增加永久水印，该函数由指定场景调用，本类中不会自动调用的
     * @param  string $key 图片url，不带http协议，如pic.hualongxiang.com/2016/01/02/214153535.jpg
     *                     需要为不带任何七牛接口修饰的图片地址，即不要加imageView什么的
     * @return string  返回saveas的图片地址供显示，使得打水印的图片存储起来。失败则返回空字符串
     */
    public function waterMark($key)
    {
        $url = $this->getHost() . $key;
        if(SM::waterMarkConfig()->enable()&&SM::waterMarkConfig()->waterMarkPic())
        {
            $baseUrl = Qiniu\base64_urlSafeEncode($this->getHost().SM::waterMarkConfig()->waterMarkPic());
            if(SM::waterMarkConfig()->position())
                $gravity = SM::waterMarkConfig()->position();
            else
                $gravity = 'SouthEast';
            $waterMark = 'watermark/1/image/'.$baseUrl.'/gravity/'.$gravity;

            $auth = new Auth($this->accessKey, $this->secretKey);
            $url = str_replace(array('http://','https://'),array(),$url);
            $encodeEntryURI = Qiniu\base64_urlSafeEncode($this->bucket.':'.$key);//EncodedEntryURI
            $newUrl = $url . '?'.$waterMark.'|saveas/' . $encodeEntryURI;
            $sign = $auth->sign($newUrl);
            $finalUrl = 'http://' . $newUrl . '/sign/' . $sign;
            $r = Qiniu\Http\Client::get($finalUrl);
            if($json = CJSON::decode($r->body)){
                if(isset($json['key'])) $key = $json['key'];
            }
        }
        return $key;
    }

    /**
     * 自动创建目录
     * @return string 相对于目录{@link root}的相对路径
     */
    public function getFilePath()
    {
        $path = '';
        if(!$this->enableCloudStorage && !empty($this->path) && is_array($this->path))
        {
            foreach($this->path as $dir)
            {
                $path .= $dir . DIRECTORY_SEPARATOR;
                if( !is_dir($this->root . $path) )
                    @mkdir($this->root . $path);
            }
        }
        else
        {
            $path .= implode('/', $this->path) .'/';
        }
        return $path;
    }

    /**
     * 设置是否多图模式
     * @param boolean $value
     */
    public function setMulti($value)
    {
        $this->_multi = (bool)$value;
    }

    /**
     * 是否多图
     * @return bollean 如果多图返回true，否则返回false
     */
    public function getMulti()
    {
        return $this->_multi;
    }

    /**
     * 设置本地存储相对于{@link root}的路径。
     */
    public function setPath($value)
    {
        if(is_array($value))
            $this->_path = $value;
    }

    /**
     * 获得本地存储相对于{@link root}的路径。
     * @return array
     */
    public function getPath()
    {
        if($this->_path===array())
            return array(date('Y'),date('md'));
        else
            return $this->_path;
    }

    /**
     * 获得本地存储路径根目录，支持Yii路径别名。需要将{@link endableCloudStorage}设置为false。
     * @return string 路径
     */
    public function getRoot()
    {
        if(!$root = Yii::getPathOfAlias($this->_root))
            $root = $this->_root;
        return rtrim($root,'\\/').DIRECTORY_SEPARATOR;
    }

    /**
     * 设置本地存储路径根目录，支持Yii路径别名。需要将{@link endableCloudStorage}设置为false。
     */
    public function setRoot($value)
    {
        $this->_root = $value;
    }

    /**
     * 设置云存储访问域名主机
     */
    public function setCloudHost($value)
    {
        $this->_cloudHost = $value;
    }

    /**
     * 设置本地存储访问主机
     */
    private function setLocalHost($value)
    {
        $this->_localHost = $value;
    }

    /**
     * 设置存储访问主机
     */
    public function setHost($value)
    {


        $value = rtrim($value,'\\/') . ($this->enableCloudStorage ? '/' : DIRECTORY_SEPARATOR);
        if($this->enableCloudStorage)
        {
            if(strpos($value,'http')===false)
                $value = 'http://'.$value;
            $this->setCloudHost($value);
        }
        else
            $this->setLocalHost($value);
    }

    /**
     * @return string 获得云存储访问域名主机
     */
    public function getCloudHost()
    {
        return $this->_cloudHost;
    }

    /**
     * @return string 获得本地存储访问主机
     */
    public function getLocalHost()
    {
        return $this->_localHost;
    }

    /**
     * @return string 获得图片访问地址前缀
     */
    public function getHost()
    {
        if($this->enableCloudStorage)
            return $this->cloudHost;
        else
            return $this->localHost;
    }

    /**
     * 设置允许上传文件类型
     * @param array $value 允许的文件类型，以逗号分割。array('jpg','png'[,...[,...]])
     */
    public function setFileType($value)
    {
        $this->_fileType = is_array($value) ? $value : array();
    }

    /**
     * @return array 获得允许上传的文件类型
     */
    public function getFileType()
    {
        return $this->_fileType;
    }

    /**
     * 从命令行上传文件
     * @param string $filePath 本地文件路径
     * @param string $extPath  目标服务器文件路径
     * @return array | string  上传返回信息
     */
    public function consoleFileUpload($filePath = '', $extPath = '')
    {
        if (!$filePath || !$extPath) {
            return false;
        }
        $auth = new Auth($this->accessKey, $this->secretKey);
        $token = $auth->uploadToken($this->bucket,null,$this->authExpire);
        $uploadMgr = New UploadManager();
        list($ret, $err) = $uploadMgr->putFile($token, $extPath, $filePath);

        if ($err !== null) {
            return $err;
        } else {
            return $ret;
        }
    }
}
?>
