<?php
namespace App;

class Curl
{
    public static function PostCurl($url,$params,$headers='')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
        curl_setopt($curl, CURLOPT_POST,true); //设置POST请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        if($headers) curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); //添加自定义httpheader
        if(!$output = curl_exec($curl)) die('curl not result');
        curl_close($curl);
        return $output;
    }

    public static function GetCurl($url, $params = [])
    {
        $curl = curl_init();
        if ($params) {
            $char = strpos($url, '?') === false ? '?' : '&';
            $url = $url . $char . http_build_query($params);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_NOBODY, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
        curl_setopt($curl, CURLOPT_POST,false); //设置POST请求
        if(!$output = curl_exec($curl)) die('curl not result');
        curl_close($curl);
        return $output;
    }
}