<?php

namespace think;

class Config
{
    // 配置参数
    private static $config_name = 'application';

    /**
     * 解析配置文件或内容
     * @param string    $config 配置文件路径或内容
     * @param string    $type 配置解析类型
     * @param string    $name 配置名（如设置即表示二级配置)
     * @return mixed
     */
    public static function parse($config, $name='',$type = 'ini')
    {
        $conf_name = CONF_PATH."/".$config.'.'.$type;
        $config = (new \Yaf\Config\Ini($conf_name,$name));
        return $config;
    }
    /**
     * 检测配置是否存在
     * @param string    $name 配置参数名（支持二级配置 .号分割）
     * @return bool
     */
    public static function has($name)
    {
        if (!strpos($name, '.')) {
            $config = self::parse(self::$config_name);
            return $config->__isset($name);
        } else {
            // 二维数组设置和获取支持
            $name = explode('.', $name, 2);
            $config = self::parse(strtolower($name[0]));
            return $config->__isset($name[1]);
        }
    }

    /**
     * 获取配置参数 为空则获取所有配置
     * @param string    $name 配置参数名（支持二级配置 .号分割）
     * @return mixed
     */
    public static function get($name = null)
    {
        // 无参数时获取所有
        if (empty($name) ) {
            return \Yaf\Registry::get('config')->toArray();
        }
        if (!strpos($name, '.')) {
            $name = strtolower($name);
            $config = $name == self::$config_name ?  self::parse(self::$config_name,$name) : self::parse($name);
            return $config->toArray();
        } else {
            // 二维数组设置和获取支持
            $name    = explode('.', $name, 2);
            $name[0] = strtolower($name[0]);
            $config = self::parse($name[0],$name[1]);
            return $config->toArray();
        }
    }



}
