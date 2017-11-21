YafApi 1.0
===============

YafApi接口,兼具yaf的简洁速度和thinkPHP的简单易上手，专门用来开发api。

## 开发计划
1. 完成日志功能，修改请求和响应方法，以便于API开发
2. 完成redis队列功能
3. 完成异常抛出和参数验证功能
4. 完成缓存功能（文件，redis，memcache）（文件缓存已完成）

## 目录结构

初始的目录结构如下：

~~~
www  WEB部署目录（或者子目录）
├─application           应用目录
│  ├─controller         控制器目录
│  ├─model              模型目录
│  ├─views              视图目录
│  ├─library            核心类库目录
│  ├─modules            其他模块目录
│  ├─plugins            插件目录
│  ├─Base.php           基础定义文件
│  ├─Bootstrap.php      框架入口文件
│  ├─Helpers.php        助手函数文件
│  └─Common.php             公共方法文件
├─conf                  配置文件目录
├─extend                扩展类库目录
├─public                WEB目录（对外访问目录）
│  ├─index.php          入口文件
│  └─.htaccess          用于apache的重写
├─runtime               应用的运行时目录（可写，可定制）
├─vendor                第三方类库目录（Composer依赖库）
├─composer.json         composer 定义文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
~~~



## 版权信息

版权所有Copyright © 2006-2017 by xushuhui  (https://www.phpst.cn)

All rights reserved。




