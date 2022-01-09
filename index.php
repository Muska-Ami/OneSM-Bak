<?php
//error_reporting(E_ALL & ~E_NOTICE);
error_reporting(0);
include './vendor/autoload.php';
include './src/i18n.php';
include './src/common.php';

//echo '<pre>'. json_encode($_SERVER, JSON_PRETTY_PRINT).'</pre>';
//echo '<pre>'. json_encode($_ENV, JSON_PRETTY_PRINT).'</pre>';
if (isset($_SERVER['DOCUMENT_ROOT'])&&$_SERVER['DOCUMENT_ROOT']==='/var/task/user') {
    if (getenv('ONESM_CONFIG_SAVE')=='env') include './src/platform/Vercel_env.php';
    else include './src/platform/Vercel.php';
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
    include './src/platform/Normal.php';
    if (!function_exists('curl_init')) {
        return message('<font color="red">Need curl</font>, please install php-curl.', 'Error', 500);
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