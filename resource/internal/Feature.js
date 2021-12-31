/*
 * Frature.js
 * 功能脚本
*/
function portocol() {
    var protocol = document.location.protocol;
        if (protocol = 'http:') {
        window.location.href = "https://"+window.location.host;
    }
}