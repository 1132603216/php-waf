<?php
define("ROOT", $_SERVER["CONTEXT_DOCUMENT_ROOT"]);   # 根目录
define("WAF", 'waf.php');   # 需要添加的 waf.php
define("ADDSTR", "<?php require_once('" . ROOT . "/" . WAF . "') ?>\n");
define("LOG", ROOT . '/' . 'add.waf.log');
$add_log = array();
function add($root)
{
    $resource = opendir($root);
    while ($row = readdir($resource)) {
        if ($row === '.' || $row === '..' || $row === WAF) continue;
        $filename = $root . '/' . $row;
        if (is_file($filename)) {
            if (strrchr($row, '.') === '.php') {  # 如果时php文件就添加
                $newstr = ADDSTR . file_get_contents($filename);
                $ok = file_put_contents($filename, $newstr);
                echo "$filename -> " . $ok . PHP_EOL;
                if (!$ok) {
                    $GLOBALS['add_log'][] = $filename;
                }
            }
        } else {
            add($root . '/' . $row);
        }
    }
}
add(ROOT);
if ($GLOBALS['add_log']) {
    echo "\n以下文件需要手动添加waf:\n";
    print_r($GLOBALS['add_log']);
}
unlink(__FILE__);
?>
