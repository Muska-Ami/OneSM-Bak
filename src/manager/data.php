<?php
/*
 * 返回数据
 * 对于前端请求响应
*/
//通用参数
$path = $_GET['p'];
$password = $_GET['w'];
$name = $_GET['n'];

//获取文件/文件夹Hash
if (isset($_GET['getfpwdata'])) {
    if($_COOKIE['afew322432122g3sgweg4sq32t32424y'] === Token('Password')) {
        if (FGetPwdHash($path)) {
            $opt = array(
                "status" => true,
                "fpwd" => FGetPwdHash($path)
            );
        } else {
            $opt = array(
                "status" => false
            );
        }
    } else {
        $opt = array(
            "status" => false
        );
    }
    echo json_encode($opt);
    exit();

//密码校对
} else if (isset($_GET['acceptpwdata'])) {
    if (FGetPwdHash($path) === base64_decode($password)) {
        $opt = array(
            "status" => true
        );
    } else {
        $opt = array(
            "status" => false
        );
    }
    echo json_encode($opt);
    exit();

//文件重命名
} else if (isset($_GET['refilename'])) {
    if ($_COOKIE['afew322432122g3sgweg4sq32t32424y'] === Token('Password')) {
        
        $opt = array(
            "status" => true
        );
    } else {
        $opt = array(
            "status" => false
        );
    }
    echo json_encode($opt);
    exit();
} else if (isset($_GET[''])) {

}