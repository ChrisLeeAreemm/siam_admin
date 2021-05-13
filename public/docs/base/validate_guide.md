# 参数验证

我们在基础规范中已经定义了，所有的控制器，都应该继承自身模块所处的Base控制器，如`AdminBaseController`。

而我们在继承控制器中，封装了验证器逻辑，只需要简单调用即可

可以自定义数据库`默认取request()->param()`、消息语句等，详情可以查看代码

```php
public function create_order()
{
    $this->validate([
        'o_sn' => 'require',
        'o_price' => 'require'
    ]);
}
```

```php
$order = OrdersModel::get_by_osn('123456');
$this->validate([
    'order' => 'require'
], [
    'order' => $order
],[
    'order.require' => '订单不存在'
]);
```