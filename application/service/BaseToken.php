<?php
/**
 * xushuhui
 * 2017/10/23
 * 17:20
 */

namespace service;
use think\Cache;
class BaseToken
{
	public function test()
	{
		return '12';
	}
	//生成令牌
	public static function generateToken()
	{
		$randChars = getRandCode(32);
		$timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
		$salt = config('secure')['token_salt'];
		return md5($randChars.$timestamp.$salt);
	}

	public static function getCurrentTokenVar($key)
	{
		$token = request()->header('token');
		$vars = Cache::get($token);
		if(!$vars){
			return json(-1000,'Token获取失败');
		}else{
			if(!is_array($vars))
			{
				$vars = json_decode($vars, true);
			}
			if(array_key_exists($key,$vars)){
				return $vars[$key];
			}else{
				return json(-1000,'尝试获取的token不存在');
			}
		}

	}
	//通过token获取uid
	public static function getCurrentUid()
	{
		$uid = self::getCurrentTokenVar('uid');
		return $uid;
	}


	//是否合法操作
	public static function isValidOperate($checkUID)
	{
		if(!$checkUID)
		{
			return json(-1000,'必须要有uid');

		}
		$currentOperateUID = self::getCurrentUid();
		if($checkUID == $currentOperateUID){
			return true;
		}
		return false;
	}
	//验证token
	public static function verifyToken($token)
	{
		$exist = Cache::get($token);
		if($exist){
			return true;
		}
		else{
			return false;
		}
	}
}