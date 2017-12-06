YafApi 1.0
===============

YafApi接口,兼具yaf的简洁速度和thinkPHP的简单易上手，专门用来开发api。

Github地址 (https://github.com/xushuhui1992/YafApi)

码云地址 (https://gitee.com/xushuhui/YafApi)

coding地址 (https://git.coding.net/xushuhui/YafApi.git)

## 开发计划
1. 增加请求和响应，配置读取（已完成）
2. 增加日志记录，错误和异常处理 （已完成）
3. 完成redis队列功能（放到后期）
4. 增加orm（已完成）
5. 增加参数验证 （已完成）
6. 完善路由功能 (待定)
## 目录结构

初始的目录结构如下：

~~~
www  WEB部署目录（或者子目录）
├─application           应用目录
│  ├─controller         控制器目录
│  ├─model              模型目录
│  ├─views              视图目录
│  ├─service            服务层目录
│  ├─library            核心类库目录
│  ├─modules            其他模块目录
│  ├─plugins            插件目录
│  ├─extend             自定义类库
│  ├─Base.php           基础定义文件
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




