<?php
//error_reporting(E_ALL & ~E_NOTICE);
error_reporting(0);
include 'vendor/autoload.php';
include 'conststr.php';
include 'common.php';

//echo '<pre>'. json_encode($_SERVER, JSON_PRETTY_PRINT).'</pre>';
//echo '<pre>'. json_encode($_ENV, JSON_PRETTY_PRINT).'</pre>';
if (isset($_SERVER['DOCUMENT_ROOT'])&&$_SERVER['DOCUMENT_ROOT']==='/var/task/user') {
    if (getenv('OneSM_CONFIG_SAVE')=='env') include 'platform/Vercel_env.php';
    else include 'platform/Vercel.php';
    $path = getpath();
    //echo 'path:'. $path;
    $_GET = getGET();
    //echo '<pre>'. json_encode($_GET, JSON_PRETTY_PRINT).'</pre>';
    $re = main($path);
    $sendHeaders = array();
    foreach ($re['headers'] as $headerName => $headerVal) {
        header($headerName . ': ' . $headerVal, true);
    }
    http_response_code($re['statusCode']);
    if ($re['isBase64Encoded']) echo base64_decode($re['body']);
    else echo $re['body'];
} else {
    include 'platform/Normal.php';
    if (!function_exists('curl_init')) {
        return message('<font color="red">需要Curl组件，请安装Curl</font>', 'Error', 500);
    }
    $path = getpath();
    //echo 'path:'. $path;
    $_GET = getGET();
    //echo '<pre>'. json_encode($_GET, JSON_PRETTY_PRINT).'</pre>';

    $re = main($path);
    $sendHeaders = array();
    foreach ($re['headers'] as $headerName => $headerVal) {
        header($headerName . ': ' . $headerVal, true);
    }
    http_response_code($re['statusCode']);
    if ($re['isBase64Encoded']) echo base64_decode($re['body']);
    else echo $re['body'];
}
?>