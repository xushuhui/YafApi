<?php
/**
 * xushuhui
 * 2017/10/23
 * 17:07
 */
namespace service;
use think\Exception;
use think\Cache;
class UserToken extends BaseToken
{
	protected $code;
	protected $wxLoginUrl;
	protected $wxconfig;

	function __construct($code)
	{
		$this->code = $code;
		$this->wxconfig = config('wx');
		$this->wxLoginUrl = sprintf($this->wxconfig['login_url'],$this->wxconfig['app_id'],$this->wxconfig['app_secret'],$this->code);

	}
	public function get()
	{
		$wxResult = json_decode(curl_get($this->wxLoginUrl),true);
		if(empty($wxResult)){
			throw new Exception('获取openid失败');
		}
		$loginFail = array_key_exists('errcode',$wxResult);
		if($loginFail){
			$this->processLoginError($wxResult);
		}else {
			$userInfoUrl = sprintf($this->wxconfig['userInfo_url'],$wxResult['access_token'],$wxResult['openid']);
			$result = json_decode(curl_get($userInfoUrl),true);//获取用户信息
			return $this->grantToken($result);
		}
	}
	private function grantToken($wxResult)
	{
		$user = \WxuserModel::getByOpenID($wxResult['openid']);
		if($user){
			$uid = $user->uid;
		}else {
			$uid = $this->newUser($wxResult);
		}
		$cachedValue = $this->prepareCachedValue($wxResult,$uid);
		$token = $this->saveToCache($cachedValue);
		return $token;
	}
	//保存缓存
	private function saveToCache($cachedValue)
	{
		$key = self::generateToken();
		$value = json_encode($cachedValue);
		$expire_in = config('setting')['token_expire_in'];
		$request = Cache::set($key,$value,86400);
	
		if(!$request){
			return json(-1000,'服务器缓存异常');
		}
		return $key;
	}
	//生成缓存值
	private function prepareCachedValue($wxResult,$uid)
	{
		$cachedValue = $wxResult;
		$cachedValue['uid'] = $uid;
		return $cachedValue;
	}
	//添加用户
	private function newUser($wxResult)
	{
		$user = \UserModel::create(['nickname' => $wxResult['nickname']]);
		$wxResult['uid'] = $user->id;
		$wxuser = \WxuserModel::create($wxResult,true);
		return $user->id;
	}

	private function processLoginError($wxResult)
	{
		return json(-1000,$wxResult['errmsg']);

	}


}