<!--
  设置
  HTML+PHP实现，不读取外部样式(Style,Config,Password除外)
  LoadSettingAPP()：加载设置程序
  LoadSettingScript()：加载设置脚本
-->
<?php
//APP
function LoadSettingAPP() {
    
//Script
function LoadSettingScript() {
    function js($jsn) {
        return '<script src="'. Resource('js', $jsn) .'"></script>';
    }
    echo js('sortable').js('ionicons').js('feature').js('setting');
}
?>
<!DOCTYPE html>
<html lang="<?php getLang() ?>">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?php echo Config('SiteName')." - ".I18n('Config'); ?></title>
        <?php
            //读取设置的登录页面
            $lp = Config('LoginPage');
            if ($lp == null) {
                $lp = 'login';
            }
            //读取Cookie
            if($_COOKIE['afew322432122g3sgweg4sq32t32424y'] === Token('Password')) {
                $logined = true;
            } else {
                header("Location: /?{$lp}");
                exit();
            }
        ?>
        <style><?php echo FileNr('../_datas/main.css'); ?></style>
    </head>
    <body>
        <nav>
            <h2><?php echo Config('SiteName'); ?></h2>
        </nav>
        <?php
        if ($logined == true) {
            LoadSettingAPP();
        } else {
            echo I18n('NoLogin');
        }
        ?>
    </body>
    <?php
    if ($logined == true) {
        LoadSettingScript();
    }
    ?>
</html>
