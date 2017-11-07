<?php
/**
 * xushuhui
 * 2017/10/21
 * 10:39
 */
class PrizeController extends BaseController
{
	//奖品列表页面
	public function indexAction()
	{
		$activity_id = $this->getRequest()->getParam("activity_id");
		if($activity_id == 0){
			return json(-1000,'请输入活动id');
		}
		$data = ActivityModel::where('id','=',$activity_id)->field(['prize','rules'])->find();
		return json(0,'success',$data);
	}
	//榜单页面
	public function rankAction()
	{
		$activity_id = $this->getRequest()->getParam("activity_id");
		$rank = $this->getRequest()->getParam("rank");
		$page = $this->getRequest()->getParam("page");
		if($activity_id == 0){
			return json(-1000,'请输入活动id');
		}
		if(empty($rank)){
			return json(-1000,'请输入榜单名称');
		}
		$page = $page == 0 ? 1 :$page;
		$data = PlayerModel::where('activity_id','=',$activity_id)->order($rank,'desc')->page($page,10)->select();
		return json(0,'success',$data);
	}

	//礼物列表
	public function giftsAction()
	{
		$data = GiftsModel::all();
		return json(0,'success',$data);
	}

}