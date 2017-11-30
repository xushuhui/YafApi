<?php
/**
 * xushuhui
 * 2017/10/18
 * 16:37
 */
use think\Cache;
use think\Config;
use think\Cookie;
use think\Db;
use think\Response;
if (!function_exists('config')) {
	/**
	 * 获取和设置配置参数
	 * @param string|array  $name 参数名
	 * @param mixed         $value 参数值
	 * @param string        $range 作用域
	 * @return mixed
	 */
	function config($name = '', $value = null)
	{
		if (is_null($value) && is_string($name)) {
			return 0 === strpos($name, '?') ? Config::has(substr($name, 1)) : Config::get($name);
		}
	}
}
if (!function_exists('json')) {
	/**
	 * 获取\think\response\Json对象实例
	 * @param mixed   $data 返回的数据
	 * @param integer $code 状态码
	 * @param array   $header 头部
	 * @param array   $options 参数
	 * @return \think\response\Json
	 */
	function json($data = [], $code = 200, $header = [], $options = [])
	{
		return Response::create($data, 'json', $code, $header, $options)->send($data);
	}
}
if (!function_exists('xml')) {
	/**
	 * 获取\think\response\Xml对象实例
	 * @param mixed   $data    返回的数据
	 * @param integer $code    状态码
	 * @param array   $header  头部
	 * @param array   $options 参数
	 * @return \think\response\Xml
	 */
	function xml($data = [], $code = 200, $header = [], $options = [])
	{
		return Response::create($data, 'xml', $code, $header, $options)->send();
	}
}
if (!function_exists('jsonp')) {
	/**
	 * 获取\think\response\Jsonp对象实例
	 * @param mixed   $data    返回的数据
	 * @param integer $code    状态码
	 * @param array   $header 头部
	 * @param array   $options 参数
	 * @return \think\response\Jsonp
	 */
	function jsonp($data = [], $code = 200, $header = [], $options = [])
	{
		return Response::create($data, 'jsonp', $code, $header, $options);
	}
}
if (!function_exists('request')) {
	function request()
	{
		return \think\Request::instance();
	}
}
