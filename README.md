> AWD攻防WAF 由php编写 轻量 关键字过滤 日志记录 一键部署

# 安装
上传 `waf.php` 到web根目录
进入web根目录执行以下命令，waf立即生效
```shell
php waf.php
```

# 修改
你可以对 `waf.php` 做出修改以达到自己的目的
或者你不想使用了可以直接删除该 waf


# 日志
日志记录在 `waflog`开头的目录中
- 对上传的文件进行拦截，用户上传的文件保存在 waflog/upload 中,
- 检测到疑似攻击的日志记录在 attack.log 中，也会记录在 req.log 中
- 所有请求记录在 req.log 中
- 检测为攻击的请求会将拦截的内容存储在 attact.log 中
- 对所有上传的文件进行无规则内容清理

# 配置
```php
// 安装选项
define('CONF_LOG_PATH', $log_path);    # 设置日志路径
define('CONF_CHECK_UPLOAD_FILE', 'T');    # 是否处理文件上传
define('CONF_WAF_NAME', 'waf.php');    # waf文件名
```

你可以保持默认
`最好修改自定义的日志目录路径， 不然知道此waf的攻击者可能进行文件包含攻击!`
你可以修改waf名称，如果你修改了waf名称，那么请手动删除安装程序 waf.php

# 关于 
source.php 为本waf的源码，如果有能力可自行更改规则等