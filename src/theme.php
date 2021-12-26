<?php
include './system.inc.php';
/*
 * 主题功能
 * 
 * 读取 theme/defult/index.osmp 的内容,并且插入 theme/defult/extra.css 内的样式
 * Time: 2021-12-25 / 
*/

//读取设置&读取主题内容
$theme = FileNr(Config('theme'));
if ($theme == null) {
    $theme = 'defult';
}
$themeopt = $theme . '/index.osmp';

//读取样式
$style = FileNr('../theme/' . $theme . '/extra.css');

//载入内容
$opt = pin('Resource@Style', $style, $themeopt);
$opt = pin('Title', Config('SiteName'), $opt);
$opt = pin('i18n@Login', I18n('Login'), $opt);
$opt = pin('Language', getLang(), $opt);
?>