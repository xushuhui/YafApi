<?php
namespace extend\Wx;

class Wechat
{

	protected static $instance;
	protected static $appid;
	protected static $appsecret;
	protected function __construct($appid='',$appsecret='')
	{
		self::$appid = $appid;
		self::$appsecret = $appsecret;
	}
	/**
	 *
	 *  @access public
	 *  @param array $options 参数
	 */
	public static function instance($appid='',$appsecret='')
	{
		if (is_null(self::$instance)) {
			self::$instance = new self($appid,$appsecret);
		}
		return self::$instance;
	}

	public function index()
	{
		//获得参数 signature nonce token timestamp echostr
		$nonce     = $_GET['nonce'] ? $_GET['nonce'] : '' ;
		$token     = 'wechat';
		$timestamp =  $_GET['timestamp'] ? $_GET['timestamp']: '';
		$signature = $_GET['signature'] ?$_GET['signature']: '';
		//形成数组，按字典序排序
		$arr     = array($nonce,$timestamp,$token);
		sort($arr);
		//拼接字符串，sha1加密，与signature比较
		$str = sha1(implode($arr));
		if($str == $signature &&  $_GET['echostr']){
			//第一次接入微信api接口时
			echo $_GET['echostr'];
			exit();
		}else{
			$this->responseMsg();
		}
	}
	//接收事件推送并回复
	public function responseMsg()
	{
		//获取到微信推送的xml格式的post数据
		//$postArr = $_GLOBALS['HTTP_RAW_POST_DATA'];
		$postArr = file_get_contents("php://input");
		//处理消息类型 并设置回复类型和内容
		$postObj = simplexml_load_string($postArr);
		//xml转化成对象

		//判断该数据是否是订阅事件推送
		if(strtolower($postObj->MsgType) == 'event'){
			if(strtolower($postObj->Event) == 'subscribe'){

				$content = "请输入关键词查看今天热点新闻,1军事，2体育，3科技，4教育，5娱乐，6财经，7股票，8旅游，9女性";
				$indexObj = new Model();
				$indexObj->responseText($postObj,$content);
			}
			//自定义菜单事件推送
			if(strtolower($postObj->Event) == 'click'){
				if(strtolower($postObj->EventKey) == 'item1'){
					$content = 'item1事件推送';
				}
				if(strtolower($postObj->EventKey) == 'songs'){
					$content = 'songs事件推送';
				}
				$indexObj = new Model();
				$res = $indexObj->responseText($postObj,$content);
			}
		}
		//自定义菜单事件推送

		//纯文本消息回复 和图文回复
		if(strtolower($postObj->MsgType) == 'text'){
			//war	军事  sport	体育   tech	科技    edu	教育    ent	娱乐   money	财经     gupiao	股票     travel	旅游 lady	女人
			switch (trim($postObj->Content)) {
				case 1:
					$type = 'war';
					break;
				case 2:
					$type = 'sport';
					break;
				case 3:
					$type = 'tech';
					break;
				case 4:
					$type = 'edu';
					break;
				case 5:
					$type = 'ent';
					break;
				case 6:
					$type = 'money';
					break;
				case 7:
					$type = 'gupiao';
					break;
				case 8:
					$type = 'travel';
					break;
				case 9:
					$type = 'lady';
					break;
				default:
					$content = '谢谢';
			}

			$indexObj = new Model();
			$url = "http://wangyi.butterfly.mopaasapp.com/news/api?type=".$type."&page=1&limit=5";
			$data = $this->http_curl($url)['list'];

			if(!empty($data) && is_array($data)){

				foreach($data as $k => $v){
					if($data[$k]['imgurl']==''){
						unset($data[$k]['imgurl']);
						$data[$k]['picurl'] = 'https://www.baidu.com/img/bd_logo.png';
					}else{
						$data[$k]['picurl'] = $data[$k]['imgurl'];
						unset($data[$k]['imgurl']);
					}

					$data[$k]['description'] = $data[$k]['title'] ? $data[$k]['title']: '新闻';
					$data[$k]['url'] = 'https://www.baidu.com/';

				}
			}

			$res = $indexObj->responseNews($postObj,$data);

		}
	}

	public function http_curl($url,$type='get',$res='json',$arr='')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		if($type == 'post'){
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
		}
		$output = curl_exec($ch);
		if($res == 'json')
		{
			if(curl_errno($ch)){
				return curl_error($ch);
			}else{
				return json_decode($output,true);
			}
		}
		curl_close($ch);

	}
	//获取access_token
	public function getAccessToken()
	{
		$access_token = cache('access_token');
		if($access_token){
			//access_token存在并且没过期
			return $access_token;
		}else{
			cache('access_token', NULL);
			//重新获取access_token
			$appid = self::$appid;
			$appsecret = self::$appsecret;
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
			$res = $this->http_curl($url,'get','json');
			cache('access_token', $res['access_token'], 7000);
			return $res['access_token'];
		}

	}
	//获取服务器IP
	public function getServerIp()
	{
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$access_token;
		$res = $this->http_curl($url);
		return $res;
	}
	//自定义菜单
	public function definedMenu()
	{
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
		$postArr = array(
			'button'=>array(
				array(//第一个一级菜单
					'type'=>'click',
					'name'=>urlencode('菜单一'),
					'key'=>'item1'
				),
				array(
					'name'=>urlencode('菜单二'),
					'sub_button'=>array(
						array(
							'type'=>'view',
							'name'=>urlencode('电影'),
							'url'=>'http://www.phpst.cn',
						),
						array(
							'type'=>'click',
							'name'=>urlencode('歌曲'),
							'key'=>'songs',
						),
					),
				),//第二个一级菜单
				array(
					'type'=>'view',
					'name'=>urlencode('菜单三'),
					'url'=>'http://www.qq.com',
				),//第三个一级菜单
			),

		);
		$postJson = urldecode(json_encode($postArr));
		$res = $this->http_curl($url,'post','json',$postJson);
		return $res;
	}
	//推送模板消息
	public function sendTemplateMsg($openid='',$template_id='',$JumpUrl='',$data=array()){
		$access_token = $this->getAccessToken();
		$array = array();
		$array = array(
			"touser"=>$openid,
			"template_id"=>$template_id,
			"url"=>$JumpUrl,
			// "data"=>array(
			// 	'name'=>array('value'=>'我是徐曙辉','color'=>'#173177'),
			// 	'data'=>array('value'=>'hello','color'=>'#173177'),
			// 	'user'=>array('value'=>'芬','color'=>'#173177'),
			// 	),
			"data"=>$data
		);
		$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
		$postJson = json_encode($array);
		$res = $this->http_curl($url,'post','json',$postJson);
		return $res;
	}
	//获取用户openid 静默授权
	public function getBaseInfo($redirect_uri)
	{
		$appid = self::$appid;
		$appsecret = self::$appsecret;

		//$redirect_uri = urlencode("http://test.phpst.cn/wechat/Index/getUserInfo");
		$redirect_uri = urlencode($redirect_uri);
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
		header('location:'.$url);
	}
	//获取用户个人信息
	public function getUserInfo($code=''){
		$appid = self::$appid;
		$appsecret = self::$appsecret;
		//$code = $_GET['code'];
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
		$res = $this->http_curl($url,'get');
		$openid = $res['openid'];
		$access_token = $res['access_token'];
		$url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
		$res = $this->http_curl($url,'get');
		return $res;
	}
	//群发接口
	public function sendMsgAll()
	{
		$access_token = $this->getAccessToken();
		//正式接口
		//$url = "https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=".$access_token;
		//预览接口
		$url = "https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=".$access_token;
		$array = array(
			"touser"=> 'osdh3wGU5kRhsu0yATpYB7aXGkTc',
			"msgtype" => "text",
			"text" => array('content' => 'I am a programmer'),
		);
		$postJson = json_encode($array);
		$res      = $this->http_curl($url,'post','json',$postJson);
		return $res;
	}
	//获取jsaoi_ticket 用于jssdk
	public function getJsApiTicket()
	{
		$jsapi_ticket = cache('jsapi_ticket');
		if($jsapi_ticket){
			return $jsapi_ticket;
		}else{
			cache('jsapi_ticket',null);
			$access_token = $this->getAccessToken();
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";
			$res = $this->http_curl($url);
			cache('jsapi_ticket', $res['ticket'], 7000);
			return $res['ticket'];
		}

	}
	//获取随机码
	public function getRandCode()
	{
		$array = array(
			'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
		);
		$tmpstr = '';
		$max = count($array);
		for ($i=0; $i < $max; $i++) {
			$key = rand(0,$max-1);
			$tmpstr .=$array[$key];
		}
		return $tmpstr;
	}
	//微信分享
	public function shareWechat($url = '')
	{
		$data['jsapi_ticket'] = $this->getJsApiTicket();
		$data['timestamp'] = time();
		$data['noncestr'] = $this->getRandCode();
		//$url = "http://test.phpst.cn/wechat/Index/shareWechat";
		$signature = "jsapi_ticket=".$data['jsapi_ticket']."&noncestr=".$data['noncestr']."&timestamp=".$data['timestamp']."&url=".$url;
		$data['signature'] = sha1($signature);
		return $data;

	}
	//临时二维码
	public function getTmpQrCode()
	{
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
		$postArr = array(
			'expire_seconds'=>604800,
			'action_name'=>'QR_SCENE',
			'action_info'=>array(
				'scene'=>array('scene_id'=>2000),
			),
		);
		$postJson = json_encode($postArr);
		$res      = $this->http_curl($url,'post','json',$postJson);
		$ticket = $res['ticket'];
		$url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);
		echo "<img src=".$url.">";
	}
	//永久二维码
	public function getForverQrCode(){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
		$postArr = array(
			'action_name'=>'QR_LIMIT_SCENE',
			'action_info'=>array(
				'scene'=>array('scene_id'=>123),
			),
		);
		$postJson = json_encode($postArr);
		$res      = $this->http_curl($url,'post','json',$postJson);
		$ticket = $res['ticket'];
		$url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);
		echo "<img src=".$url.">";
	}
	// 调试打印数据
	public  function LogWrite($logs = array()) {

		$myfile = fopen("a.txt", "w") or die("Unable to open file!");
		$file_name = "a.txt";
		file_put_contents($file_name, var_export($logs, true), FILE_APPEND);
	}
	// 获取永久素材列表
	public function getMaterialList($type='news')
	{
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=".$access_token;
		$postArr = array(
				'type'=>$type,
				'offset'=>0,
				'count'=>20,
		);
		$postJson = json_encode($postArr);
		$res      = $this->http_curl($url,'post','json',$postJson);
		return $res;
	}
	// 获取素材
	public function getOneMaterial($media_id='')
	{
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=".$access_token;
		$postArr = array(
			'media_id' => $media_id
		);
		$postJson = json_encode($postArr);
		$res      = $this->http_curl($url,'post','json',$postJson);
		return $res;
	}
}


