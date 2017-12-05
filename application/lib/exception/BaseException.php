<?php


namespace lib\exception;

use think\Exception;

/**
 * 错误异常抛出基类
 * Class BaseException
 * @package app\lib\exception
 * @author xushuhui
 * @version 1.0
 */
class BaseException extends Exception
{
	//HTTP 状态码
	public $code = 400;
	//错误信息
	public $msg = '参数错误';
	//自定义错误码
	public $errorCode = 10000;
	public $data = '';
	public function __construct($params = [])
	{
		if(!is_array($params)){
			throw new Exception('参数必须是数组');
		}
		if(array_key_exists('code',$params)){
			$this->code = $params['code'];
		}
		if(array_key_exists('msg',$params)){
			$this->msg = $params['msg'];
		}
		if(array_key_exists('data',$params)){
			$this->data = $params['data'];
		}
		if(array_key_exists('errorCode',$params)){
			$this->errorCode = $params['errorCode'];
		}
	}

}