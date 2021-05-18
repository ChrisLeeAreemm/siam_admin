# Api限流器（apiFilter）

自定义u_id, token, ip 等规则的访问限流，便于saas系统等。

限流统计级别是1秒，1s内超出配置数量值的请求会被拒绝。

## 启动相关

1. Api限流器使用了 Redis 作为数据缓存服务，在使用限流器之前请确保Redis服务正常工作。
2. 如何启动，使用命令行 `php think api-fitler`

## 检测逻辑

全局限流 -> ip限流 -> 自定义限流

```php
use app\plugs\apiFilter\service\ApiAccessContain;
use think\facade\Event;
use app\exception\ErrorCode;

//全局限流
$set = ApiAccessContain::getInstance()->getNumber(ApiAccessContain::API_FILTER_TOTAL);
if ($set >= 0) {
    if (ApiAccessContain::getInstance()->getAccess(ApiAccessContain::API_FILTER_TOTAL) > $set) {
        throw new Exception('全局限流',ErrorCode::WAF_IP_FILTER);
    }
}

// ip限流
$ip = request()->ip();
$set = ApiAccessContain::getInstance()->getNumber($ip);
if ($set >= 0) {
    if (ApiAccessContain::getInstance()->getAccess($ip) > $set) {
        throw new Exception('IP限流',ErrorCode::WAF_IP_FILTER);
    }
}

// token限流/分组限流/自定义限流  从Token限流事件中获取Token
Event::listen('TokenFilter', function ($token) {
    if (isset($token)) {
        $set = ApiAccessContain::getInstance()->getNumber($token);
        if (ApiAccessContain::getInstance()->getAccess($token) > $set) {
            throw new Exception('Token限流',ErrorCode::WAF_IP_FILTER);
        }
    }
});
```

## 添加规则

apiFilter组件中可以自由添加限流规则，内置的默认规则如下

| 配置key | 配置数量 |
| :------ | :------- |
| TOTAL   | 30       |

配置key (自定规则)可以如下:

- TOTAL:（默认生成，全局限流）
- 120.555.555.11 (IP规则)
- u_id:1(Token限流，带有明确标识的规则，用户ID：1)
- token
- 用户组
等等
## 自定义限流规则

#### 如何使用自定义限流规则？

##### 全局限流

全局限流通过默认规则是配置项 的 `TOTAL` 设置，默认值是 30 。

##### IP限流

在配置项中，以IP的规则来设置，当有请求进入时，会自动触发IP限流对请求的IP地址进行检测

##### 自定义Token

在你任何需要的限流的代码块中，使用 

```php
Event::trigger('TokenFilter','userId:9527')
```

触发一个限流规则事件，此标记表示：触发 `TokenFilter` 限流事件，并表示触发的规则为 `userId:9527`，当触发的规则是已设置并存在于缓存标记中时，规则才会生效。



> 同理，userId:9527 也可以换成用户的Token标识，用户角色组等。



*伪代码示例*

```php
class AdminUsersController extends AdminBaseController
{

    /**
     * @return mixed
     * @throws \think\Exception
     */
    public function get_list()
    {	
        $user_id = input('user_id');
        // 在方法开头触发一个限流规则(确保规则已设置),比如
        // 1. 需要限制某用户请求列表的频率
            $rule = 'userId:'.$user_id;
            Event::trigger('TokenFilter',$rule);
        
        // 2. 限制整个角色组
        	$role_id = 1;
        	$rule    = 'roleId:'.$role_id;
        	Event::trigger('TokenFilter',$rule);
        
        // 3. 根据用户Token
		//.....more code
    }
}
```

