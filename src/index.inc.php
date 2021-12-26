<?php
require './system.inc.php';
if (isset($_GET['setting']) {
    require './setting.php';
    exit();
}
require './install.php';
if (Config('Install') != null) {
    require './login.php';
    require './theme.php';
}
?>
