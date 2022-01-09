<?php
// https://vercel.com/docs/api#endpoints/deployments/create-a-new-deployment

function getpath()
{
    $_SERVER['firstacceptlanguage'] = strtolower(splitfirst(splitfirst($_SERVER['HTTP_ACCEPT_LANGUAGE'],';')[0],',')[0]);
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
    if (isset($_SERVER['HTTP_FLY_CLIENT_IP'])) $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_FLY_CLIENT_IP'];
    if ($_SERVER['REQUEST_SCHEME']!='http'&&$_SERVER['REQUEST_SCHEME']!='https') {
        if ($_SERVER['HTTP_X_FORWARDED_PROTO']!='') {
            $tmp = explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO'])[0];
            if ($tmp=='http'||$tmp=='https') $_SERVER['REQUEST_SCHEME'] = $tmp;
        }
        if ($_SERVER['HTTP_FLY_FORWARDED_PROTO']!='') $_SERVER['REQUEST_SCHEME'] = $_SERVER['HTTP_FLY_FORWARDED_PROTO'];
    }
    $_SERVER['host'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
    $_SERVER['referhost'] = explode('/', $_SERVER['HTTP_REFERER'])[2];
    $_SERVER['base_path'] = "/";
    if (isset($_SERVER['UNENCODED_URL'])) $_SERVER['REQUEST_URI'] = $_SERVER['UNENCODED_URL'];
    $p = strpos($_SERVER['REQUEST_URI'],'?');
    if ($p>0) $path = substr($_SERVER['REQUEST_URI'], 0, $p);
    else $path = $_SERVER['REQUEST_URI'];
    $path = path_format( substr($path, strlen($_SERVER['base_path'])) );
    $_SERVER['DOCUMENT_ROOT'] = '/var/task/user';
    return $path;
}

function getGET()
{
    if (!$_POST) {
        if (!!$HTTP_RAW_POST_DATA) {
            $tmpdata = $HTTP_RAW_POST_DATA;
        } else {
            $tmpdata = file_get_contents('php://input');
        }
        if (!!$tmpdata) {
            $postbody = explode("&", $tmpdata);
            foreach ($postbody as $postvalues) {
                $pos = strpos($postvalues,"=");
                $_POST[urldecode(substr($postvalues,0,$pos))]=urldecode(substr($postvalues,$pos+1));
            }
        }
    }
    if (isset($_SERVER['UNENCODED_URL'])) $_SERVER['REQUEST_URI'] = $_SERVER['UNENCODED_URL'];
    $p = strpos($_SERVER['REQUEST_URI'],'?');
    if ($p>0) {
        $getstr = substr($_SERVER['REQUEST_URI'], $p+1);
        $getstrarr = explode("&",$getstr);
        foreach ($getstrarr as $getvalues) {
            if ($getvalues != '') {
                $pos = strpos($getvalues, "=");
            //echo $pos;
                if ($pos > 0) {
                    $getarry[urldecode(substr($getvalues, 0, $pos))] = urldecode(substr($getvalues, $pos + 1));
                } else {
                    $getarry[urldecode($getvalues)] = true;
                }
            }
        }
    }
    if (isset($getarry)) {
        return $getarry;
    } else {
        return [];
    }
}

function getConfig($str, $disktag = '')
{
    $projectPath = splitlast(__DIR__, '/')[0];
    $configPath = $projectPath . '/.data/config.php';
    $s = file_get_contents($configPath);
    $configs = '{' . splitlast(splitfirst($s, '{')[1], '}')[0] . '}';
    if ($configs!='') {
        $envs = json_decode($configs, true);
        if (isInnerEnv($str)) {
            if ($disktag=='') $disktag = $_SERVER['disktag'];
            if (isset($envs[$disktag][$str])) {
                if (isBase64Env($str)) return base64y_decode($envs[$disktag][$str]);
                else return $envs[$disktag][$str];
            }
        } else {
            if (isset($envs[$str])) {
                if (isBase64Env($str)) return base64y_decode($envs[$str]);
                else return $envs[$str];
            }
        }
    }
    return '';
}

function setConfig($arr, $disktag = '')
{
    if ($disktag=='') $disktag = $_SERVER['disktag'];
    $projectPath = splitlast(__DIR__, '/')[0];
    $configPath = $projectPath . '/.data/config.php';
    $s = file_get_contents($configPath);
    $configs = '{' . splitlast(splitfirst($s, '{')[1], '}')[0] . '}';
    if ($configs!='') $envs = json_decode($configs, true);
    $disktags = explode("|",getConfig('disktag'));
    $indisk = 0;
    $operatedisk = 0;
    foreach ($arr as $k => $v) {
        if (isCommonEnv($k)) {
            if (isBase64Env($k)) $envs[$k] = base64y_encode($v);
            else $envs[$k] = $v;
        } elseif (isInnerEnv($k)) {
            if (isBase64Env($k)) $envs[$disktag][$k] = base64y_encode($v);
            else $envs[$disktag][$k] = $v;
            $indisk = 1;
        } elseif ($k=='disktag_add') {
            array_push($disktags, $v);
            $operatedisk = 1;
        } elseif ($k=='disktag_del') {
            $disktags = array_diff($disktags, [ $v ]);
            $envs[$v] = '';
            $operatedisk = 1;
        } elseif ($k=='disktag_copy') {
            $newtag = $v . '_' . date("Ymd_His");
            $envs[$newtag] = $envs[$v];
            array_push($disktags, $newtag);
            $operatedisk = 1;
        } elseif ($k=='disktag_rename' || $k=='disktag_newname') {
            if ($arr['disktag_rename']!=$arr['disktag_newname']) $operatedisk = 1;
        } else {
            $envs[$k] = $v;
        }
    }
    if ($indisk) {
        $diskconfig = $envs[$disktag];
        $diskconfig = array_filter($diskconfig, 'array_value_isnot_null');
        ksort($diskconfig);
        $envs[$disktag] = $diskconfig;
    }
    if ($operatedisk) {
        if (isset($arr['disktag_newname']) && $arr['disktag_newname']!='') {
            $tags = [];
            foreach ($disktags as $tag) {
                if ($tag==$arr['disktag_rename']) array_push($tags, $arr['disktag_newname']);
                else array_push($tags, $tag);
            }
            $envs['disktag'] = implode('|', $tags);
            $envs[$arr['disktag_newname']] = $envs[$arr['disktag_rename']];
            $envs[$arr['disktag_rename']] = '';
        } else {
            $disktags = array_unique($disktags);
            foreach ($disktags as $disktag) if ($disktag!='') $disktag_s .= $disktag . '|';
            if ($disktag_s!='') $envs['disktag'] = substr($disktag_s, 0, -1);
            else $envs['disktag'] = '';
        }
    }
    $envs = array_filter($envs, 'array_value_isnot_null');
    //ksort($envs);
    //sortConfig($envs);
    //error_log1(json_encode($arr, JSON_PRETTY_PRINT) . ' => tmp：' . json_encode($envs, JSON_PRETTY_PRINT));
    //echo json_encode($arr, JSON_PRETTY_PRINT) . ' => tmp：' . json_encode($envs, JSON_PRETTY_PRINT);
    return setVercelConfig($envs, getConfig('HerokuappId'), getConfig('APIKey'));
}

function install()
{
    global $i18n;
    if ($_GET['install1']) {
        if ($_POST['admin']!='') {
            $tmp['admin'] = $_POST['admin'];
            //$tmp['language'] = $_POST['language'];
            $tmp['timezone'] = $_COOKIE['timezone'];
            $APIKey = $_POST['APIKey'];
            //if ($APIKey=='') {
            //    $APIKey = getConfig('APIKey');
            //}
            $tmp['APIKey'] = $APIKey;

            $token = $APIKey;
            $header["Authorization"] = "Bearer " . $token;
            $header["Content-Type"] = "application/json";
            $aliases = json_decode(curl("GET", "https://api.vercel.com/v3/now/aliases", "", $header)['body'], true);
            $host = splitfirst($_SERVER["host"], "//")[1];
            foreach ($aliases["aliases"] as $key => $aliase) {
                if ($host==$aliase["alias"]) $projectId = $aliase["projectId"];
            }
            $tmp['HerokuappId'] = $projectId;

            $response = json_decode(setVercelConfig($tmp, $projectId, $APIKey), true);
            if (api_error($response)) {
                $html = api_error_msg($response);
                $title = 'Error';
                return message($html, $title, 400);
            } else {
                $html = geti18n('Success') . '
    <script>
        var status = "' . $response['DplStatus'] . '";
        var i = 0;
        var expd = new Date();
        expd.setTime(expd.getTime()+1000);
        var expires = "expires="+expd.toGMTString();
        document.cookie=\'language=; path=/; \'+expires;
        var uploadList = setInterval(function(){
            if (document.getElementById("dis").style.display=="none") {
                console.log(i++);
            } else {
                clearInterval(uploadList);
                location.href = "' . path_format($_SERVER['base_path'] . '/') . '";
            }
        }, 1000);
    </script>';
                return message($html, $title, 201, 1);
            }
        }
    }
    if ($_GET['install0']) {
        $html .= '
    <form action="?install1" method="post" onsubmit="return notnull(this);">
language:<br>';
        foreach ($i18n['languages'] as $key1 => $value1) {
            $html .= '
        <label><input type="radio" name="language" value="'.$key1.'" '.($key1==$i18n['language']?'checked':'').' onclick="changelanguage(\''.$key1.'\')">'.$value1.'</label><br>';
        }
        $html .= '<br>
        <a href="https://vercel.com/account/tokens" target="_blank">' . geti18n('Create') . ' token</a><br>
        <label>Token:<input name="APIKey" type="password" placeholder="" value=""></label><br>';
        $html .= '<br>
        <label>Set admin password:<input name="admin" type="password" placeholder="' . geti18n('EnvironmentsDescription')['admin'] . '" size="' . strlen(geti18n('EnvironmentsDescription')['admin']) . '"></label><br>';
        $html .= '
        <input type="submit" value="'.geti18n('Submit').'">
    </form>
    <div id="showerror"></div>
    <script>
        var nowtime= new Date();
        var timezone = 0-nowtime.getTimezoneOffset()/60;
        var expd = new Date();
        expd.setTime(expd.getTime()+(2*60*60*1000));
        var expires = "expires="+expd.toGMTString();
        document.cookie="timezone="+timezone+"; path=/; "+expires;
        var errordiv = document.getElementById("showerror");
        function changelanguage(str)
        {
            var expd = new Date();
            expd.setTime(expd.getTime()+(2*60*60*1000));
            var expires = "expires="+expd.toGMTString();
            document.cookie=\'language=\'+str+\'; path=/; \'+expires;
            location.href = location.href;
        }
        function notnull(t)
        {
            if (t.admin.value==\'\') {
                alert(\'input admin\');
                return false;
            }
            if (t.APIKey.value==\'\') {
                alert(\'input Token\');
                return false;
            }
            return true;
        }
    </script>';
        $title = geti18n('SelectLanguage');
        return message($html, $title, 201);
    }

    if (substr($_SERVER["host"], -10)=="vercel.app") {
        $html .= '<a href="?install0">' . geti18n('ClickInstall') . '</a>, ' . geti18n('LogintoBind');
        $html .= "<br>Remember: you MUST wait 30-60s after each operate / do some change, that make sure Vercel has done the building<br>" ;
    } else {
        $html.= "Please visit form *.vercel.app";
    }
    $title = 'Install';
    return message($html, $title, 201);
}

function copyFolder($from, $to)
{
    if (substr($from, -1)=='/') $from = substr($from, 0, -1);
    if (substr($to, -1)=='/') $to = substr($to, 0, -1);
    if (!file_exists($to)) mkdir($to, 0777, 1);
    $handler=opendir($from);
    while($filename=readdir($handler)) {
        if($filename != '.' && $filename != '..'){
            $fromfile = $from.'/'.$filename;
            $tofile = $to.'/'.$filename;
            if(is_dir($fromfile)){// 如果读取的某个对象是文件夹，则递归
                copyFolder($fromfile, $tofile);
            }else{
                copy($fromfile, $tofile);
            }
        }
    }
    closedir($handler);
    return 1;
}

function setVercelConfig($envs, $appId, $token)
{
    sortConfig($envs);
    $outPath = '/tmp/code/';
    $outPath_Api = $outPath . 'api/';
    $coderoot = __DIR__;
    $coderoot = splitlast($coderoot, '/')[0] . '/';
    //echo $outPath_Api . '<br>' . $coderoot . '<br>';
    copyFolder($coderoot, $outPath_Api);
    $prestr = '<?php $configs = \'' . PHP_EOL;
    $aftstr = PHP_EOL . '\';';
    file_put_contents($outPath_Api . '.data/config.php', $prestr . json_encode($envs, JSON_PRETTY_PRINT) . $aftstr);

    return VercelUpdate($appId, $token, $outPath);
}

function VercelUpdate($appId, $token, $sourcePath = "")
{
    if (checkBuilding($appId, $token)) return '{"error":{"message":"Another building is in progress."}}';
    $url = "https://api.vercel.com/v13/deployments";
    $header["Authorization"] = "Bearer " . $token;
    $header["Content-Type"] = "application/json";
    $data["name"] = "OneSM";
    $data["project"] = $appId;
    $data["target"] = "production";
    $data["routes"][0]["src"] = "/(.*)";
    $data["routes"][0]["dest"] = "/api/index.php";
    $data["functions"]["api/index.php"]["runtime"] = "vercel-php@0.4.0";
    if ($sourcePath=="") $sourcePath = splitlast(splitlast(__DIR__, "/")[0], "/")[0];
    //echo $sourcePath . "<br>";
    getEachFiles($file, $sourcePath);
    $data["files"] = $file;

    //echo json_encode($data, JSON_PRETTY_PRINT) . " ,data<br>";
    $response = curl("POST", $url, json_encode($data), $header);
    //echo json_encode($response, JSON_PRETTY_PRINT) . " ,res<br>";
    $result = json_decode($response["body"], true);
    $result['DplStatus'] = $result['id'];
    return json_encode($result);
}

function checkBuilding($projectId, $token)
{
    $r = 0;
    $url = "https://api.vercel.com/v6/deployments/?projectId=" . $projectId;
    $header["Authorization"] = "Bearer " . $token;
    $header["Content-Type"] = "application/json";
    $response = curl("GET", $url, '', $header);
    //echo json_encode($response, JSON_PRETTY_PRINT) . " ,res<br>";
    $result = json_decode($response["body"], true);
    foreach ( $result['deployments'] as $deployment ) {
        if ($deployment['state']!=="READY") $r++;
    }
    return $r;
    //if ($r===0) return true;
    //else return false;
}

function getEachFiles(&$file, $base, $path = "")
{
    //if (substr($base, -1)=="/") $base = substr($base, 0, -1);
    //if (substr($path, -1)=="/") $path = substr($path, 0, -1);
    $handler=opendir(path_format($base . "/" . $path));
    while($filename=readdir($handler)) {
        if($filename != '.' && $filename != '..' && $filename != '.git'){
            $fromfile = path_format($base . "/" . $path . "/" . $filename);
        //echo $fromfile . "<br>";
            if(is_dir($fromfile)){// 如果读取的某个对象是文件夹，则递归
                $response = getEachFiles($file, $base, path_format($path . "/" . $filename));
                if (api_error(setConfigResponse($response))) return $response;
            }else{
                $tmp['file'] = path_format($path . "/" . $filename);
                $tmp['data'] = file_get_contents($fromfile);
                $file[] = $tmp;
            }
        }
    }
    closedir($handler);
    
    return json_encode( [ 'response' => 'success' ] );
}

function api_error($response)
{
    return isset($response['error']);
}

function api_error_msg($response)
{
    return $response['error']['code'] . '<br>
' . $response['error']['message'] . '<br>
<button onclick="location.href = location.href;">'.geti18n('Refresh').'</button>';
}

function setConfigResponse($response)
{
    return json_decode($response, true);
}

function WaitFunction($deployid) {
    if ($buildId=='1') {
        $tmp['stat'] = 400;
        $tmp['body'] = 'id must provided.';
        return $tmp;
    }
    $header["Authorization"] = "Bearer " . getConfig('APIKey');
    $header["Content-Type"] = "application/json";
    $url = "https://api.vercel.com/v11/deployments/" . $deployid;
    $response = curl("GET", $url, "", $header);
    if ($response['stat']==200) {
        $result = json_decode($response['body'], true);
        if ($result['readyState']=="READY") return true;
        if ($result['readyState']=="ERROR") return $response;
        return false;
    } else {
        $response['body'] .= $url;
        return $response;
    }
}

function changeAuthKey() {
    if ($_POST['APIKey']!='') {
        $APIKey = $_POST['APIKey'];
        $tmp['APIKey'] = $APIKey;
        $response = setConfigResponse( setVercelConfig($tmp, getConfig('HerokuappId'), $APIKey) );
        if (api_error($response)) {
            $html = api_error_msg($response);
            $title = 'Error';
            return message($html, $title, 400);
        } else {
            $html = geti18n('Success') . '
    <script>
        var status = "' . $response['DplStatus'] . '";
        var i = 0;
        var uploadList = setInterval(function(){
            if (document.getElementById("dis").style.display=="none") {
                console.log(i++);
            } else {
                clearInterval(uploadList);
                location.href = "' . path_format($_SERVER['base_path'] . '/') . '";
            }
        }, 1000);
    </script>';
            return message($html, $title, 201, 1);
        }
    }
    $html = '
    <form action="" method="post" onsubmit="return notnull(this);">
        <a href="https://vercel.com/account/tokens" target="_blank">' . geti18n('Create') . ' token</a><br>
        <label>Token:<input name="APIKey" type="password" placeholder="" value=""></label><br>
        <input type="submit" value="' . geti18n('Submit') . '">
    </form>
    <script>
        function notnull(t)
        {
            if (t.APIKey.value==\'\') {
                alert(\'Input Token\');
                return false;
            }
            return true;
        }
    </script>';
    return message($html, 'Change platform Auth token or key', 200);
}
