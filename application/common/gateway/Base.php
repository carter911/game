<?php
namespace app\common\gateway;

use think\Log;

class Base  {

    public static function curlOpt($url, &$rdata, $options=array()) {  // 自定义选项执行CURL
        Log::info('远程请求地址'.$url.var_export($rdata,true).var_export($options,true));

        if (!isset($options[CURLOPT_RETURNTRANSFER])) {
            $options[CURLOPT_RETURNTRANSFER] = 1;
        }
        if (!isset($options[CURLOPT_HEADER])) {
            $options[CURLOPT_HEADER] = 0;
        }
        if (!isset($options[CURLOPT_TIMEOUT])) {
            $options[CURLOPT_TIMEOUT] = 30;
        }
        if (!isset($options[CURLOPT_FOLLOWLOCATION]) || $options[CURLOPT_FOLLOWLOCATION]) {
            $options[CURLOPT_FOLLOWLOCATION] = true;
            $options[CURLOPT_MAXREDIRS] = 3;
        }
        if (substr($url,0,5)=='https' && !isset($options[CURLOPT_SSL_VERIFYPEER])) {
            $options[CURLOPT_SSL_VERIFYPEER] = false;
        }
        $options[ CURLOPT_HTTPHEADER ] = self::headerCover ( $options[ CURLOPT_HTTPHEADER ] );
        $ch                            = curl_init($url);
        curl_setopt_array($ch, $options);
        $rdata = curl_exec($ch);
        Log::info('远程请求返回'.$url.var_export($rdata,true).var_export($options,true));
        $r = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $r;
    }

    /**
     * 实现header覆盖设置
     * @param $header
     * @return array
     */
    private static function headerCover ( $header )
    {
        $header_cover = [];
        $trim_str     = ': ';
        if ( is_array ( $header ) ) {
            foreach ( $header as $k => $v ) {
                if ( is_numeric ( $k ) ) {
                    $header_cover[ trim ( strstr ( $v, ':', true ), $trim_str ) ] =
                        trim ( strstr ( $v, ':' ), $trim_str );
                }
                else {
                    $header_cover[ trim ( $k, $trim_str ) ] = trim ( strstr ( $v, ':' ), $trim_str );
                }
            }
            $header = [];
            foreach ( $header_cover as $k => $v ) {
                $header[] = "{$k}: $v";
            }
        }
        return $header;
    }

    ///////////////////////////////////////////////
    public static function curlJson($url, $pdata, &$rdata, $header=array(), $method='POST') {  // 执行CURL / JSON
        $options = array();
        $options[CURLOPT_CUSTOMREQUEST] = $method;
        if (!empty($pdata)) {
            $options[CURLOPT_POSTFIELDS] = json_encode($pdata);
        }
        $options[CURLOPT_USERAGENT] = 'gameCharge/v2.*/CURL';
        $options[CURLOPT_ENCODING] = 'gzip,deflate';
        $h = array('Accept: application/json',
            'Content-Type: application/json',
            'Accept-Language: en-US,en',
            'Pragma: no-cache',
            'Cache-Control: no-cache',
            'Expect:');
        $header = array_merge($h, $header);
        $options[CURLOPT_HTTPHEADER] = $header;
        $r = self::curlOpt($url, $rdata, $options);
        $rdata = (array)json_decode($rdata, true);
        return $r;
    }

    ///////////////////////////////////////////////
    public static function curlPost($url, $pdata, &$rdata, $header=array()) {  // 执行CURL / POST
        $options = array();
        if (!empty($pdata)) {
            $options[CURLOPT_CUSTOMREQUEST] = 'POST';
            $options[CURLOPT_POSTFIELDS] = $pdata;
        }
        $options[CURLOPT_USERAGENT] = 'gameCharge/v2.*/CURL';
        $options[CURLOPT_ENCODING] = 'gzip,deflate';
        $h = array('Accept: text/html,application/xhtml+xml,application/xml',
            'Accept-Language: en-US,en',
            'Pragma: no-cache',
            'Cache-Control: no-cache',
            'Expect:');
        $header = array_merge($h, $header);
        $options[CURLOPT_HTTPHEADER] = $header;
        $r = self::curlOpt($url, $rdata, $options);
        return $r;
    }

    ///////////////////////////////////////////////
    public static function curlHtml($url, &$rdata, $header=array()) {  // 执行CURL / 获取页面
        $options = array();
        $options[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36';
        $options[CURLOPT_ENCODING] = 'gzip,deflate';
        $h = array('Accept: text/html,application/xhtml+xml,application/xml',
            'Accept-Language: en-US,en',
            'Pragma: no-cache',
            'Cache-Control: no-cache',
            'Expect:');
        $header = array_merge($h, $header);
        $options[CURLOPT_HTTPHEADER] = $header;
        $r = self::curlOpt($url, $rdata, $options);
        return $r;
    }

    ///////////////////////////////////////////////
    public static function urlInDomainList($url, $domainList) {  // 检查url是否在域名列表内
        $url = parse_url($url);
        if (empty($url['host'])) {
            return false;
        }
        foreach ($domainList as $d) {
            if ($d==substr($url['host'],0-strlen($d))) {
                return true;
            }
        }
        return false;
    }

    /**
     * xml解析成数组
     * @param $xml
     * @param bool $isFile
     *
     * @return bool|mixed
     *
     */
    public static function xmlToArray($xml, $isFile=false){
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        if ($isFile) {
            if(!file_exists($xml)) return false;
            $xmlStr = file_get_contents($xml);
        } else {
            $xmlStr = $xml;
        }
        $xmlStr = str_replace(chr(194).chr(160), ' ', $xmlStr);  // 过滤非法空格
        $r = (array)json_decode(json_encode(simplexml_load_string($xmlStr, 'SimpleXMLElement', LIBXML_NOCDATA|LIBXML_ERR_FATAL)), true);

        if (!empty($xmlStr) && empty($r)) {  // temp log
            $fp = @fopen('/var/log/log4xml.txt', 'a+');
            @fwrite($fp, date('Y-m-d H:i:s')."\r\n".$xmlStr."\r\n\r\n");
            @fclose($fp);
        }

        return $r;
    }

}
