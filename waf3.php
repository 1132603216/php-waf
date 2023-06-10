<?php
//捕获请求   记录所有请求信息
error_reporting(0);
define("FILENAME","req.log");

date_default_timezone_set("PRC");
date("Y/m/d H:i:s");


$data = file_get_contents("php://input");
$segment = "====================================================";
$content = date("Y/m/d H:i:s")."\n".$data."\n".print_r($_FILES, true)."\n".$segment."\n";
file_put_contents(FILENAME, $content,FILE_APPEND);
