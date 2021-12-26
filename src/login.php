<?php
/*
 * 登录
 * 
 * 用户登录功能
 * Time: 2021-12-25 / 
*/

//读取设置的登录页面
$lp = Config('LoginPage');
if ($lp == null) {
    $lp = 'login';
}

//判断是否访问
if (isset($_GET[$lp])) {
    $style = FileNr('../_datas/others/login.osmc');
    $login = FileNr('../_datas/others/login.osmp');
    //载入内容
    $opt = pin('Resource@Style', $style, $login);
    $opt = pin('Title', Config('SiteName'), $opt);
    $opt = pin('Language', getLang(), $opt);
}
?>