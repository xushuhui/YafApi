<?php

//参数异常
namespace lib\exception;
/**
 * 参数错误抛出类
 * Class ParameterException
 * @package app\lib\exception
 * @author xushuhui
 * @version 1.0
 */
class ParameterException extends BaseException
{
	//HTTP 状态码
	public $code = 400;
	//错误信息
	public $msg = '参数错误';
	//自定义错误码
	public $errorCode = 10000;


}