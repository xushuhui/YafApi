<?php
/**
 * xushuhui
 * 2017/10/21
 * 11:35
 */
class OrderModel extends BaseModel
{

	public function wxuser()
	{
		return $this->hasOne("WxuserModel","uid","id");
	}
}