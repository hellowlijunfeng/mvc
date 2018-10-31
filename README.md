

个人快速搭建轻量级mvc框架

目录结构

-app                       项目总目录

----index                     前台版块

--------controller              控制器

--------model                   模型

--------view                    视图

----admin                    后台版块

--------controller              控制器

--------model                   模型

--------view                    视图

-boot                      路由自动加载

-cache                     缓存

----index                     前台缓存 

----admin                     前台缓存

--------*.php                   数据库字段缓存

-config                    配置

----config.php                配置文件 

----namespace.php             命名空间对照

-public                    静态文件目录

----index                     前台 

----admin                     前台

-verder                    第三方库

-index.php                 入口文件

运行

http://www.cglife.top/index.php?c=index&m=index&a=index



