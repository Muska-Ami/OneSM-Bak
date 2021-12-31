<?php
if (!function_exists('curl_init')) {
    echo('<font color="red">需要Curl</font>, 请安装 PHP-Curl.');
    exit();
}

include './src/index.inc.php';

require './src/manager/data.php';

?>