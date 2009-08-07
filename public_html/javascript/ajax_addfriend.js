var xmlhttp = null;

function ajax_addfriend(uid,auid) {

    xmlhttp = new XMLHttpRequest();

    var qs = '';

    qs = '?uid=' + uid + '&auid=' + auid;

    xmlhttp.open('GET', site_url + '/pm/ajax_addfriend.php' + qs, true);
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            receiveUserAdd(xmlhttp.responseXML,auid);
        }
    };
    xmlhttp.send(null);

}
function receiveUserAdd(dom) {
    var id = '';
    try{
        var oxml = dom.getElementsByTagName('auid');
        id = oxml[0].childNodes[0].nodeValue;
    }catch(e){}
    var html = '';
    try{
        var oxml = dom.getElementsByTagName('html');
        html = oxml[0].childNodes[0].nodeValue;
    }catch(e){}

    if (id != '' && html != '') {
        var obj = document.getElementById('u' + id);
        obj.innerHTML = html;
    }
}