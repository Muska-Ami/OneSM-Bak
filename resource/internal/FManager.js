/*
 * FManager.js
 * 文件管理功能
*/
var xhr = new XMLHttpRequest();
var path = JSON.parse(madata).path;

function fpwd() {
    xhr.open('POST', '/?getfpwdata&p='+path);
    xhr.send();
    xhr.onreadystatechange = function () {
        if (xhr.readyState = 4) {
            if (xhr.status = 200) {
                var data = xhr.responseText;
                var json = JSON.parse(data);
                return json.hash;
            } else {
                return 'Can\'t link server.';
            }
        }
    }
}
function fgpwdf(pwd) {
    var hash = md5(sha1(pwd));
    xhr.open('POST', '/?acceptfpwdata&p='+path+'&w='+hash);
    xhr.send();
    xhr.onreadystatechange = function () {
        if (xhr.readyState = 4) {
            if (xhr.status = 200) {
                var data = xhr.responseText;
                var json = JSON.parse(data);
                if (json.status = true) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
}

function frename(path, newname) {
    xhr.open('POST', '/?refilename&p='+path+'&n='+newname);
    xhr.send();
    xhr.onreadystatechange = function () {
        if (xhr.readyState = 4) {
            if (xhr.status = 200) {
                var data = xhr.responseText;
                var json = JSON.parse(data);
                if (json.status = true) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
}