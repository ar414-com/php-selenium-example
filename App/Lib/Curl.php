<?php
/**
 * Created by PhpStorm.
 * User: ar414.com@gmail.com
 * Date: 2019/2/12
 * Time: 14:07
 */

namespace App\Lib;

use EasySwoole\Curl\Field;
use EasySwoole\Curl\Request;
use EasySwoole\Curl\Response;

class Curl
{
    public static function getCountPageForPsApi(string $url,array $params = null,int $timeout = 10)
    {
        $params['opt'][CURLOPT_TIMEOUT] = $timeout;
        $ret = self::request('GET',$url,$params);
        if(!empty($ret->getError()) || $ret->getCurlInfo()['http_code'] != 200)
        {
            return false;
        }
        preg_match_all("/X-Total:(.*)\n/U",$ret->getHeaderLine(),$result);
        if(!empty($result[0]))
        {
            $totalNum = (int)$result[1][0];
            return ceil($totalNum/50);
        }
        else
        {
            return false;
        }
    }

    public static function get(string $url,array $params = null,int $timeout = 10,string $proxy = null)
    {
        $params['opt'][CURLOPT_TIMEOUT] = $timeout;
        $ret = self::request('GET',$url,$params,$proxy);
        var_dump($ret->getCurlInfo()['http_code']);
        if(!empty($ret->getError()) || $ret->getCurlInfo()['http_code'] != 200)
        {
            return false;
        }
        return $ret->getBody();
    }

    public static function post(string $url,array $params = null,int $timeout = 10)
    {
        $params['opt'][CURLOPT_TIMEOUT] = $timeout;
        $ret = self::request('POST',$url,$params);
        if(!empty($ret->getError()) || $ret->getCurlInfo()['http_code'] != 200)
        {
            return false;
        }
        return $ret->getBody();
    }

    public static function request(string $method, string $url, array $params = null,string $proxy = null): Response
    {
        $request = new Request( $url );

        switch( $method ){
            case 'GET' :
                if( $params && isset( $params['query'] ) )
                {
                    foreach( $params['query'] as $key => $value )
                    {
                        $request->addGet( new Field( $key, $value ) );
                    }
                }
                break;
            case 'POST' :
                if( $params && isset( $params['form_params'] ) )
                {
                    foreach( $params['form_params'] as $key => $value )
                    {
                        $request->addPost( new Field( $key, $value ) );
                    }
                }
                elseif($params && isset( $params['body'] ))
                {
                    if(!isset($params['header']['Content-Type']) ){
                        $params['header']['Content-Type'] = 'application/json; charset=utf-8';
                    }
                    $request->setUserOpt( [CURLOPT_POSTFIELDS => $params['body']] );
                }
                break;
            default:
                throw new \InvalidArgumentException( "method eroor" );
                break;
        }

        if( isset( $params['header'] ) && !empty( $params['header'] ) && is_array( $params['header'] ) )
        {
            foreach( $params['header'] as $key => $value )
            {
                $string   = "{$key}:$value";
                $header[] = $string;
            }

            $request->setUserOpt( [CURLOPT_HTTPHEADER => $header] );
        }

        if(!empty($proxy))
        {
            $proxyType = explode('://', $proxy)[0];
            $proxyIpPort = explode('://', $proxy)[1];
            $request->setUserOpt( [CURLOPT_PROXY => $proxyIpPort] );
            $request->setUserOpt( [CURLOPT_HTTPPROXYTUNNEL => false] );
            switch ($proxyType)
            {
                case 'http':
                    $request->setUserOpt( [CURLOPT_PROXYTYPE => CURLPROXY_HTTP] );
                    break;
                case 'https':
                    $request->setUserOpt( [CURLOPT_SSL_VERIFYHOST => 2] );
                    $request->setUserOpt( [CURLOPT_SSL_VERIFYPEER => false] );
                    break;
                case 'socks4':
                    $request->setUserOpt( [CURLOPT_PROXYTYPE => CURLPROXY_SOCKS4] );
                    break;
                case 'socks5':
                    $request->setUserOpt( [CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5] );
                    break;
            }

        }

        if( isset( $params['opt'] ) && !empty( $params['opt'] ) && is_array( $params['opt'] ) )
        {

            $request->setUserOpt($params['opt']);
        }

        return $request->exec();
    }

}