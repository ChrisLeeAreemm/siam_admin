# 用户权限说明

用户权限 = 角色组权限 + 个人特殊独立权限 
## 服务端验证权限

代码封装于`app/common/UserAuthHelper.php`

调用方式为

```php
use app\common\UserAuthHelper;

$user = \app\model\UsersModel::get(1);
// 实际在代码控制器中  可以用以下方式
// $user = $this->who;
UserAuthHelper::get_list_by_user($user);
```


## 前端根据权限渲染dom

系统在layui的`view` module，已经封装了`renderAuth` 方法，在引入该module的时候将会自动执行一次（在tab新打开时）

将会遍历带有`data-siam-auth`的dom节点，然后验证权限是否拥有，没有则将会移除该dom

```js
view.renderAuth = function(dom){
    dom.find("[data-siam-auth]").each(function (i) {
        let auth = $(this).attr('data-siam-auth');
        if (view.vifAuth(auth) === false){
            $(this).remove();
        }
    })
};
```


### 使用方式

这是一个添加按钮，被`add_user_btn` 该权限节点控制 （后台的auth_rules）

```html
<button class="layui-btn layui-btn-sm" data-siam-auth="add_user_btn">
    <i class="layui-icon">&#xe654;</i>添加
</button>
```

如果是在数据表格等异步渲染的地方，需要在组件渲染完成之后，手动再调用一次
```js
let view = layui.view;

done(){
    view.renderAuth($("body"));// 这里也可以是具体的 小一点的 dom  如$("#panel")
}
```
