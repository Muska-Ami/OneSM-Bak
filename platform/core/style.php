<?php
function st($t) {
    return '<style>'.$t.'</style>';
}
$styleuniversity='
    .input-btn {
        border: none;
        border-radius: 3px;
        padding: 5px;
    }
    .input-tepwd {
        background-color: #00000000;
        border-top: none;
        border-left: none;
        border-right: none;
        outline: none;
        border-bottom: 2px solid #5600e0;
        padding: 5px;
    }
    .input-options {
        border: 1px solid black;
        padding: 5px;
        background-color: #00000000;
    }
    .header-nav {
        background-color: #5600e0;
        position: relative;
        margin-top: -15px;
        margin-left: -10px;
        width: 101.45%;
        height: 60px;
        color: white;
    }
';
$styleadmin='
    .login-border {
        border: 1px;
        padding: 15px;
        border-radius: 3px;
        text-aglin: center;
    }
    .login-info {
        font-size: smail;
    }
';
$stylepanel='
    a {
        text-decoration: none;
        color: #47374e;
    }
';

if (isset($_GET['WaitFunction'])) {
} else {
    echo st($styleuniversity);
    echo st($styleadmin);
    if(isset($_GET['settings'])) {
        echo st($stylepanel);
    }
    echo '
<span style="position: fixed;background-color: white; bottom: 0px;right: 0px;padding: 5px;border: gray;z-index: 21254674651576237623576;">Powered by <a href="https://onesm.skimino.cf" style="color: rgb(238, 80, 80);text-decoration: none;">OneSM</a></span>
';
}
?>