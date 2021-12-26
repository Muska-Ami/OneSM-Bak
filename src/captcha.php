<?php
/*
 * Captcha人机验证功能
 *
 * 支持hCaptcha，ReCAPTCHA
 * Time: 2021-12-25 /
*/
function Captcha() {
    $file = FileNr('../_datas/others/login.osmp');

    if (isset($_GET['CaptchaVerify'])) {
        if (Config('CaptchaType') == 'hcaptcha') {
            $data = curl('https://hcaptcha.com/siteverify?response='. $_GET['key'] .'&secret='. Token('hCaptchaSecretKey'));
            $hjson = json_decode($data);
            if ($hjson->success == true) {
                echo json_encode(array(
                    "status"=> true
                ));
            } else {
                echo json_encode(array(
                    "status"=> false
                ));
            }
        }
        exit();
    }
    if (Config('Captcha') == true) {
        if(Config('CaptchaType') == 'hcaptcha') {
            //hCaptcha
            $html = '<div class="h-captcha" data-sitekey="'. Token('hCaptchaSiteKey') .'" data-callback="login"></div>';
            $copt = pin('Captcha', $html, $file);
            $minjs = '<script src="https://js.hcaptcha.com/1/api.js" async defer></script>';
            $copt = pin('CaptchaMinJS', $minjs, $copt);
            $js = '<script>
    function login() {
        function tk(token) {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "/?CaptchaVerify&key=" + token, true);
            xhr.setRequestHeader(\'content-type\', \'application/json, */*\');
            xhr.onreadystatechange = function () {
                var data = JSON.parse(xhr.responseText);
                if (data.status = false) {
                    document.querySelector(\'msg-container\').innerHTML = '. I18n('CaptchaNoPassMsg') .';
                    document.querySelector(\'msg-container\').style.display = \'\';
                } else if (data.status = true) {
                    document.cookie = \'asfqq32ubgb23482fb3283gb23ie2g83='. Token('PwdMd5') .'\';
                    document.querySelector(\'msg-container\').innerHTML = '. I18n('LoginSuccessMsg') .';
                    document.querySelector(\'msg-container\').style.display = \'\';
                }
            }
            xhr.onerror = function () {
                document.querySelector(\'msg-container\').innerHTML = '. I18n('LoginLinkServerError') .';
            }
        }
    }
</script>';
            $copt = pin('CaptchaJS', $js, $copt);
        } else {
            pin('CaptchaMinJS', '', $file);
            pin('CaptchaJS', '', $file);
        }
    }
}
?>
