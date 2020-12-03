//20071113

var userAgent = navigator.userAgent.toLowerCase();
var is_opera = userAgent.indexOf('opera') != -1 && opera.version();
var is_moz = (navigator.product == 'Gecko') && userAgent.substr(userAgent.indexOf('firefox') + 8, 3);
var is_ie = (userAgent.indexOf('msie') != -1 && !is_opera) && userAgent.substr(userAgent.indexOf('msie') + 5, 3);

function $DE(id) {
	return document.getElementById(id);
}
/*
function $(id) {
	return document.getElementById(id);
}
*/
function showbase(show,base) {
	var showobj = $DE(show);
	var baseobj = $DE(base);
	if(baseobj.checked)
	{
		showobj.style.display = "block";
	} else {
		showobj.style.display = "none";
	}
}

function showhide(id) {
    var obj = $DE(id);
    if(obj.style.display == "none") {
    	obj.style.display = "block";
    } else {
    	obj.style.display = "none";
    }
}

function checkall(form, prefix, checkall) {
	var checkall = checkall ? checkall : 'chkall';
	for(var i = 0; i < form.elements.length; i++) {
		var e = form.elements[i];
		if(e.name && e.name != checkall && (!prefix || (prefix && e.name.match(prefix)))) {
			e.checked = form.elements[checkall].checked;
		}
	}
}

function mb_strlen(str) {
	var len = 0;
	for(var i = 0; i < str.length; i++) {
		len += str.charCodeAt(i) < 0 || str.charCodeAt(i) > 255 ? (charset == 'utf-8' ? 3 : 2) : 1;
	}
	return len;
}