<?php

namespace Libs;

/**
*
*/
class Net
{
    /**
     * curl方式获取远程数据
     * @param  [type] $filename [description]
     * @param  [type] $referer  [description]
     * @return [type]           [description]
     */
	public static function curl($filename , $referer = '')
	{
	   $ch = curl_init();
	   curl_setopt($ch, CURLOPT_URL, $filename);
       if($referer == ''){
            $parseUrl = parse_url($filename);
            $referer = $parseUrl['host'];
       }
	   curl_setopt($ch, CURLOPT_REFERER, $referer); // 看这里，你也可以说你从google来
	   curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:2.0b13pre) Gecko/20110307 Firefox/4.0b13pre");
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   // curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);
	   curl_setopt($ch, CURLOPT_HEADER, false);//设定是否输出页面内容
	   // curl_setopt($ch, CURLOPT_GET, 1); // post,get 过去
	   $filecontent = curl_exec($ch);
	   curl_close($ch);
	   return $filecontent;
	}

    /**
     * 是否url连接
     * @param  [type]  $str [description]
     * @return boolean      [description]
     */
    public static function isUrl($str)
    {
        return preg_match("/^http[s]?:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\"])*$/", $str);
    }
}
