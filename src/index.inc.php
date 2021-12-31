<?php
require './system.inc.php';
require './install.php';
if (Config('Install') != null) {
    if (isset($_GET['setting']) {
        require './setting.php';
        exit();
    }
    require './login.php';
    require './theme.php';
}
?>
