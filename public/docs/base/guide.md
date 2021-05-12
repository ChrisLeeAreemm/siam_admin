# 框架规范说明

## 目录

SiamAdmin是基于TP6框架的业务封装系统，仅仅将常用、好用的代码封装起来，定一些使用规范来让项目更加可控。

所以主要的工作目录为 `app` 目录

关于app目录，以下为细分子目录，以及对应的工作说明

```
├─bean          一些bean的定义
├─cache         缓存相关的定义，比如封装一个UserCache::get(x) 这些逻辑
├─command       自定义命令行相关
├─common        通用，一般存放助手型封装  如FileHelper AuthHelper
├─controller    控制器※
│  ├─admin      admin模块，存放admin端对接接口
│  ├─agent      agent模块，存放代理型接口 开放给代理 对外api
│  ├─index      index模块，存放公开型接口，无需验证的非敏感接口
│  └─notify     notify模块，存放回调型接收接口
├─cron          定时任务存放目录
├─event         事件存放目录
├─exception     异常类存放目录
├─facade        门面类存放目录，比如一些Redis SQLite 的使用门面
├─handle        处理者，比如路由接管、异常接管、日志接管
├─model         模型※
├─plugs         插件※
└─service       逻辑※
```

## bean

!> 跟java的bean可能存在不一致的地方，因为作者是phper，没有用过java...请谅解，也希望能得到交流和补充 

bean有以下特性
- 1、所有属性为private
- 2、提供默认构造方法
- 3、提供getter和setter
- 4、实现serializable接口

个人使用场景：

数据库有一些字段，如消费者信息，储存的是一个json字符串。若每次都以数组的形式操作，则可能会导致在不同地方的 键名、格式 出错。

此时定义一个bean，统一操作bean，然后写入时，bean转为json字符串，可保证数据格式的一致

!> 以前旧的写法，涉及的逻辑多了，在不同的地方/或者多个同事对该字段进行操作，可能导致键名的错误编写
```php
$customer_info = [
    'customer_name'    => 'siam',  
    'customer_phone'   => '59419979',  
    'customer_address' => 'guangzhou',  
];
$order->customer_info = json_encode($customer_info, 256);
```

使用bean，就有了统一的方法定义，如系统提供的参考CustomerInfoBean


定义bean时，我们继承`EasySwoole\Spl\SplBean`这个类，提供默认的toArray转换、构造函数等。同时使用idea 生成setter和getter(可以百度或者交流群讨论)。

> easyswoole是作者加入的另一个框架开发维护

## 模块规范

SiamAdmin默认使用的是  `[模块名]/[控制器]/[方法]`  这种路由格式

路由处理者为` app/handle/RouterDispatch.php `

!> 你也可以使用自定义路由的方式来使用，但此时关于插件的`运行模块`，就会失效了~ 插件的运行模块解析逻辑是通过url 第一个占位来解析。相关逻辑为 `app/plugs/PlugsInitEvent.php`

```php 
// 不开启插件模块
$siam_plugs = env("app.siam_plugs");
if (!$siam_plugs) return false;

if ($module === null){
    $path_info = request()->pathinfo();
    $path_info = explode('/', $path_info);
    SiamApp::getInstance()->setModule($path_info[0]);
    $module = $path_info[0];
}
```


## 控制器规范

- 每个模块的控制器，都需要继承所在模块的Base控制器，才能实现统一的权限认证、代码封装使用
- 命名规则为：`模块名+控制器名+Controller` 后缀。如AdminUserController
- 控制器可以声明属性，鉴权白名单，将不校验token，如用户控制器的登录方法 `protected $white = ['login'];`


## 模型规范

- 参考代码规范说明

## 代码风格说明

- 方法名、函数名、变量名，都统一用`蛇形`
- 类名用`大驼峰`
- 参考代码规范说明

## 缓存说明

- 缓存时候，对于key的使用，需要在cache目录下的CacheTag中注册声明，写好注释，这样子才不会造成某些情况下的key冲突，便于管理

## api返回码说明

- 返回码定义在exception下的ErrorCode，新增通用错误码场景需要在其中注册声明，便于管理，api单个独立的错误码为60x系列，根据具体逻辑定义