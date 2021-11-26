<?php
if ($_GET['settings']==='platform') {
    $frame .= '
<form name="common" action="" method="post">
    <input name="_admin" type="hidden" value="">';
foreach ($EnvConfigs as $key => $val) if (isCommonEnv($key) && isShowedEnv($key)) {
    $frame .= '
    <tr>
        <td><label>' . $key . '</label></td>
        <td width=100%>';
    if ($key=='timezone') {
        $frame .= '
            <select name="' . $key .'">';
        foreach (array_keys($timezones) as $zone) {
            $frame .= '
                <option value="'.$zone.'" '.($zone==getConfig($key)?'selected="selected"':'').'>'.$zone.'</option>';
        }
        $frame .= '
            </select>
            ' . geti18n('EnvironmentsDescription')[$key];
    } elseif ($key=='theme') {
        $theme_arr = scandir(__DIR__ . $slash . 'theme');
        $frame .= '
            <select name="' . $key .'">
                <option value=""></option>';
        foreach ($theme_arr as $v1) {
            if ($v1!='.' && $v1!='..') $frame .= '
                <option value="'.$v1.'" '.($v1==getConfig($key)?'selected="selected"':'').'>'.$v1.'</option>';
        }
        $frame .= '
            </select>
            ' . geti18n('EnvironmentsDescription')[$key];
    } /*elseif ($key=='domain_path') {
        $tmp = getConfig($key);
        $domain_path = '';
        foreach ($tmp as $k1 => $v1) {
            $domain_path .= $k1 . ':' . $v1 . '|';
        }
        $domain_path = substr($domain_path, 0, -1);
        $frame .= '
    <tr>
        <td><label>' . $key . '</label></td>
        <td width=100%><input type="text" class="input-tepwd" name="' . $key .'" value="' . $domain_path . '" placeholder="' . geti18n('EnvironmentsDescription')[$key] . '" style="width:100%"></td>
    </tr>';
    }*/ else $frame .= '
            <input type="text" class="input-tepwd" name="' . $key . '" value="' . htmlspecialchars(getConfig($key)) . '" placeholder="' . geti18n('EnvironmentsDescription')[$key] . '" style="width:100%">';
    $frame .= '
        </td>
    </tr>';
}
$frame .= '
    <tr><td><input type="submit" name="submit1" value="' . geti18n('Setup') . '" class="input-btn"></td><td></td></tr>
</form>
</table><br>';
} elseif (isset($_GET['disktag'])&&$_GET['disktag']!==true&&in_array($_GET['disktag'], $disktags)) {
        $disktag = $_GET['disktag'];
        $disk_tmp = null;
        $diskok = driveisfine($disktag, $disk_tmp);
        $frame .= '
<table width=100%>
    <tr>
        <td>
            <form action="" method="post" style="margin: 0" onsubmit="return renametag(this);">
                <input type="hidden" name="disktag_rename" value="' . $disktag . '">
                <input name="_admin" type="hidden" value="">
                <input type="text" class="input-tepwd" name="disktag_newname" value="' . $disktag . '" placeholder="' . geti18n('EnvironmentsDescription')['disktag'] . '">
                <input type="submit" name="submit1" value="' . geti18n('RenameDisk') . '" class="input-btn">
            </form>
        </td>
    </tr>
</table><br>
<table>
<tr>
    <td>
        <form action="" method="post" style="margin: 0" onsubmit="return deldiskconfirm(this);">
            <input type="hidden" name="disktag_del" value="' . $disktag . '">
            <input name="_admin" type="hidden" value="">
            <input type="submit" name="submit1" value="' . geti18n('DelDisk') . '" class="input-btn">
        </form>
    </td>
    <td>
        <form action="" method="post" style="margin: 0" onsubmit="return cpdiskconfirm(this);">
            <input type="hidden" name="disktag_copy" value="' . $disktag . '">
            <input name="_admin" type="hidden" value="">
            <input type="submit" name="submit1" value="' . geti18n('CopyDisk') . '" class="input-btn">
        </form>
    </td>
</tr>
</table>
<table border=1 width=100%>
    <tr>
        <td>Driver</td>
        <td>' . getConfig('Driver', $disktag);
        if ($diskok) $frame .= ' <a href="?AddDisk=' . get_class($disk_tmp) . '&disktag=' . $disktag . '&SelectDrive">' . geti18n('ChangeDrivetype') . '</a>';
        $frame .= '</td>
    </tr>';
        if ($diskok) {
            $frame .= '
    <tr>
        <td>diskSpace</td><td>' . $disk_tmp->getDiskSpace() . '</td>
    </tr>';
            foreach (extendShow_diskenv($disk_tmp) as $ext_env) {
                $frame .= '
    <tr>
        <td>' . $ext_env . '</td>
        <td>' . getConfig($ext_env, $disktag) . '</td>
    </tr>';
            }

            $frame .= '
<form name="' . $disktag . '" action="" method="post">
    <input name="_admin" type="hidden" value="">
    <input type="hidden" name="disk" value="' . $disktag . '">';
            foreach ($EnvConfigs as $key => $val) if (isInnerEnv($key) && isShowedEnv($key)) {
                $frame .= '
    <tr>
        <td><label>' . $key . '</label></td>
        <td width=100%><input type="text" class="input-tepwd" name="' . $key . '" value="' . getConfig($key, $disktag) . '" placeholder="' . geti18n('EnvironmentsDescription')[$key] . '" style="width:100%"></td>
    </tr>';
            }
            $frame .= '
    <tr><td></td><td><input type="submit" name="submit1" value="' . geti18n('Setup') . '" class="input-btn"></td></tr>
</form>';
        } else {
            $frame .= '
<tr>
    <td colspan="2">' . ($disk_tmp->error['body']?$disk_tmp->error['stat'] . '<br>' . $disk_tmp->error['body']:'Add this disk again.') . '</td>
</tr>';
        }
        $frame .= '
</table>

<script>
    function deldiskconfirm(t) {
        var msg="' . geti18n('Delete') . ' ??";
        if (confirm(msg)==true) return true;
        else return false;
    }
    function cpdiskconfirm(t) {
        var msg="' . geti18n('Copy') . ' ??";
        if (confirm(msg)==true) return true;
        //else 
        return false;
    }
    function renametag(t) {
        if (t.disktag_newname.value==\'\') {
            alert(\'' . geti18n('DiskTag') . '\');
            return false;
        }
        if (t.disktag_newname.value==t.disktag_rename.value) {
            return false;
        }
        envs = [' . $envs . '];
        if (envs.indexOf(t.disktag_newname.value)>-1) {
            alert(\'Do not input ' . $envs . '\');
            return false;
        }
        var reg = /^[a-zA-Z]([_a-zA-Z0-9]{1,20})$/;
        if (!reg.test(t.disktag_newname.value)) {
            alert(\'' . geti18n('TagFormatAlert') . '\');
            return false;
        }
        return true;
    }
</script>';
    } else {
        //$_GET['disktag'] = '';
        $Driver_arr = scandir(__DIR__ . $slash . 'disk');
        if (count($disktags)>1) {
            $frame .= '
<script src="//cdn.bootcss.com/Sortable/1.8.3/Sortable.js"></script>
<style>
    .sortable-ghost {
        opacity: 0.4;
        background-color: #1748ce;
    }

    #sortdisks td {
        cursor: move;
    }
</style>
<table border=1>
    <form id="sortdisks_form" action="" method="post" style="margin: 0" onsubmit="return dragsort(this);" class="input-btn">
    <tr id="sortdisks">
        <input type="hidden" name="disktag_sort" value="">';
            $num = 0;
            foreach ($disktags as $disktag) {
                if ($disktag!='') {
                    $num++;
                    $frame .= '
        <td>' . $disktag . '</td>';
                }
            }
            $frame .= '
        <input name="_admin" type="hidden" value="">
    </tr>
    <tr><td colspan="' . $num . '">' . geti18n('DragSort') . '<input type="submit" name="submit1" value="' . geti18n('SubmitSortdisks') . '" class="input-btn"></td></tr>
    </form>
</table>
<script>
    var disks=' . json_encode($disktags) . ';
    function change(arr, oldindex, newindex) {
        //console.log(oldindex + "," + newindex);
        tmp=arr.splice(oldindex-1, 1);
        if (oldindex > newindex) {
            tmp1=JSON.parse(JSON.stringify(arr));
            tmp1.splice(newindex-1, arr.length-newindex+1);
            tmp2=JSON.parse(JSON.stringify(arr));
            tmp2.splice(0, newindex-1);
        } else {
            tmp1=JSON.parse(JSON.stringify(arr));
            tmp1.splice(newindex-1, arr.length-newindex+1);
            tmp2=JSON.parse(JSON.stringify(arr));
            tmp2.splice(0, newindex-1);
        }
        arr=tmp1.concat(tmp, tmp2);
        //console.log(arr);
        return arr;
    }
    function dragsort(t) {
        if (t.disktag_sort.value==\'\') {
            alert(\'' . geti18n('DragSort') . '\');
            return false;
        }
        envs = [' . $envs . '];
        if (envs.indexOf(t.disktag_sort.value)>-1) {
            alert(\'Do not input ' . $envs . '\');
            return false;
        }
        return true;
    }
    Sortable.create(document.getElementById(\'sortdisks\'), {
        animation: 150,
        onEnd: function (evt) { //拖拽完毕之后发生该事件
            //console.log(evt.oldIndex);
            //console.log(evt.newIndex);
            if (evt.oldIndex!=evt.newIndex) {
                disks=change(disks, evt.oldIndex, evt.newIndex);
                document.getElementById(\'sortdisks_form\').disktag_sort.value=JSON.stringify(disks);
            }
        }
    });
</script><br>';
        }
        $frame .= '
<select name="DriveType" onchange="changedrivetype(this.options[this.options.selectedIndex].value)">';
        foreach ($Driver_arr as $v1) {
            if ($v1!='.' && $v1!='..') {
                //$v1 = substr($v1, 0, -4);
                $v2 = splitlast($v1, '.php')[0];
                if ($v2 . '.php'==$v1) $frame .= '
    <option value="' . $v2 . '"' . ($v2=='Onedrive'?' selected="selected"':'') . '>' . $v2 . '</option>';
            }
        }
        $frame .= '
</select>
<a id="AddDisk_link" href="?AddDisk=Onedrive">' . geti18n('AddDisk') . '</a><br><br>
<script>
    function changedrivetype(d) {
        document.getElementById(\'AddDisk_link\').href="?AddDisk=" + d;
    }
</script>';

        $canOneKeyUpate = 0;
        if (isset($_SERVER['USER'])&&$_SERVER['USER']==='qcloud') {
            $canOneKeyUpate = 1;
        } elseif (isset($_SERVER['HEROKU_APP_DIR'])&&$_SERVER['HEROKU_APP_DIR']==='/app') {
            $canOneKeyUpate = 1;
        } elseif (isset($_SERVER['FC_SERVER_PATH'])&&$_SERVER['FC_SERVER_PATH']==='/var/fc/runtime/php7.2') {
            $canOneKeyUpate = 1;
        } elseif (isset($_SERVER['BCE_CFC_RUNTIME_NAME'])&&$_SERVER['BCE_CFC_RUNTIME_NAME']=='php7') {
            $canOneKeyUpate = 1;
        } elseif (isset($_SERVER['_APP_SHARE_DIR'])&&$_SERVER['_APP_SHARE_DIR']==='/var/share/CFF/processrouter') {
            $canOneKeyUpate = 1;
        } elseif (isset($_SERVER['DOCUMENT_ROOT'])&&$_SERVER['DOCUMENT_ROOT']==='/var/task/user') {
            $canOneKeyUpate = 1;
        } else {
            $tmp = time();
            if ( mkdir(''.$tmp, 0777) ) {
                rmdir(''.$tmp);
                $canOneKeyUpate = 1;
            }
        }
        $frame .= '<a href="https://github.com/XiaMoHuaHuo-CN/OneSM" target="_blank">Github</a>';
        /*if (!$canOneKeyUpate) {
            $frame .= '
' . geti18n('CannotOneKeyUpate') . '<br>';
        } else {
            $frame .= '
<form name="updateform" action="" method="post">
    <input name="_admin" type="hidden" value="">
    <input type="text" class="input-tepwd" name="auth" size="6" placeholder="auth" value="XiaMoHuaHuo-CN">
    <input type="text" class="input-tepwd" name="project" size="12" placeholder="project" value="OneSM">
    <button class="input-btn" name="QueryBranchs" onclick="querybranchs();return false;">' . geti18n('QueryBranchs') . '</button>
    <select name="branch">
        <option value="main">main</option>
    </select>
    <input type="submit" name="updateProgram" value="' . geti18n('updateProgram') . '" class="input-btn">
</form>
<script>
    function querybranchs()
    {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "https://api.github.com/repos/"+document.updateform.auth.value+"/"+document.updateform.project.value+"/branches");
        //xhr.setRequestHeader("User-Agent","XiaMoHuaHuo-CN/OneSM");
        xhr.onload = function(e){
            console.log(xhr.responseText+","+xhr.status);
            if (xhr.status==200) {
                document.updateform.branch.options.length=0;
                JSON.parse(xhr.responseText).forEach( function (e) {
                    document.updateform.branch.options.add(new Option(e.name,e.name));
                    if ("main"==e.name) document.updateform.branch.options[document.updateform.branch.options.length-1].selected = true; 
                });
                document.updateform.QueryBranchs.style.display="none";
            } else {
                alert(xhr.responseText+"\n"+xhr.status);
            }
        }
        xhr.onerror = function(e){
            alert("Network Error "+xhr.status);
        }
        xhr.send(null);
    }
</script>
';
        }
        if ($needUpdate) {
            $frame .= '<div style="position: relative; word-wrap: break-word;">
        ' . str_replace("\r", '<br>', $_SERVER['github_ver_new']) . '
</div>
<button class="input-btn" onclick="document.getElementById(\'github_ver_old\').style.display=(document.getElementById(\'github_ver_old\').style.display==\'none\'?\'\':\'none\');">更多...</button>
<div id="github_ver_old" style="position: relative; word-wrap: break-word; display: none">
        ' . str_replace("\r", '<br>', $_SERVER['github_ver_old']) . '
</div>';
        } else {
            $frame .= geti18n('NotNeedUpdate');
        }*/
        $frame .= '<br><br>
<script src="https://cdn.bootcss.com/js-sha1/0.6.0/sha1.min.js"></script>
<table>
    <form id="change_pass" name="change_pass" action="" method="POST" onsubmit="return changePassword(this);">
        <input name="_admin" type="hidden" value="">
    <tr>
        <td>' . geti18n('OldPassword') . ':</td><td><input type="password" name="oldPass" class="input-tepwd">
        <input type="hidden" name="timestamp"></td>
    </tr>
    <tr>
        <td>' . geti18n('NewPassword') . ':</td><td><input type="password" name="newPass1" class="input-tepwd"></td>
    </tr>
    <tr>
        <td>' . geti18n('ReInput') . ':</td><td><input type="password" name="newPass2" class="input-tepwd"></td>
    </tr>
    <tr>
        <td></td><td><button class="input-btn" name="changePass" value="changePass" class="input-btn">' . geti18n('ChangAdminPassword') . '</button></td>
    </tr>
    </form>
</table><br>
<table>
    <form id="config_f" name="config" action="" method="POST" onsubmit="return false;">
    <tr>
        <td>' . geti18n('AdminPassword') . ':<input type="password" name="pass" class="input-tepwd">
        <button class="input-btn" name="config_b" value="export" onclick="exportConfig(this);">' . geti18n('export') . '</button></td>
    </tr>
    <tr>
        <td>' . geti18n('config') . ':<input name="config_t" />
        <button class="input-btn" name="config_b" value="import" onclick="importConfig(this);">' . geti18n('import') . '</button></td>
    </tr>
    </form>
</table><br>
<script>
    var config_f = document.getElementById("config_f");
    function exportConfig(b) {
        if (config_f.pass.value=="") {
            alert("admin pass");
            return false;
        }
        try {
            sha1(1);
        } catch {
            alert("sha1.js 未加载，可能正在加载");
            return false;
        }
        var timestamp = new Date().getTime();
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "");
        xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded;charset=utf-8");
        xhr.onload = function(e){
            console.log(xhr.responseText+","+xhr.status);
            if (xhr.status==200) {
                var res = JSON.parse(xhr.responseText);
                config_f.config_t.value = xhr.responseText;
                config_f.parentNode.style = "width: 100%";
                config_f.config_t.style = "width: 100%";
                config_f.config_t.style.height = config_f.config_t.scrollHeight + "px";
            } else {
                alert(xhr.status+"\n"+xhr.responseText);
            }
        }
        xhr.onerror = function(e){
            alert("Network Error "+xhr.status);
        }
        xhr.send("pass=" + sha1(config_f.pass.value + "" + timestamp) + "&config_b=" + b.value + "&timestamp=" + timestamp + "&_admin=" + localStorage.getItem("admin"));
    }
    function importConfig(b) {
        if (config_f.pass.value=="") {
            alert("admin pass");
            return false;
        }
        if (config_f.config_t.value=="") {
            alert("input config");
            return false;
        } else {
            try {
                var tmp = JSON.parse(config_f.config_t.value);
            } catch(e) {
                alert("config error!");
                return false;
            }
        }
        try {
            sha1(1);
        } catch {
            alert("sha1.js 未加载，可能正在加载");
            return false;
        }
        var timestamp = new Date().getTime();
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "");
        xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded;charset=utf-8");
        xhr.onload = function(e){
            console.log(xhr.responseText+","+xhr.status);
            if (xhr.status==200) {
                //var res = JSON.parse(xhr.responseText);
                alert("Import success");
            } else {
                alert(xhr.status+"\n"+xhr.responseText);
            }
        }
        xhr.onerror = function(e){
            alert("Network Error "+xhr.status);
        }
        xhr.send("pass=" + sha1(config_f.pass.value + "" + timestamp) + "&config_t=" + encodeURIComponent(config_f.config_t.value) + "&config_b=" + b.value + "&timestamp=" + timestamp + "&_admin=" + localStorage.getItem("admin"));
    }
    function changePassword(f) {
        if (f.oldPass.value==""||f.newPass1.value==""||f.newPass2.value=="") {
            alert("Input");
            return false;
        }
        if (f.oldPass.value==f.newPass1.value) {
            alert("密码相同");
            return false;
        }
        if (f.newPass1.value!==f.newPass1.value) {
            alert("请重复密码");
            return false;
        }
        try {
            sha1(1);
        } catch {
            alert("sha1.js 未加载，可能正在加载");
            return false;
        }
        var timestamp = new Date().getTime();
        f.timestamp.value = timestamp;
        f.oldPass.value = sha1(f.oldPass.value + "" + timestamp);
        return true;
    }
</script>';
    }