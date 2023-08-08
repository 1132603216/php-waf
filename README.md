> AWD攻防WAF 由php编写 轻量 关键字过滤 日志记录 一键部署

# 安装
上传 `waf.php` 到web根目录
进入web根目录执行以下命令，waf立即生效
```shell
php waf.php
```


# 卸载
直接删除 `waf.php`

# 日志
日志记录在 `waflog` 目录中，请保该目录有可写的权限
- 对上传的文件进行拦截，用户上传的文件保存在 waflog/upload 中,文件名为 `ip-port-timestamp`，原本上传的文件内容将清空处理
- 对 `GET` 参数进行拦截，记录在 `GET.log` 文件中
- 对 `请求体` 进行拦截, 记录在 `BODY.log` 文件中
- 对 `请求头`进行拦截 ,记录在 `HEADER.log` 文件中

# 配置WAF
如果你想对waf进行配置，你可以对 `define()` 中以 `CONF` 开头的常量进行修改
编辑 waf.php
