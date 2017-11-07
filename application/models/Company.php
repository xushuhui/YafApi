<?php
/**
 * xushuhui
 * 2017/10/20
 * 10:04
 */
class CompanyModel extends BaseModel
{
	public function activity()
	{
		return $this->hasOne("ActivityModel","company_id","id");
	}

	public function getLogoAttr($value, $data)
	{
		$config = config('setting');
		return $config['apiUrl'].$config['imgUrl'].$value;
	}
}