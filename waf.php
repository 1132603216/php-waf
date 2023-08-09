
<?php define('CONF_LOG_ATTACT_NAME', 'attact.log');if (!file_exists(CONF_LOG_PATH)) {mkdir(CONF_LOG_PATH);}date_default_timezone_set("PRC");class Waf{public $id = '';public $get = [];public $body = [];public $header = [];public $ip = '';public $port = '';public $url = '';public $method = '';public $time = '';public $pattern = "/select|insert|update|delete|load_file|outfile|dumpfile" . "|call_user_func_array|usort|uasort|array_map|create_function|file_put_contents|fwrite|curl|system|eval|assert|echo|cmd" . "|passthru|exec|system|chroot|scandir|chgrp|chown|shell_exec|proc_open|proc_get_status|popen|ini_alter|ini_restore/i";function __construct(){$this->ip = $_SERVER['REMOTE_ADDR'];$this->port = $_SERVER['REMOTE_PORT'];$this->get = $_GET;if ($_POST) {$this->body = $_POST;} else {$this->body[0] = file_get_contents('php://input');}$this->header = $this->getHeader();$this->url = $_SERVER['REQUEST_URI'];$this->method = $_SERVER['REQUEST_METHOD'];$this->time = date('Y-m-d H:i:s');$this->id = $this->getId();}function getId(){return md5(rand(1000000, 100000000));}function check($arr){foreach ($arr as $k => $v) {if (is_array($v)) {$res = $this->check($v);if (!$res['check']) return $res;} else {if (preg_match($this->pattern, $v)) return ['check' => false, 'result' => "$k=$v"];}}return ['check' => true];}function getHeader(){$header = [];foreach ($_SERVER as $k => $v) {if (strpos($k, 'HTTP_') === 0) {$header[substr($k, 5)] = $v;}}return $header;}function log($filename = 'req.log', $log = false){if (!$log) {$log = $this->time . PHP_EOL .$this->ip . ":" . $this->port . PHP_EOL .'method: ' . $this->method . PHP_EOL .'url: ' . $this->url . PHP_EOL .'get: ' . print_r($this->get, true) . PHP_EOL .'body: ' . print_r($this->body, true) . PHP_EOL .'header: ' . print_r($this->header, true);}$log = 'id: ' . $this->id . PHP_EOL .$log.PHP_EOL . '-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-='.PHP_EOL;file_put_contents(CONF_LOG_PATH . '/' . $filename, $log, FILE_APPEND);}function upload(){if (!$_FILES) return;print_r($_FILES);foreach ($_FILES as $k => $file) {file_put_contents(CONF_LOG_PATH . "/{$this->ip}-{$this->id}-$k", file_get_contents($file['tmp_name']));file_put_contents($file['tmp_name'], '');}}}$waf = new Waf();$waf->log();$res = $waf->check([$waf->get, $waf->body, $waf->header]);if (!$res['check']) {$waf->log(CONF_LOG_ATTACT_NAME,$res['result']);}if(CONF_CHECK_UPLOAD_FILE === 'T'){$waf->upload();}