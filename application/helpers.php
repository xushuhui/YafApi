<?php
/**
 * xushuhui
 * 2017/10/18
 * 16:37
 */

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
		if(is_null($name)){
			return \Yaf\Registry::get('config')->toArray();
		}
		if (is_null($value) && is_string($name)) {
			return  (new Yaf\Config\Ini(APPLICATION_PATH.'/conf/application.ini',$name))->toArray();
		}
	}
}
if (!function_exists('json')) {
	function json($errorCode = 0,$msg = '',$data = [], $code = 200, $header = [], $options = [])
	{
		$result = [
				'errorCode' => $errorCode,
				'msg'  => $msg,
				'data' => $data
		];
		exit(json_encode($result)) ;
	}
}
if (!function_exists('request')) {
	function request()
	{
		return \think\Request::instance();
	}
}
