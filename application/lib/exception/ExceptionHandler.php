<?php

namespace lib\exception;

use think\Exception;
use think\exception\Handle;
use think\Log;
use think\Request;

/**
 * 异常处理类
 * Class ExceptionHandler
 * @package app\lib\exception
 * @author xushuhui
 * @version 1.0
 */
class ExceptionHandler extends Handle
{
	//HTTP 状态码
	private $code = 400;
	//错误信息
	private $msg = '参数错误';
	//自定义错误码
	private $errorCode = 10000;
	private $data = '';
	public function render(\Exception $e)
	{
		if ($e instanceof BaseException) {
			//如果是自定义的异常
			$this->code = $e->code;
			$this->msg = $e->msg;
			$this->errorCode = $e->errorCode;
			$this->data = $e->data;
		} else {
			//开发环境抛出所有错误信息
			if(APP_DEBUG){
				return parent::render($e);
			}else {//生产环境只记录错误信息
				$this->code = 500;
				$this->msg = '服务器内部错误';
				$this->errorCode = 999;
				$this->recordErrorLog($e);
			}
		}
		$result = [
			'msg' => $this->msg,
			'errorCode' => $this->errorCode,
			'requestUrl' => Request::instance()->url(),
			'data' => $this->data
		];
		return json($result);
	}

	//记录错误日志
	private function recordErrorLog(\Exception $e){
		Log::init([
			'type'=>'file',
			'path'=>Log_PATH,
			'level'=>['error']
		]);
		Log::record($e->getMessage(),'error');
	}

}