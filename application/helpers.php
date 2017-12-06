<?php
/**
 * xushuhui
 * 2017/10/18
 * 16:37
 */
use think\Cache;
use think\Config;
use think\Db;
use think\Response;
use think\Log;
use think\Debug;
use think\exception\HttpException;
use think\exception\HttpResponseException;
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
		return Response::create($data, 'jsonp', $code, $header, $options)->send();
	}
}
if (!function_exists('exception')) {
	/**
	 * 抛出异常处理
	 *
	 * @param string    $msg  异常消息
	 * @param integer   $code 异常代码 默认为0
	 * @param string    $exception 异常类
	 *
	 * @throws Exception
	 */
	function exception($msg, $code = 0, $exception = '')
	{
		$e = $exception ?: '\think\Exception';
		throw new $e($msg, $code);
	}
}
if (!function_exists('cache')) {
	/**
	 * 缓存管理
	 * @param mixed     $name 缓存名称，如果为数组表示进行缓存设置
	 * @param mixed     $value 缓存值
	 * @param mixed     $options 缓存参数
	 * @param string    $tag 缓存标签
	 * @return mixed
	 */
	function cache($name, $value = '', $options = null, $tag = null)
	{
		if (is_array($options)) {
			// 缓存操作的同时初始化
			$cache = Cache::connect($options);
		} elseif (is_array($name)) {
			// 缓存初始化
			return Cache::connect($name);
		} else {
			$cache = Cache::init();
		}

		if (is_null($name)) {
			return $cache->clear($value);
		} elseif ('' === $value) {
			// 获取缓存
			return 0 === strpos($name, '?') ? $cache->has(substr($name, 1)) : $cache->get($name);
		} elseif (is_null($value)) {
			// 删除缓存
			return $cache->rm($name);
		} elseif (0 === strpos($name, '?') && '' !== $value) {
			$expire = is_numeric($options) ? $options : null;
			return $cache->remember(substr($name, 1), $value, $expire);
		} else {
			// 缓存数据
			if (is_array($options)) {
				$expire = isset($options['expire']) ? $options['expire'] : null; //修复查询缓存无法设置过期时间
			} else {
				$expire = is_numeric($options) ? $options : null; //默认快捷缓存设置过期时间
			}
			if (is_null($tag)) {
				return $cache->set($name, $value, $expire);
			} else {
				return $cache->tag($tag)->set($name, $value, $expire);
			}
		}
	}
}
if (!function_exists('trace')) {
	/**
	 * 记录日志信息
	 * @param mixed     $log log信息 支持字符串和数组
	 * @param string    $level 日志级别
	 * @return void|array
	 */
	function trace($log = '[think]', $level = 'log')
	{
		if ('[think]' === $log) {
			return Log::getLog();
		} else {
			return Log::record($log, $level);
		}
	}
}
if (!function_exists('dump')) {
	/**
	 * 浏览器友好的变量输出
	 * @param mixed     $var 变量
	 * @return void|string
	 */
	function dump($var)
	{
		return Debug::dump($var);
	}
}
if (!function_exists('abort')) {
	/**
	 * 抛出HTTP异常
	 * @param integer|Response      $code 状态码 或者 Response对象实例
	 * @param string                $message 错误信息
	 * @param array                 $header 参数
	 */
	function abort($code, $message = null, $header = [])
	{
		if ($code instanceof Response) {
			throw new HttpResponseException($code);
		} else {
			throw new HttpException($code, $message, null, $header);
		}
	}
}

if (!function_exists('halt')) {
	/**
	 * 调试变量并且中断输出
	 * @param mixed      $var 调试变量或者信息
	 */
	function halt($var)
	{
		dump($var);
		throw new HttpResponseException(new Response);
	}
}
if (!function_exists('collection')) {
	/**
	 * 数组转换为数据集对象
	 * @param array $resultSet 数据集数组
	 * @return \think\model\Collection|\think\Collection
	 */
	function collection($resultSet)
	{
		$item = current($resultSet);
		if ($item instanceof \think\Think) {
			return \think\model\Collection::make($resultSet);
		} else {
			return \think\Collection::make($resultSet);
		}
	}
}
if (!function_exists('request')) {
	function request()
	{
		return \think\Request::instance();
	}
}
