/*function PM_addFriend(uid, stat)
{
    var dataS = {
		"action": "addfriend",
        "uid": uid,
		"stat": stat,
    };
    var data = $.param(dataS);
    $.ajax({
        type: "POST",
        dataType: "json",
        url: glfusionSiteUrl + "/pm/ajax_addfriend.php",
        data: data,
        success: function(result) {
            try {
				if (result.status == 0) {
					console.log(result);
					if (stat == 1) {	// added the friend
						$("#sp_is_friend").css("display", "inline");
						$("#sp_not_friend").css("display", "none");
					} else {			// removed the friend
						$("#sp_is_friend").css("display", "none");
						$("#sp_not_friend").css("display", "inline");
					}
					PM.notify(result.message);
				}
            } catch(err) {
            }
        },
        error: function() {
        }
    });
    return false;
}*/

var PM = (function() {
	return {
		addFriend: function(uid, stat) {
		    var dataS = {
				"action": "addfriend",
		        "uid": uid,
				"stat": stat,
		    };
		    var data = $.param(dataS);
		    $.ajax({
				type: "POST",
		        dataType: "json",
				url: glfusionSiteUrl + "/pm/ajax_addfriend.php",
		        data: data,
				success: function(result) {
					try {
						if (result.status == 0) {
							console.log(result);
							if (stat == 1) {	// added the friend
								$("#sp_is_friend").css("display", "inline");
								$("#sp_not_friend").css("display", "none");
								$("#unblock_user").css("display", "none");
								$("#block_user").css("display", "block");
							} else {			// removed the friend
								$("#sp_is_friend").css("display", "none");
								$("#sp_not_friend").css("display", "inline");
								$("#unblock_user").css("display", "none");
								$("#block_user").css("display", "block");
							}
							PM.notify(result.message);
						}
				    } catch(err) {
		            }
				},
		        error: function() {
				}
		    });
		    return false;
		},

		blockUser: function(uid, stat) {
		    var dataS = {
				"action": "blockuser",
		        "uid": uid,
				"stat": stat,
		    };
		    var data = $.param(dataS);
		    $.ajax({
				type: "POST",
		        dataType: "json",
				url: glfusionSiteUrl + "/pm/ajax_addfriend.php",
		        data: data,
				success: function(result) {
					try {
						if (result.status == 0) {
							console.log(result);
							if (stat == 1) {	// blocked the user
								$("#sp_is_friend").css("display", "none");
								$("#sp_not_friend").css("display", "inline");
								$("#unblock_user").css("display", "block");
								$("#block_user").css("display", "none");
							} else {			// removed the block
								$("#unblock_user").css("display", "none");
								$("#block_user").css("display", "block");
							}
							PM.notify(result.message);
						}
				    } catch(err) {
		            }
				},
		        error: function() {
				}
		    });
		    return false;
		},

		// Display a notification popup for a short time.
		notify: function(message, status='', timeout=1500) {
			if (status == 'success') {
				var icon = "<i class='uk-icon uk-icon-check'></i>&nbsp;";
			} else if (status == 'warning') {
				var icon = '<i class="uk-icon uk-icon-exclamation-triangle"></i>&nbsp';
			} else {
				var icon = '';
			}
			if (typeof UIkit.notify === 'function') {
				// uikit v2 theme
	            UIkit.notify(icon + message, {timeout: timeout});
			} else if (typeof UIkit.notification === 'function') {
		        // uikit v3 theme
				UIkit.notification({
		            message: icon + message,
				    timeout: timeout,
		            status: status,
				});
		    } else {
				alert(message);
			}
		}
	};
})();

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
