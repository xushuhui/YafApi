<?php

class IndexController extends BaseController{

	//首页接口
	public function indexAction() {//默认Action
		$id = $this->getRequest()->getParam("id");
		$data = CompanyModel::where("id","=",$id)->with(['activity'])->find();
		if($data){
			$data['end_seconds'] = strtotime($data['activity']['end_time']);
			$data['is_open'] = time()-strtotime($data['activity']['start_time'])>0 ? true : false;
			$data['visiting'] = PlayerModel::where('activity_id','=',$data['activity']['id'])->sum('visiting');
			$data['votes'] = PlayerModel::where('activity_id','=',$data['activity']['id'])->sum('votes');
			$data['players'] = PlayerModel::where('activity_id','=',$data['activity']['id'])->count('id');	
		}
		return json(0,'success',$data);
	}



	//搜索接口
	public function searchAction()
	{
		$name = $this->getRequest()->getParam("name");
		$activity_id = $this->getRequest()->getParam("activity_id");
		$player_id = $this->getRequest()->getParam("player_id");
		if($activity_id == 0){
			return json(-1000,'请输入活动id');
		}
		if(empty($name) &&  empty($player_id)){
			return json(-1000,'请输入搜索关键字或者编号');
		}
		if($name){
			$data = PlayerModel::where('name','like','%'.$name.'%')->where('activity_id','=',$activity_id)->field(['id','name','imgurl','votes','visiting','gifts'])->select();
		}
		if($player_id > 0){
			$data = PlayerModel::where('id','=',$player_id)->where('activity_id','=',$activity_id)->field(['id','name','imgurl','votes','visiting','gifts'])->select();

		}
		return json(0,'success',$data);

	}

	//作品列表接口
	public function playersAction()
	{
		$activity_id = $this->getRequest()->getParam("activity_id");
		$page = $this->getRequest()->getParam("page");
		$page = $page == 0 ? 1 :$page;
		if($activity_id == 0){
			return json(-1000,'请输入活动id');
		}
		$data = PlayerModel::where('activity_id','=',$activity_id)
				->field(['id','name','imgurl','votes','visiting','gifts'])->order("id desc")->page($page,10)->select();
		return json(0,'success',$data);
	}

	//选手详情页面
	public function playerAction()
	{
		$player_id = $this->getRequest()->getParam("player_id");
		if($player_id == 0){
			return json(-1000,'请输入选手id');
		}
		//增加访问量
		$data = PlayerModel::where('id','=',$player_id)->find();
		$res = PlayerModel::where('id','=',$player_id)->setInc("visiting");
		return json(0,'success',$data);
	}

	//选手礼物列表页面
	public function playerGiftsAction()
	{
		$player_id = $this->getRequest()->getParam("player_id");
		$page = $this->getRequest()->getParam("page");
		$page = $page == 0 ? 1 :$page;
		if($player_id == 0){
			return json(-1000,'请输入选手id');
		}
		$data = OrderModel::where('player_id','=',$player_id)->with(['wxuser'])
				->field(['id','player_id','pay_time'])->page($page,10)->select();

		return json(0,'success',$data);
	}

}
