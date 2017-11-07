<?php

/**
 * xushuhui
 * 2017/10/25
 * 17:58
 */
class LoginContrller extends Yaf\Controller_Abstract
{
	//微信获取用户信息 前端获取令牌
	public function getTokenAction()
	{
		$code = request()->post('code');
		if(empty($code)){
			return json(-1000,'请输入code');
		}
		$token = (new UserToken($code))->get();
		return json(0,'success',['token'=>$token]);
	}
	// token验证
	public function verifyTokenAction()
	{
		$token = request()->post('token');
		if(!$token){
			return json(-1000,'token不允许为空');

		}
		$valid = UserToken::verifyToken($token);
		return json(0,'success',['isValid' => $valid]);
	}
}