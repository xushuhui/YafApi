<?php
/**
 * xushuhui
 * 2017/10/18
 * 16:45
 */
use service\UserToken;
class BaseController extends Yaf\Controller_Abstract
{
    public function init()
	{
		//$this->response -> setHeader( 'Content-Type', 'application/json; charset=utf-8' );
	//	$this->uid = UserToken::getCurrentUid();
	//	if($this->uid==0){
	//		return json(-1000,'请输入用户token');
	//	}
	}
}
