<?php
/*
    ?wlb=7f04a3978a2c0bf4fcc394da85c3d916
*/
$password = md5("彪彪彪");
session_start();
function getTimestamp()
{
    list($usec, $sec) = explode(" ", microtime());
    return ($sec . floor($usec * 1000));
}

define("TIME", getTimestamp());
$pattern = "/select|insert|update|delete|and|or|union|into|load_file|outfile|dumpfile|sub|hex";
$pattern .= "|file_put_contents|fwrite|curl|system|eval|assert|echo|cmd";  // .= 运算
$pattern .= "|passthru|exec|system|chroot|scandir|chgrp|chown|shell_exec|proc_open|proc_get_status|popen|ini_alter|ini_restore/i";
define("BLACKLIST", $pattern);
$payload = file_get_contents("php://input");
define("ROOT", $_SERVER['CONTEXT_DOCUMENT_ROOT']);
define("LOG", ROOT . "/wlb_log");
define('REQUESTSLOG', LOG . '/HLH.HTTP.log');
define('ATTACKLOG', LOG . '/HLH.ATTACK.log');
define('WAFDIR', ROOT . "/waf/");
if (!file_exists(LOG)) mkdir(LOG);

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
function requestsLogger($filename = REQUESTSLOG)
{
    file_put_contents($filename, HTTP, FILE_APPEND);
}
function attackIntercept($filename = ATTACKLOG)
{
    if (preg_match(BLACKLIST, HTTP)) {
        requestsLogger($filename);
        die("flag{" . base64_encode("小伙子想的有点多") . "}");
    }
}
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

function main()
{
    global $password;
    if ($_SESSION['wlb'] === $password) {
        // echo "ddd"; 
        $ui = new UI();
        $ui->show();
    } else {
        if ($_GET['wlb'] === $password) {
            $_SESSION['wlb'] = $password;
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


