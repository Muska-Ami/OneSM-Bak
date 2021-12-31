<?php
/*
 * 文件加密
 * 
 * 给文件/文件夹添加密码
 * 或者读取
 * MD5+SHA1+Base64加密
 */

function FNewPwd($hash, $path) {
    JSONNewItem('../_datas/filekey.osm', $path, $hash);
}

function FWritePwd($hash, $path) {
    JSONWhiteItem('../_datas/filekey.osm', $path, $hash);
}

function FGetPwdHash($path) {
    $a = FileNr('../_datas/filekey.osm');
    $json = json_decode($a);
    return $json->$path;
}
?>