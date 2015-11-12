<?php
namespace Libs;
use Libs\Net;
use Libs\Log;
use Libs\File;
/**
*
*/
class Spider
{
    private $html = '';
    private $host = '';
    private $base = '';
    private $url = '';
    private $selector   =   array(
            'link'=>'href',
            'script'=>'src',
            'img'=>'src',
            'a' => 'href'
        );
    private $dirs = array(
            'link'=>'style',
            'script'=>'js',
            'img'=>'images',
        );
    /**
     * [__construct description]
     * @param [type] $url [description]
     */
    public function __construct($url)
    {
        if(!Net::isUrl($url)){
            throw new Exception("什么臭鸡玩意？");
        }
        $this->url = $url;
        $this->html = Web::file_get_html($this->url);
        $this->host();
        $this->base();
    }

    /**
     * 开始干鸡巴
     * @return [type] [description]
     */
    public function init()
    {
        foreach ($this->selector as $key => $value) {
            foreach($this->html->find($key) AS $e){
                //a标签清除
                if($key == 'a'){
                    $e->$value = '';
                    continue;
                }
                $param = $this->subquery($e->$value);

                $method = "{$key}Method";
                Log::view("开始处理：[{$e->$value}]");
                $dir = $this->dirs[$key];
                if(method_exists($this , $method)){
                    $newlink = $this->$method($param , $dir);
                }else{
                    $newlink = $this->defaultMethod($param , $dir);
                }
                $e->$value = $newlink;
                Log::view("处理完毕：[{$e->$value}]");
            }
        }
        $filename = $this->dir().$this->filename($this->url);

        File::put($filename,$this->html);
        Log::view("处理完毕");
    }


    public function linkMethod($url , $dir)
    {
        $url = $this->url($url);
        $filename = basename($url);
        $path = $this->dir($dir);

        $linkContent = File::get($url);

        if($this->getSuffix($url) == 'css'){
            $patterns = '/url\([\"|\']?((.+)[jpg|gif|png|eot|woff2|woff|ttf|svg][\?]?[^\)]{1,30})[\"|\']?\)/U'; //正则根据不同地址需要变换
            preg_match_all($patterns, $linkContent, $matches);
            $imagesUrls = $matches[1];
            $urlBase  = dirname($url);
            $urlBase = $this->paddedSuffix($urlBase);
            foreach($imagesUrls as $image){
                $imageUrl = $this->url($image , $urlBase);
                Log::view("开始处理：[{$image}]");
                $imageUrl = $this->subquery($imageUrl);
                $newimage = $this->defaultMethod($imageUrl , 'images');
                $linkContent = str_replace($image , $newimage ,$linkContent);
                Log::view("处理完毕：[{$imageUrl}]");
            }

        }
        File::put($path.$filename , $linkContent);
    }

    /**
     * [defaultMethod description]
     * @param  [type] $url [description]
     * @param  [type] $dir [description]
     * @return [type]      [description]
     */
    public function defaultMethod($url , $dir)
    {
        $url = $this->url($url);
        $filename = basename($url);
        $path = $this->dir($dir);
        File::putForHttp($url , $path.$filename);
        return $dir.'/'.$filename;
    }
    /**
     * [base description]
     * @return [type] [description]
     */
    private function base()
    {
        $variable = $this->html->find('base' ,0);
        //设置了base标签
        if(!empty($variable)){
            foreach ($variable as $e) {
                $href = $e->href;
                if($href){
                    $this->base = $this->paddedSuffix($href);
                    return true;
                }
            }
        }
        //没有base标签
        $this->base = $this->filepath($this->url);
        return true;
    }


    /**
     * [host description]
     * @return [type] [description]
     */
    private function host()
    {
        $parseUrl = parse_url($this->url);
        $this->host = $parseUrl['host'];
    }

    /**
     * [paddedSuffix description]
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    private function paddedSuffix($url){
        if(substr($url, -1 , 1) != "/"){
            return $url.'/';
        }else{
            return $url;
        }
    }
    /**
     * [getSuffix description]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    private function getSuffix($file)
    {
        $fileSuffixArray   = explode(".",$file);
        $fileSuffix = end($fileSuffixArray);
        return strtolower($fileSuffix);
    }
    /**
     * [filename description]
     * @return [type] [description]
     */
    private function filename($url){
        $urlArray = explode('/', parse_url($url , PHP_URL_PATH  ));
        $filename = end($urlArray);
        if(strrpos($filename , '.') === false){
            return 'index.html';
        }
        return $filename;
    }

    /**
     * [FunctionName description]
     * @param string $value [description]
     */
    public function subquery($url)
    {
        $parseUrl = parse_url($url);
        $url = '';
        if(isset($parseUrl['scheme'])){
            $url .= $parseUrl['scheme'].'://';
        }
        if(isset($parseUrl['host'])){
            $url .= $parseUrl['host'];
        }
        if(isset($parseUrl['path'])){
            $url .= $parseUrl['path'];
        }
        return $url;
    }

    /**
     * [filepath description]
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    private function filepath($url)
    {
        $parseUrl = parse_url($url );
        $urlArray = explode('/', $parseUrl['path']);
        $filename = end($urlArray);
        if(strrpos($filename , '.') === false){
            $filepath = "{$parseUrl['scheme']}://{$parseUrl['host']}{$parseUrl['path']}";
        }else{
            $filepath = "{$parseUrl['scheme']}://{$parseUrl['host']}".dirname($parseUrl['path']);
        }

        return $this->paddedSuffix($filepath);
    }

    /**
     * [FunctionName description]
     * @param string $value [description]
     */
    private function url($url , $base = '')
    {
        if(Net::isUrl($url)){
            return $url;
        }else{
            $base = ($base == '') ? $this->base : $base;
            $base = $this->filepath($base);
            if(substr($url, 0 , 1) == "/"){
                $parseUrl = parse_url($this->url);
                return "{$parseUrl['scheme']}://{$parseUrl['host']}/".$url;
            }elseif(substr($url, 0 , 3) == "../"){
                return $this->filepath(dirname($base)) . substr($url , 3);
            }elseif(substr($url, 0 , 2) == "./"){
                return $base.substr($url , 2);
            }else{
                return  $base . $url;
            }
        }
    }

    /**
     * [dir description]
     * @return [type] [description]
     */
    private function dir($dir = ''){
        $path = ROOT_PAHT . 'temp/' . $this->host . '/' . $dir;
        $path = $this->paddedSuffix($path);
        if(!is_dir($path)){
            mkdir($path , 755 , true);
        }
        return $path;
    }
}
