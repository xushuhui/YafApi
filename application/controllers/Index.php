<?php

class IndexController extends BaseController{

	//首页接口
	public function indexAction() {//默认Action
		$res = UserModel::create(['name' => 'xsh']);
		p($res);
	}



}
