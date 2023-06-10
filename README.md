# 介绍
这些waf是在打线上awd编写的
当时登堂入室，于是参考网上的匹配规则然后写了一个自己的waf

waf1-7： 也就是版本1.1 到1.7， 变化不大，主要就是做了一些改进
add.php: 为当前目录下的所有php文件添加此waf
waf_v2： 版本v2, 也是最后一个版本，有一个ui界面，算是比较大的改进

waf会拦截所有请求并记录日志，日志文件在根目录，并会将疑似攻击的请求单独记录到攻击日志中并拦截
建议使用waf7.php



# 使用
添加waf,  将其中一个 waf 和 add.php 放入根目录中，并且将该waf改名为 waf.php，并运行add.php即可
```bash
php add.php
```

如果是 waf_v2, 那么还可以访问管理界面
`http://ip/waf.php?wlb=7f04a3978a2c0bf4fcc394da85c3d916`

其中的wlb参数就是密码，默认密码为 md5("彪彪彪")，也就是 7f04a3978a2c0bf4fcc394da85c3d916

如何需要更改密码 设置 $password 变量即可


# 检测
- 请求头
- 文件上传
- get
- post
