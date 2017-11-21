<?php

/**
 * User: xushuhui
 * Date: 2017/2/25
 * Time: 12:52
 */
/* bootstrap class should be defined under ./application/Bootstrap.php */
class Bootstrap extends Yaf\Bootstrap_Abstract {

	public function _initConfig() {
		//把配置保存起来
		$arrConfig = Yaf\Application::app()->getConfig();
		Yaf\Registry::set('config', $arrConfig);
	}

	public function _initPlugin(Yaf\Dispatcher $dispatcher) {

		//注册一个插件
		//$objSamplePlugin = new SamplePlugin();
		//$dispatcher->registerPlugin($objSamplePlugin);
	}

	public function _initRoute(Yaf\Dispatcher $dispatcher) {
		//在这里注册自己的路由协议,默认使用简单路由
	}

	public function _initView(Yaf\Dispatcher $dispatcher){
		//在这里注册自己的view控制器，例如smarty,firekylin
		Yaf\Dispatcher::getInstance()->disableView();    //如果只是提供数据接口，则禁止模板输出
	}

	public function _initLoader(Yaf\Dispatcher $dispatcher){
		//导入一个函数库文件helpers.php，即可使用helpers.php中的函数
		Yaf\Loader::import(APPLICATION_PATH.'/application/Base.php');


	}



}