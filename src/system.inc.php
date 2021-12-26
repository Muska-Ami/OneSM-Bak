<?php
function FileNr($file) {
    $a = fopen($file, 'rb+');
    $b = fread($a, filesize($file));
    return $b;
    fclose($a);
}

function Config($configname) {
    $a = FileNr('../_datas/config.osm');
    $json = json_decode($a);
    return $json->$configname;
}

function getLang() {
    $lg = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    $a = substr($lg,0,strrpos($lg,","));
    if ($a == null) {
        $a = substr($lg,0,strrpos($lg,";"));
    }
    if ($a == 'zh-CN') {
        $lang = 'zh-CN';
    } else if ($a == 'zh-TW') {
        $lang = 'zh-TW';
    } else if ($a == 'zh') {
        $lang = 'zh-CN';
    } else if ($a == 'en') {
        $lang = 'en';
    } else if ($a == 'jp') {
        $lang = 'jp';
    } else {
        $lang = 'en';
    }
    return $lang;
}

function I18n($itemid) {
    $a = FileNr('../_datas/i18n.osm');
    $json = json_decode($a);
    return $json->getLang()->$itemid;
}

function pin($itemid, $content, $from) {
    return str_replace('!#'.$itemid.'#!', $content, $from);
}

function Token($tokenname) {
    $a = FileNr('../_datas/token.osm');
    $json = json_decode($a);
    return $json->$tokenname;
}

function JSONNewItem($jsonfile, $item, $put) {
    $a = FileNr($jsonfile);
    $b = array($item => $put);
    $codes = substr_replace($a, json_encode($b) . '}', -1);
    file_put_contents($jsonfile, $codes);
}

function curl($url, $UA = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36 Edg/96.0.1054.53', $refer = '', $timeout = 10) {
	$header[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";
    $header[] = "Accept-Encoding: gzip, deflate, sdch, br";
    $header[] = "Accept-Language: zh-CN,zh;q=0.9";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_USERAGENT, $UA);
    curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_REFERER, $refer);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

function Resource($type, $resource) {
    $code = json_decode(FileNr('../_dats/resource.osm'));
    return $code->$type->$resource;
}
?>
