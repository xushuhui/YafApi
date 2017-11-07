<?php
/**
 * xushuhui
 * 2017/10/20
 * 16:54
 */
class PlayerModel extends BaseModel
{
	public function votes()
	{
		return $this->hasMany("VotesModel","player_id","id");
	}

	public function order()
	{
		return $this->hasMany("OrderModel","player_id","id")->page(1,10);
	}
	public function getImgurlAttr($value, $data)
	{
		$config = config('setting');
		return $config['apiUrl'].$config['imgUrl'].$value;
	}
}