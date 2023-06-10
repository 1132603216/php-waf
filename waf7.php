<?php
/* waf基本组成：
        日志记录
 *      拦截关键字，如sql语句
 *      文件上传拦截,清空文件内容
 * */
//基本设置
date_default_timezone_set("PRC");
$pattern = "/select|insert|update|delete|and|union|load_file|outfile|dumpfile|hex|file_put_contents|fwrite|curl|system|eval|assert|echo|passthru|exec|system|chroot|scandir|chgrp|chown|shell_exec|proc_open|proc_get_status|popen|ini_alter|ini_restore|dl|openlog|syslog|readlink|symlink|popepassthru|stream_socket_server|assert|pcntl_exec/i";
define("BLACKLIST", $pattern);
define('TIME', (string)time());
define('ROOT', $_SERVER['CONTEXT_DOCUMENT_ROOT']);  # 根目录
define('WAFLOG', ROOT . '/waf_log');  # 日志目录
define('WAFUPLOAD', ROOT . '/waf_upload/');  # 文件上传目录
define('REQ', WAFLOG . '/req.log');  # 请求日志文件
define('ATTACK', WAFLOG . '/attack.log');  # 攻击请求日志文件
if (!file_exists(WAFLOG)) mkdir(WAFLOG);  # 创建目录

# 请求头
$headers = array();
foreach ($_SERVER as $k => $v) {
    if (substr($k, 0, 5) === 'HTTP_') {
        $headers[$k] = $v;
    }
}

//http数据包
$http = TIME . PHP_EOL
    . date("Y-m-d H:i:s") . PHP_EOL .
    $_SERVER['REQUEST_METHOD'] . PHP_EOL .
    $_SERVER['REMOTE_ADDR'] . PHP_EOL .
    $_SERVER['REQUEST_URI'] . PHP_EOL .
    print_r($headers, true) . PHP_EOL . PHP_EOL .
    file_get_contents('php://input') . PHP_EOL . PHP_EOL;


# 请求记录
file_put_contents(REQ, HTTP, FILE_APPEND);
//攻击记录
if (preg_match(BLACKLIST, HTTP, $out)) {
    file_put_contents(ATTACK, print_r($out, true) . PHP_EOL . HTTP, FILE_APPEND);  # 请求记录
    die('flag{attack}');
}


//文件上传记录
if (isset($_FILES) && $_FILES) {
//        获取文件名
    $filename = $_FILES['file']['name'];
    $suffix = strrchr($filename, '.');
    $_FILES['file']['name'] = TIME . $suffix;  # 修改文件名
//            文件危险等级
    $grade = preg_match('/php/i', $suffix) ? '危险' : '普通';
    $data = file_get_contents($_FILES['file']['tmp_name']);
    $data = $filename . '  ' . $grade . PHP_EOL . $data . PHP_EOL . PHP_EOL;
    if (!file_exists(WAFUPLOAD)) mkdir(WAFUPLOAD);
    file_put_contents(WAFUPLOAD . TIME . '-txt', $data);   # 记录文件
//        滞空内容
    file_put_contents($_FILES['file']['name'], '');
    if ($grade === '危险') die('flag{file}');
}
?>
