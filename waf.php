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
    $waf = "ZGVmaW5lKCdDT05GX0xPR19BVFRBQ1RfTkFNRScsICdhdHRhY3QubG9nJyk7aWYgKCFmaWxlX2V4aXN0cyhDT05GX0xPR19QQVRIKSkge21rZGlyKENPTkZfTE9HX1BBVEgpO31kYXRlX2RlZmF1bHRfdGltZXpvbmVfc2V0KCJQUkMiKTtjbGFzcyBXYWZ7cHVibGljICRpZCA9ICcnO3B1YmxpYyAkZ2V0ID0gW107cHVibGljICRib2R5ID0gW107cHVibGljICRoZWFkZXIgPSBbXTtwdWJsaWMgJGlwID0gJyc7cHVibGljICRwb3J0ID0gJyc7cHVibGljICR1cmwgPSAnJztwdWJsaWMgJG1ldGhvZCA9ICcnO3B1YmxpYyAkdGltZSA9ICcnO3B1YmxpYyAkcGF0dGVybiA9ICIvc2VsZWN0fGluc2VydHx1cGRhdGV8ZGVsZXRlfGxvYWRfZmlsZXxvdXRmaWxlfGR1bXBmaWxlIiAuICJ8Y2FsbF91c2VyX2Z1bmNfYXJyYXl8dXNvcnR8dWFzb3J0fGFycmF5X21hcHxjcmVhdGVfZnVuY3Rpb258ZmlsZV9wdXRfY29udGVudHN8ZndyaXRlfGN1cmx8c3lzdGVtfGV2YWx8YXNzZXJ0fGVjaG98Y21kIiAuICJ8cGFzc3RocnV8ZXhlY3xzeXN0ZW18Y2hyb290fHNjYW5kaXJ8Y2hncnB8Y2hvd258c2hlbGxfZXhlY3xwcm9jX29wZW58cHJvY19nZXRfc3RhdHVzfHBvcGVufGluaV9hbHRlcnxpbmlfcmVzdG9yZS9pIjtmdW5jdGlvbiBfX2NvbnN0cnVjdCgpeyR0aGlzLT5pcCA9ICRfU0VSVkVSWydSRU1PVEVfQUREUiddOyR0aGlzLT5wb3J0ID0gJF9TRVJWRVJbJ1JFTU9URV9QT1JUJ107JHRoaXMtPmdldCA9ICRfR0VUO2lmICgkX1BPU1QpIHskdGhpcy0+Ym9keSA9ICRfUE9TVDt9IGVsc2UgeyR0aGlzLT5ib2R5WzBdID0gZmlsZV9nZXRfY29udGVudHMoJ3BocDovL2lucHV0Jyk7fSR0aGlzLT5oZWFkZXIgPSAkdGhpcy0+Z2V0SGVhZGVyKCk7JHRoaXMtPnVybCA9ICRfU0VSVkVSWydSRVFVRVNUX1VSSSddOyR0aGlzLT5tZXRob2QgPSAkX1NFUlZFUlsnUkVRVUVTVF9NRVRIT0QnXTskdGhpcy0+dGltZSA9IGRhdGUoJ1ktbS1kIEg6aTpzJyk7JHRoaXMtPmlkID0gJHRoaXMtPmdldElkKCk7fWZ1bmN0aW9uIGdldElkKCl7cmV0dXJuIG1kNShyYW5kKDEwMDAwMDAsIDEwMDAwMDAwMCkpO31mdW5jdGlvbiBjaGVjaygkYXJyKXtmb3JlYWNoICgkYXJyIGFzICRrID0+ICR2KSB7aWYgKGlzX2FycmF5KCR2KSkgeyRyZXMgPSAkdGhpcy0+Y2hlY2soJHYpO2lmICghJHJlc1snY2hlY2snXSkgcmV0dXJuICRyZXM7fSBlbHNlIHtpZiAocHJlZ19tYXRjaCgkdGhpcy0+cGF0dGVybiwgJHYpKSByZXR1cm4gWydjaGVjaycgPT4gZmFsc2UsICdyZXN1bHQnID0+ICIkaz0kdiJdO319cmV0dXJuIFsnY2hlY2snID0+IHRydWVdO31mdW5jdGlvbiBnZXRIZWFkZXIoKXskaGVhZGVyID0gW107Zm9yZWFjaCAoJF9TRVJWRVIgYXMgJGsgPT4gJHYpIHtpZiAoc3RycG9zKCRrLCAnSFRUUF8nKSA9PT0gMCkgeyRoZWFkZXJbc3Vic3RyKCRrLCA1KV0gPSAkdjt9fXJldHVybiAkaGVhZGVyO31mdW5jdGlvbiBsb2coJGZpbGVuYW1lID0gJ3JlcS5sb2cnLCAkbG9nID0gZmFsc2Upe2lmICghJGxvZykgeyRsb2cgPSAkdGhpcy0+dGltZSAuIFBIUF9FT0wgLiR0aGlzLT5pcCAuICI6IiAuICR0aGlzLT5wb3J0IC4gUEhQX0VPTCAuJ21ldGhvZDogJyAuICR0aGlzLT5tZXRob2QgLiBQSFBfRU9MIC4ndXJsOiAnIC4gJHRoaXMtPnVybCAuIFBIUF9FT0wgLidnZXQ6ICcgLiBwcmludF9yKCR0aGlzLT5nZXQsIHRydWUpIC4gUEhQX0VPTCAuJ2JvZHk6ICcgLiBwcmludF9yKCR0aGlzLT5ib2R5LCB0cnVlKSAuIFBIUF9FT0wgLidoZWFkZXI6ICcgLiBwcmludF9yKCR0aGlzLT5oZWFkZXIsIHRydWUpO30kbG9nID0gJ2lkOiAnIC4gJHRoaXMtPmlkIC4gUEhQX0VPTCAuJGxvZy5QSFBfRU9MIC4gJy09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPScuUEhQX0VPTDtmaWxlX3B1dF9jb250ZW50cyhDT05GX0xPR19QQVRIIC4gJy8nIC4gJGZpbGVuYW1lLCAkbG9nLCBGSUxFX0FQUEVORCk7fWZ1bmN0aW9uIHVwbG9hZCgpe2lmICghJF9GSUxFUykgcmV0dXJuO3ByaW50X3IoJF9GSUxFUyk7Zm9yZWFjaCAoJF9GSUxFUyBhcyAkayA9PiAkZmlsZSkge2ZpbGVfcHV0X2NvbnRlbnRzKENPTkZfTE9HX1BBVEggLiAiL3skdGhpcy0+aXB9LXskdGhpcy0+aWR9LSRrIiwgZmlsZV9nZXRfY29udGVudHMoJGZpbGVbJ3RtcF9uYW1lJ10pKTtmaWxlX3B1dF9jb250ZW50cygkZmlsZVsndG1wX25hbWUnXSwgJycpO319fSR3YWYgPSBuZXcgV2FmKCk7JHdhZi0+bG9nKCk7JHJlcyA9ICR3YWYtPmNoZWNrKFskd2FmLT5nZXQsICR3YWYtPmJvZHksICR3YWYtPmhlYWRlcl0pO2lmICghJHJlc1snY2hlY2snXSkgeyR3YWYtPmxvZyhDT05GX0xPR19BVFRBQ1RfTkFNRSwkcmVzWydyZXN1bHQnXSk7fWlmKENPTkZfQ0hFQ0tfVVBMT0FEX0ZJTEUgPT09ICdUJyl7JHdhZi0+dXBsb2FkKCk7fQ==";
    $waf = '<?php ' . "define('CONF_LOG_PATH','" . CONF_LOG_PATH . "');" . "define('CONF_CHECK_UPLOAD_FILE','" . CONF_CHECK_UPLOAD_FILE . "');" . base64_decode($waf);
    file_put_contents(CONF_WAF_NAME, $waf);
}


function wafstr($wafpath)
{
    return "<?php if (!defined('WAF')) {define('WAF', true);if(file_exists('$wafpath')) require_once('$wafpath');}?>".PHP_EOL;
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
wafload();