<?php
/**
 * xushuhui
 * 2017/10/23
 * 17:44
 */

function LogWrite($logs = '', $label =  '', $fileName =  '')
{
	$file_name = APPLICATION_PATH . date('Y-m-d') . '.txt';
	$now_time = date('Y-m-d H:i:s');
	$log_message = "[$now_time]:" . $label . var_export($logs, true) . PHP_EOL;
	file_put_contents($file_name, $log_message, FILE_APPEND);
}
//获取随机码
function getRandCode($max=0)
{
	$array = array(
		'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
	);
	$tmpstr = '';
	for ($i=0; $i < $max; $i++) {
		$key = rand(0,$max-1);
		$tmpstr .=$array[$key];
	}
	return $tmpstr;
}
/**
 * @param string $url get请求地址
 * @param int $httpCode 返回状态码
 * @return mixed
 */
function curl_get($url, &$httpCode = 0)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	//不做证书校验,部署在linux环境下请改为true
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	$file_contents = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return $file_contents;
}

function curl_post($url, array $params = array())
{
	$data_string = json_encode($params);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt(
		$ch, CURLOPT_HTTPHEADER,
		array(
			'Content-Type: application/json'
		)
	);
	$data = curl_exec($ch);
	curl_close($ch);
	return ($data);
}
function curl_upload($url,array $params = array())
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);

	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

	if ($error = curl_error($ch) ) {
		die($error);
	}
	$response = curl_exec($ch);
	curl_close($ch);
	return ($response);
}
//获取订单号
function getOrderNo()
{
	$time = substr(date('YmdHis'),2,12);
	$str = getRandCode(4);
	return $time.$str;
}
function substr_replace_cn($string, $repalce = '*',$start = 0,$len = 0) {
	$count = mb_strlen($string, 'UTF-8'); //此处传入编码，建议使用utf-8。此处编码要与下面mb_substr()所使用的一致
	if(!$count) { return $string; }
	if($len == 0){
		$end = $count; 	//传入0则替换到最后
	}else{
		$end = $start + $len;		//传入指定长度则为开始长度+指定长度
	}
	$i = 0;
	$returnString = '';
	while ($i < $count) {		//循环该字符串
		$tmpString = mb_substr($string, $i, 1, 'UTF-8'); // 与mb_strlen编码一致
		if ($start <= $i && $i < $end) {
			$returnString .= $repalce;
		} else {
			$returnString .= $tmpString;
		}
		$i ++;
	}
	return $returnString;
}
