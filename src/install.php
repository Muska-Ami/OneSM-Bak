<?php
/*
 * 安装程序
 * 
 * Tips: 谨慎修改
*/
$file = FileNr('../_datas/others/universal.osmp');

if (Config('Install') == null) {
    //消除无用内容
    $opt = pin('CaptchaJS', '', $file);
    $opt = pin('CaptchaMinJS', '', $opt);
    
    $opt = pin('Title', I18n('Install').' - OneSM', $opt);
    $html = '';
    $html .= '<form action="?inst2" method="POST" id="submit">
    <input type="text" class="intxt sitename" placeholder="'. I18n('SiteName') .'" />
    <input type="password" class="intxt password" placeholder="'. I18n('Password') .'" />
    <input type="button" onclick="Next(document.querySelector(\'.sitename\').value, document.querySelector(\'.password\').value)" />
</form>
<script src="'. Resource('js', 'sha1') .'"></script>
<script src="'. Resource('js', 'blueimp-md5') .'"></script>
<script>
    function Next(sitename, pwd) {
        var code = sha1(md5(pwd));
        document.cookie = \'asfqq32ubgb23482fb3283gb23ie2g83=\'+ code +\';\';
        document.cookie = \'adfgrwe455g6j554h5h65h45435hh54h=\'+ sitename +\';\';
        document.getElementById(\'submit\').submit();
    }
</script>
';
    $opt = pin('Nrls', $html, $opt);
}