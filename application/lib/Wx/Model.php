<?php
namespace Wx;
class Model
{

	//回复图文消息
	public function responseNews(object $postObj,array $arr)
	{
		$toUser = $postObj->FromUserName;
		$fromUser = $postObj->ToUserName;
		$time = time();

		$template = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<ArticleCount>".count($arr)."</ArticleCount>
						<Articles>";
		foreach ($arr as $k => $v) {
			$template.= "<item>
				<Title><![CDATA[".$v['title']."]]></Title>
				<Description><![CDATA[".$v['description']."]]></Description>
				<PicUrl><![CDATA[".$v['picurl']."]]></PicUrl>
				<Url><![CDATA[".$v['url']."]]></Url>
				</item>";
		}
		$template.=	"</Articles>
							</xml> ";
		echo sprintf($template,$toUser,$fromUser,$time,'news');
	}
	//回复单文本
	public function responseText($postObj,$content)
	{
		$template = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						</xml>";
		$toUser = $postObj->FromUserName;
		$fromUser = $postObj->ToUserName;
		$time = time();
		$msgType = 'text';
		echo sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
	}
	//回复关注
	public function responseSubscribe(object $postObj,array $content)
	{
		$res = $this->responseNews($postObj,$content);
		return $res;
	}
}
