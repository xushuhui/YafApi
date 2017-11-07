<?php
/**
 * xushuhui
 * 2017/10/21
 * 12:04
 */
class WxuserModel extends BaseModel
{
	protected $visible = ['nickname','headimgurl','uid'];

	public static function getByOpenID($openid)
	{
		$wxuser = self::where('openid','=',$openid)->find();
		return $wxuser;
	}
}