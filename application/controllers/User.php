<?php
use service\UserToken;
use think\Db;
/**
 * xushuhui
 * 2017/10/20
 * 15:30
 */

class UserController extends BaseController
{
	// 报名
	public function signAction()
	{
		$data = request()->post();
		$data['uid'] = $this->uid;
	
		$files = file_put_contents(CACHE_PATH . 'test.jpg' , base64_decode($data['image']));
		$cfile = new CURLFile(CACHE_PATH . 'test.jpg','image/jpg');
		$img = curl_upload('http://votebackend.phpst.cn/Login/base64ImgUpload',['image' => $cfile]);
		$data['imgurl'] = json_decode($img,true)['msg'];

		//$data['uid'] = 1;
		$res = PlayerModel::create($data,true);
		if(!$res){
			return json(-1000,'报名失败');
		}
		return json(0,'success');
	}

	//投票
	public function voteAction()
	{
		$uid =  $this->uid;//TODO::获取用户id
	
		$player_id = request()->post('player_id');
		if($player_id == 0){
			return json(-1000,'请输入选手id');
		}
		Db::startTrans();
		try{
			$ret = $this->checkVotePermission($uid);//TODO::限制用户只能每天投一票
			$res = VotesModel::create([
					'player_id'=>$player_id,
					'uid' => $uid
			]);
			$res = PlayerModel::where("id","=",$player_id)->setInc('votes');
			Db::commit();
		} catch (\think\Exception $e) {
			return json(-1000,$e->getMessage());
			// 回滚事务
			Db::rollback();
		}
		return json(0,'success');
	}




	//检测用户是否有权限投票
	private function checkVotePermission($uid)
	{
		//"select * from table where create_time>".strtotime(date("Y-m-d 00:00:00"))
		$now = strtotime(date("Y-m-d 00:00:00"));
		$data = VotesModel::where("create_time",">",$now)->where("uid","=",$uid)->find();
		if($data){
			return json(-1000,'今天已经投过票了');
		}
		return true;
	}

}