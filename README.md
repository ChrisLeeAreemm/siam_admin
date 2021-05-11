Siam Admin 2.0
===============

> 运行环境要求PHP7.1+，兼容PHP8.0。基于TP6.0开发。

# 📣 SiamAdmin 📣

- 🔎 统一状态码等
- 🏬 模块规范设计，代码整洁
- ✨ CURD一键生成代码
- 🐌 TOKEN管理器，实现单点登录、强制下线等
- 🔨 API限流器（可视化配置，总限流、分组限流、账号token限流等）
- 🕐 定时任务管理、运行
- 📖 数据库字典
- 🔌 数据库优化
- 🎓 数据库备份
- 📰 异常记录
- 🎳 请求监控、复发
- 👻 站内信

...

> 有任何问题请在github 提交issue 或联系作者

## 安装运行

- composer install
- php think run (开发阶段)
- 安装数据库 http://127.0.0.1:8000/index.php 运行向导
- 后台首页 http://127.0.0.1:8000/admin/index.html

## 其他事项

- 前端开发文档：http://layuimini.99php.cn/docs/

插件思路&规则
===================
1. [start.plugs] 代表 启用的插件 规则为json文本
2. 第一次启用插件的时候，判断[插件目录下是否有install.lock]文件，无则为第一次安装，执行插件的install事件。然后往[start.plugs]文件插入该插件名
3. 插件没有卸载功能，可以反复停用、启用，不丢失历史数据
4. 插件里的数据表命名格式`表前缀_插件名_表名` 如，`siam_cms_articles`
5. 创建新插件`php think plugs --name="TesSiam"`  