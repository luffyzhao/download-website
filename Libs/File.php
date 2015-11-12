<?php

namespace Libs;
use \Libs\Net;

/**
*
*/
class File
{
    /**
     * 写入文件
     * @param  [type] $filename [description]
     * @param  [type] $content  [description]
     * @return [type]           [description]
     */
    public static function put($filename , $content)
    {
        if(file_exists($filename)){
            return true;
        }
        $fp = fopen($filename , "a");
        if($fp === false){
            throw new Exception("[{$filename}]文件打开失败！");
        }
        $fw = fwrite($fp, $content);
        if($fw === false){
            throw new Exception("[{$filename}]文件写入失败！");
        }
        fclose($fp);
    }

    /**
     * 获取文件内容
     * @param  [type] $filename [description]
     * @return [type]           [description]
     */
    public static function get($filename){

        if(Net::isUrl($filename)){
           return Net::curl($filename);
        }

        $fp = fopen($filename , 'r');
        if($fp === false){
            throw new Exception("[{$filename}]文件打开失败！");
        }
        $fr = fread($fp , filesize($filename));
        if($fr === false)
        {
            throw new Exception("[{$filename}]文件读取失败！");
        }
        fclose($fp);
        return $fr;
    }

    /**
     * 下载远程文件到本地
     * @return [type] [description]
     */
    public static function putForHttp($url , $filename)
    {
        self::put($filename , self::get($url));
    }
}
