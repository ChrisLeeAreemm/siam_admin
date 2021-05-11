# Api限流器（apiFilter）

自定义u_id, token, ip 等规则的访问限流，便于saas系统等。

限流统计级别是1秒，1s内超出配置数量值的请求会被拒绝。

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
TODO 
