<meta charset="utf8">
<form action="./waf3.php" method="post" enctype="multipart/form-data">
    <input type="file" name="file">
    <input type="submit" name="submit" value="提交">
</form>
<pre>

<?php
$pattern = "/select|insert|update|delete|and|or|union|into|load_file|outfile|dumpfile|sub|hex";
$pattern .= "|file_put_contents|fwrite|curl|system|eval|assert|echo";
$pattern .= "|passthru|exec|system|chroot|scandir|chgrp|chown|shell_exec|proc_open|proc_get_status|popen|ini_alter|ini_restore";
$pattern .= "|`|dl|openlog|syslog|readlink|symlink|popepassthru|stream_socket_server|assert|pcntl_exec/i";
define("BLACKLIST", $pattern);
define('TIME', (string)time());
define('ROOT', $_SERVER['CONTEXT_DOCUMENT_ROOT']);  # 根目录
define('WAFLOG', ROOT . '/waf_log');  # 日志目录
define('WAFUPLOAD', ROOT . '/waf_upload/');  # 文件上传目录
define('REQ', WAFLOG . '/req.log');  # 请求日志文件
define('ATTACK', WAFLOG . '/attack.log');  # 攻击请求日志文件
$http = new Http();
define('HTTP', $http);
if (!file_exists(WAFLOG)) mkdir(WAFLOG);

//http数据包
class Http
{
    public $time;
    public $uri;
    public $method;
    public $ip;
    public $headers = array();
    public $payload;

//    获取请求头

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        foreach ($_SERVER as $k => $v) {
            if (substr($k, 0, 5) === 'HTTP_') {
                $this->headers[$k] = $v;
            }
        }
        return $this->headers;
    }

    public function __construct()
    {
        $this->time = TIME;
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->payload = file_get_contents('php://input');
    }


    public function __toString()
    {
        return
            $this->time . PHP_EOL
            . date("Y-m-d H:i:s") . PHP_EOL .
            $this->method . PHP_EOL .
            $this->ip . PHP_EOL .
            $this->uri . PHP_EOL .
            print_r($this->getHeaders(), true) . PHP_EOL . PHP_EOL .
            $this->payload . PHP_EOL . PHP_EOL;
    }

}

// 日志记录
function logger($filename)
{
    file_put_contents($filename, HTTP, FILE_APPEND);
}

//请求记录
function req_log()
{
    logger(REQ);
}

//攻击记录
function attack_log()
{
    if (preg_match(BLACKLIST, HTTP, $out)) {
        logger(ATTACK);
        die('flag{' . base64_encode("高尚是高尚者的墓志铭\n卑鄙是卑鄙者的通行证") . '}');
    }
}

//文件上传记录
function file_upload_log()
{
    print_r($_FILES);
    if ($_POST['submit']) {
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
            if ($grade === '危险') die('flag{' . base64_encode('哪有什么回头路') . '}');
        }
    }
}

function main()
{
    req_log();
    attack_log();
    file_upload_log();
}

main();
?>
</pre>

