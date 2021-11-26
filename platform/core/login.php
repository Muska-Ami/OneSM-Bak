<?php
function adminform($name = '', $pass = '', $storage = '', $path = '')
{
    $title=getConfig('diskname');
    if ($title==null) {
        $title='OneSM';
    }
    if(getConfig('hcaptcha')==true){
        $hchaiframe='
        <form id="hcaptcha-form" method="POST">
            <div class="">
                <div id="hcaptcha" class="h-captcha" data-sitekey="'. getConfig('hcaptcha-key') .'" data-callback="onSuccess" data-expired-callback="onExpire"></div>
            </div>
        </form>
';
        $hchajs='<script src="https://js.hcaptcha.com/1/api.js" type="text/javascript" async defer></script>';
        $hchajsstart='
        var onSuccess = function(response) {
            var errorDivs = document.getElementsByClassName("hcaptcha-error");
                if (errorDivs.length) {
                    errorDivs[0].className = "";
                }
                var errorMsgs = document.getElementsByClassName("hcaptcha-error-message");
                if (errorMsgs.length) {
                    errorMsgs[0].parentNode.removeChild(errorMsgs[0]);
                }
            var logEl = document.querySelector(".login-info");
';
        $hchajsend='
        }
    }
';
    }
    $html = '<html>
    <head>
        <title>'. $title .' - '. geti18n('AdminLogin') . '</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        '.$hchajs.'
        <style>
            .nav-title {
                postion: fixed;
                font-size: 30px;
                font-weight: bold;
                margin-left: 25px;
                margin-top: 10px;
            }
            #language {
                background-color: #00000000;
                border: none;
                color: rgb(255, 255, 255);
                float: right;
                margin-top: -33px;
                margin-right: 15px;
                padding: 5px;
                outline: none;
            }
            a{
                color: #1a1a1a;
                cursor: pointer;
                text-decoration: none;
            }
        </style>
    </head>';
    if ($name=='admin'&&$pass!='') {
        $html .= '
        <!--<meta http-equiv="refresh" content="3;URL=' . $path . '">-->
    <body>
        ' . geti18n('LoginSuccess') . '
        <script>
            localStorage.setItem("admin", "' . $storage . '");
            var url = location.href;
            var search = location.search;
            url = url.substr(0, url.length-search.length);
            if (search.indexOf("preview")>0) url += "?preview";
            location = url;
        </script>
    </body>
</html>';
        $statusCode = 201;
        date_default_timezone_set('UTC');
        $_SERVER['Set-Cookie'] = $name . '=' . $pass . '; path=' . $_SERVER['base_path'] . '; expires=' . date(DATE_COOKIE, strtotime('+7day'));
        return output($html, $statusCode);
    }
    $statusCode = 401;
    $html .= '
<body>
    <nav class="header-nav">
        <div class="nav-title"><a href="/" style="color: white;">'. $title .'</a></div>
    </nav>
    <div class="login-border">
    <h2>' . geti18n('InputPassword') . '</h2>
    ' . $name . '
    <div class="login-info smsg" aria-live="polite"></div>
    <form action="" method="post" onsubmit="return gologin(this);">
        <div>
            <input id="password1" name="password1" type="password" class="input-tepwd" />
'.$hchaiframe.'
            <input type="submit" value="' . geti18n('Login') . '" class="input-btn" />
            <input name="timestamp" type="hidden" />
        </div>
    </form>
    </div>
</body>';
    $html .= '
<script>
function gologin(f) {
'.$hchajsstart.'
        document.getElementById("password1").focus();
            if (f.password1.value=="") return false;
            try {
                timestamp = new Date().getTime() + "";
                timestamp = timestamp.substr(0, timestamp.length-3);
                f.timestamp.value = timestamp;
                f.password1.value = sha1(timestamp + "" + f.password1.value);
                logEl.innerHTML="登录成功，即将跳转";
                return true;
            } catch {
                document.getElementByClassName(\'login-info\').innerHTML="sha1.js 未加载，请重试";
                return false;
            }
'.$hchajsend.'
}
</script>
<script src="https://cdn.bootcss.com/js-sha1/0.6.0/sha1.min.js"></script>';
    $html .= '</html>';
    return output($html, $statusCode);
}
?>