<?php
// 基本设置
header("content-type: text/html;charset=utf-8");
session_start();

//获取毫秒级时间戳
function getTimestamp()
{
    list($usec, $sec) = explode(" ", microtime());
    return ($sec . floor($usec * 1000));
}

define("TIME", getTimestamp());  # 时间  \/\*|\*|\.\.\/|\.\/|
//$pattern = "/select|insert|update|delete|and|union|into|load_file|outfile|dumpfile|sub|hex";
$pattern = "|file_put_contents|fwrite|curl|system|eval|assert|echo|cmd";  // .= 运算
//$pattern .= "|passthru|exec|system|chroot|scandir|chgrp|chown|shell_exec|proc_open|proc_get_status|popen|ini_alter|ini_restore/i";
$pattern = "/select\b|insert\b|update\b|drop\b|delete\b|dumpfile\b|outfile\b|load_file|rename\b|floor\(|extractvalue|updatexml|name_const|multipoint\(/i";
define("BLACKLIST", $pattern);

$payload = file_get_contents("php://input");  # playload
define("ROOT", $_SERVER['CONTEXT_DOCUMENT_ROOT']); // 网站根目录
define("LOG", ROOT . "/wlb_log"); // 日志目录
define('REQUESTSLOG', LOG . '/HLH.HTTP.log');    // 请求日志
define('ATTACKLOG', LOG . '/HLH.ATTACK.log');   // 请求拦截日志
define('WAFDIR', ROOT . "/waf/");               // 文件上传副本

//创建日志目录
if (!file_exists(LOG)) mkdir(LOG);
//获取请求头
function getHeaders()
{
    $headers = array();
    foreach ($_SERVER as $key => $v) {
        if (substr($key, 0, 5) === 'HTTP_')
            $headers[$key] = $v;
    }

    $s = "";
    foreach ($headers as $k => $v) {
        $s .= $k . ": " . $v . PHP_EOL;
    }
    return $s;
}

//数据包
class HttpPackage
{
    public $headers;
    public $payload;
    public $uri;
    public $ip;

    public function __construct($headers, $payload)
    {
        $this->headers = $headers;
        $this->payload = $payload;
        $this->ip = $_SERVER["REMOTE_ADDR"];
        $this->uri = $_SERVER["REQUEST_URI"];
    }

//    获取数据包
    public function __toString()
    {
        return
            TIME . "  " .   # 时间戳
            date("Y-m-d H:i:s", TIME / 1000) . PHP_EOL .    # 时间
            $this->ip . PHP_EOL .
            $_SERVER["REQUEST_METHOD"] . "  " . urldecode($this->uri) . PHP_EOL .  # 请求方式 uri
            $this->headers . PHP_EOL .       # 请求头
            $this->payload . PHP_EOL . PHP_EOL;     # payload
    }
}

$http = new HttpPackage(print_r(getHeaders(), true), $payload);
define("HTTP", $http);

//请求记录
function requestsLogger($filename = REQUESTSLOG)
{
    file_put_contents($filename, HTTP, FILE_APPEND);
}

//攻击拦截
function attackIntercept($filename = ATTACKLOG)
{
    if (preg_match(BLACKLIST, HTTP)) {
        requestsLogger($filename);  # 记录
//        有攻击行为退出
        die("flag{" . base64_encode("小伙子想的有点多") . "}");
    }
}

//文件上传拦截
function uploadIntercept()
{
    if (!$_FILES) return;
//    获取文件后缀
    $filename = $_FILES['file']['name'];
    $suffix = strrchr($filename, '.');
//    生成新文件名
    echo TIME . $suffix;
    $_FILES['file']['name'] = TIME . $suffix;
    $tmp = file_get_contents($_FILES['file']['tmp_name']);
    $data = "文件名: " . $filename . PHP_EOL . $tmp;
    if (!file_exists(WAFDIR)) mkdir(WAFDIR);
//    获取文件内容保存为txt
    file_put_contents(WAFDIR . $_FILES['file']['name'], $data, FILE_APPEND);
//    讲上传文件的内容替换
    if (!file_put_contents($_FILES["file"]["tmp_name"], date("Y/m/d H:i:s", TIME))) {
//        如果修改内容不成功则修改后缀
        $_FILES["file"]["name"] = $_FILES["file"]["name"] . "-.txt";
    }
}


//ui
class UI
{
    public $h = "hello";

    public function show()
    {
        if (file_exists(REQUESTSLOG)) {
            $file_content = file_get_contents(REQUESTSLOG);
            preg_match_all("/(\d{13}).*(\d{4}-\d{2}-\d{2}).*(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}).*\n(.*)\n/Us", $file_content, $out);

            $tr = "";
            for ($i = 0; $i < count($out[0]); $i++) {
                $id = print_r($out[1][$i],true);
                $date = print_r($out[2][$i],true);
                $ip = print_r($out[3][$i],true);
                $uri = print_r($out[4][$i],true);
                $tr .= "<tr>
            <td>$id</td>
            <td>$date</td>
            <td>$ip</td>
            <td>$uri</td>
        </tr>";
            }
        }
        if ($_SERVER['SCRIPT_NAME'] !== '/waf.php') return;
        echo <<<EOF
<html>
<head><title>管理面板</title>
    <meta charset='utf8'>
    <style>
        header {
            height: 30px;
            line-height: 30px;
            background-color: rgb(130, 236, 118, .5);
            font-size: 30px;
            padding: 20px;
            margin-bottom: 20px;
        }

        table {
            color: #424646;
            border-collapse: collapse;
            width: 45%;
            min-width: 600px;
            text-align: center;
            position: absolute;
            right: 20px;
            margin-left: 20px;
            background-color: rgba(148, 196, 148, 0.5);
        }

        th {
            background-color: rgba(86, 234, 68, 0.5);
        }
        
        th, td{
        min-width: 125px;
        }

        tbody tr:nth-child(2n) {
            background-color: rgb(130, 236, 118, .5);
        }

        input {
            line-height: 40px;
            width: 50%;
            color: #fff;
            font-size: 20px;
            outline: none;
            border: none;
            height: 40px;
            padding-left: 10px;
            background-color: rgba(0, 0, 0, 0.7);
        }

        #container {
            background-color: #1a1e21;
            height: 500px;
            width: 50%;
            margin-top: 10px;
            background-color: rgba(0, 0, 0, 0.7);
        }

        #result {
            height: 100%;
            overflow: auto;
            padding: 10px;
            color: #fff;
        }
    </style>
</head>
<body>
<header>管理面板</header>
<main>
    <table>
        <thead>
        <tr>
            <th>id</th>
            <th>时间</th>
            <th>ip</th>
            <th>uri</th>
        </tr>
        </thead>
<!--        7-->
        <tbody>
EOF
            . $tr .
            <<<EOF
        </tbody>
    </table>
    <input autocomplete="off" placeholder="ls" autofocus>
    <div id="container">
        <div id="result">
        </div>
    </div>
    <script>
        setTimeout(()=>{
            location.reload(true)
        },10000)
        input = document.querySelector("input")
        result = document.querySelector("#result")
        input.onkeydown = function (even){
            if(even.keyCode === 13){
                result.innerText = "未开放"
            }
        }
    </script>
</main>
</body>
</html>
EOF;

    }

}

//总启动
function main()
{
//    7f04a3978a2c0bf4fcc394da85c3d916
    if ($_SESSION['wlb'] === md5("彪彪彪")) {
        $ui = new UI();
        $ui->show();
    } else {
        if ($_GET['wlb'] === md5("彪彪彪")) {
            $_SESSION['wlb'] = md5("彪彪彪");
            echo "<script>location.href='./waf.php'</script>";
        } else {
            requestsLogger();  # 请求记录
            attackIntercept();  # 关键字记录
            uploadIntercept();  # 文件上传记录
        }
    }
}

main();
?>


