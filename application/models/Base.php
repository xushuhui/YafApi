<?php
/**
 * xushuhui
 * 2017/10/18
 * 17:18
 */

class BaseModel extends \think\Think{
	protected $hidden = ['create_time','update_time', 'delete_time','admin_id'];
	protected $autoWriteTimestamp = true;

}