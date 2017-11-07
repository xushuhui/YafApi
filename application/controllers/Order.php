<?php

/**
 * xushuhui
 * 2017/10/25
 * 17:44
 */
class OrderController extends BaseController
{
	public function place()
	{
		$player_id = $this->getRequest()->getParam("player_id");
		$gift_id = $this->getRequest()->getParam("gift_id");
		$data['order_no'] = getOrderNo();

	}
}