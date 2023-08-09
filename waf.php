<?php
$log_path = dirname($_SERVER['SCRIPT_FILENAME']) . DIRECTORY_SEPARATOR . 'waflog';

/**
 * - 安装选项 ---------------------------------------------------------------------------------------
 */

define('CONF_LOG_PATH', $log_path);    # 设置日志路径
define('CONF_CHECK_UPLOAD_FILE', 'T');    # 是否处理文件上传
define('CONF_WAF_NAME', 'waf.php');    # waf文件名

/**
 * --------------------------------------------------------------------------------------------------
 */

function wafload()
{
    $waf = "PD9waHAgZGVmaW5lKCdDT05GX0xPR19QQVRIJywgJy4vd2FmbG9nJyk7ZGVmaW5lKCdDT05GX0NIRUNLX1VQTE9BRF9GSUxFJywgJ1QnKTtkZWZpbmUoJ0NPTkZfTE9HX0FUVEFDVF9OQU1FJywgJ2F0dGFjdC5sb2cnKTtpZiAoIWZpbGVfZXhpc3RzKENPTkZfTE9HX1BBVEgpKSB7bWtkaXIoQ09ORl9MT0dfUEFUSCk7fWRhdGVfZGVmYXVsdF90aW1lem9uZV9zZXQoIlBSQyIpO2NsYXNzIFdhZntwdWJsaWMgJGlkID0gJyc7cHVibGljICRnZXQgPSBbXTtwdWJsaWMgJGJvZHkgPSBbXTtwdWJsaWMgJGhlYWRlciA9IFtdO3B1YmxpYyAkaXAgPSAnJztwdWJsaWMgJHBvcnQgPSAnJztwdWJsaWMgJHVybCA9ICcnO3B1YmxpYyAkbWV0aG9kID0gJyc7cHVibGljICR0aW1lID0gJyc7cHVibGljICRwYXR0ZXJuID0gIi9zZWxlY3R8aW5zZXJ0fHVwZGF0ZXxkZWxldGV8bG9hZF9maWxlfG91dGZpbGV8ZHVtcGZpbGUiIC4gInxjYWxsX3VzZXJfZnVuY19hcnJheXx1c29ydHx1YXNvcnR8YXJyYXlfbWFwfGNyZWF0ZV9mdW5jdGlvbnxmaWxlX3B1dF9jb250ZW50c3xmd3JpdGV8Y3VybHxzeXN0ZW18ZXZhbHxhc3NlcnR8ZWNob3xjbWQiIC4gInxwYXNzdGhydXxleGVjfHN5c3RlbXxjaHJvb3R8c2NhbmRpcnxjaGdycHxjaG93bnxzaGVsbF9leGVjfHByb2Nfb3Blbnxwcm9jX2dldF9zdGF0dXN8cG9wZW58aW5pX2FsdGVyfGluaV9yZXN0b3JlL2kiO2Z1bmN0aW9uIF9fY29uc3RydWN0KCl7JHRoaXMtPmlwID0gJF9TRVJWRVJbJ1JFTU9URV9BRERSJ107JHRoaXMtPnBvcnQgPSAkX1NFUlZFUlsnUkVNT1RFX1BPUlQnXTskdGhpcy0+Z2V0ID0gJF9HRVQ7aWYgKCRfUE9TVCkgeyR0aGlzLT5ib2R5ID0gJF9QT1NUO30gZWxzZSB7JHRoaXMtPmJvZHlbMF0gPSBmaWxlX2dldF9jb250ZW50cygncGhwOi8vaW5wdXQnKTt9JHRoaXMtPmhlYWRlciA9ICR0aGlzLT5nZXRIZWFkZXIoKTskdGhpcy0+dXJsID0gJF9TRVJWRVJbJ1JFUVVFU1RfVVJJJ107JHRoaXMtPm1ldGhvZCA9ICRfU0VSVkVSWydSRVFVRVNUX01FVEhPRCddOyR0aGlzLT50aW1lID0gZGF0ZSgnWS1tLWQgSDppOnMnKTskdGhpcy0+aWQgPSAkdGhpcy0+Z2V0SWQoKTt9ZnVuY3Rpb24gZ2V0SWQoKXtyZXR1cm4gbWQ1KHJhbmQoMTAwMDAwMCwgMTAwMDAwMDAwKSk7fWZ1bmN0aW9uIGNoZWNrKCRhcnIpe2ZvcmVhY2ggKCRhcnIgYXMgJGsgPT4gJHYpIHtpZiAoaXNfYXJyYXkoJHYpKSB7JHJlcyA9ICR0aGlzLT5jaGVjaygkdik7aWYgKCEkcmVzWydjaGVjayddKSByZXR1cm4gJHJlczt9IGVsc2Uge2lmIChwcmVnX21hdGNoKCR0aGlzLT5wYXR0ZXJuLCAkdikpIHJldHVybiBbJ2NoZWNrJyA9PiBmYWxzZSwgJ3Jlc3VsdCcgPT4gIiRrPSR2Il07fX1yZXR1cm4gWydjaGVjaycgPT4gdHJ1ZV07fWZ1bmN0aW9uIGdldEhlYWRlcigpeyRoZWFkZXIgPSBbXTtmb3JlYWNoICgkX1NFUlZFUiBhcyAkayA9PiAkdikge2lmIChzdHJwb3MoJGssICdIVFRQXycpID09PSAwKSB7JGhlYWRlcltzdWJzdHIoJGssIDUpXSA9ICR2O319cmV0dXJuICRoZWFkZXI7fWZ1bmN0aW9uIGxvZygkZmlsZW5hbWUgPSAncmVxLmxvZycsICRsb2cgPSBmYWxzZSl7aWYgKCEkbG9nKSB7JGxvZyA9ICR0aGlzLT50aW1lIC4gUEhQX0VPTCAuJHRoaXMtPmlwIC4gIjoiIC4gJHRoaXMtPnBvcnQgLiBQSFBfRU9MIC4nbWV0aG9kOiAnIC4gJHRoaXMtPm1ldGhvZCAuIFBIUF9FT0wgLid1cmw6ICcgLiAkdGhpcy0+dXJsIC4gUEhQX0VPTCAuJ2dldDogJyAuIHByaW50X3IoJHRoaXMtPmdldCwgdHJ1ZSkgLiBQSFBfRU9MIC4nYm9keTogJyAuIHByaW50X3IoJHRoaXMtPmJvZHksIHRydWUpIC4gUEhQX0VPTCAuJ2hlYWRlcjogJyAuIHByaW50X3IoJHRoaXMtPmhlYWRlciwgdHJ1ZSk7fSRsb2cgPSAnaWQ6ICcgLiAkdGhpcy0+aWQgLiBQSFBfRU9MIC4kbG9nLlBIUF9FT0wgLiAnLT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09Jy5QSFBfRU9MO2ZpbGVfcHV0X2NvbnRlbnRzKENPTkZfTE9HX1BBVEggLiAnLycgLiAkZmlsZW5hbWUsICRsb2csIEZJTEVfQVBQRU5EKTt9ZnVuY3Rpb24gdXBsb2FkKCl7aWYgKCEkX0ZJTEVTKSByZXR1cm47cHJpbnRfcigkX0ZJTEVTKTtmb3JlYWNoICgkX0ZJTEVTIGFzICRrID0+ICRmaWxlKSB7ZmlsZV9wdXRfY29udGVudHMoQ09ORl9MT0dfUEFUSCAuICIveyR0aGlzLT5pcH0teyR0aGlzLT5pZH0tJGsiLCBmaWxlX2dldF9jb250ZW50cygkZmlsZVsndG1wX25hbWUnXSkpO2ZpbGVfcHV0X2NvbnRlbnRzKCRmaWxlWyd0bXBfbmFtZSddLCAnJyk7fX19JHdhZiA9IG5ldyBXYWYoKTskd2FmLT5sb2coKTskcmVzID0gJHdhZi0+Y2hlY2soWyR3YWYtPmdldCwgJHdhZi0+Ym9keSwgJHdhZi0+aGVhZGVyXSk7aWYgKCEkcmVzWydjaGVjayddKSB7JHdhZi0+bG9nKENPTkZfTE9HX0FUVEFDVF9OQU1FLCRyZXNbJ3Jlc3VsdCddKTt9aWYoQ09ORl9DSEVDS19VUExPQURfRklMRSA9PT0gJ1QnKXskd2FmLT51cGxvYWQoKTt9";
    $waf = '<?php ' . "define('CONF_LOG_PATH','" . CONF_LOG_PATH . "');" . "define('CONF_CHECK_UPLOAD_FILE','" . CONF_CHECK_UPLOAD_FILE . "');" . base64_decode($waf);
    file_put_contents(CONF_WAF_NAME, $waf);
}


function wafstr($wafpath)
{
    return "<?php if (!defined('WAF')) {define('WAF', true);if(file_exists('$wafpath')) require_once('$wafpath');}?>";
}

// 添加字符串
function addwafstr($filepath, $wafpath)
{
    echo $filepath . " " . $wafpath . PHP_EOL;
    file_put_contents($filepath, wafstr($wafpath) . file_get_contents($filepath));
}

// 要添加的字符串  
function add($root)
{
    $resource = opendir($root);
    while (false !== ($row = readdir($resource))) {
        if ($row === '.' || $row === '..' || $row === 'waf.php') continue;
        $filename = $root . '/' . $row;
        if (is_file($filename)) {
            if (strrchr($row, '.') === '.php') {  # 如果时php文件就添加
                $wafpath = '';
                $c = substr_count($filename, '/');
                for ($i = 1; $i < $c; $i++) $wafpath .= '../';
                $wafpath .= CONF_WAF_NAME;
                addwafstr($filename, $wafpath);
            }
        } else {
            add($root . '/' . $row);
        }
    }
}

add('.');
