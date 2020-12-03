
//初始化导航菜单状态
var m = new Array();
$.cookie("dede_member_menu")==null ? cookie = "0,1,0,1,0,0,0,0" : cookie = $.cookie("dede_member_menu");
m = cookie.split(",");

$(document).ready(function(){
	//导航菜单
	$(".menu dl dt").each(function(i){
		$(this).attr("_dedemenu",i.toString(10));//给每个菜单设置标记
		mbox = $(this).next("dd");
		if( m[i] == "0" ){//应用菜单状态
			mbox.hide();
		}else if( m[i] =="1" ){
			mbox.show();
		}else{
			setmenu(i);
		}
	}).click(function(){//定义菜单事件
		mid = $(this).attr("_dedemenu");
		mbox = $(this).next("dd");
		if(mbox.css("display")=="block"){
			mbox.slideUp("fast");
			//mbox.hide();
			setmenu(mid);
		}else{
			mbox.slideDown("fast");
			//mbox.show();
			setmenu(mid);
		}
	});
	
	//table tbody设置
	tbody();
	//文本输入框CSS+鼠标悬停
	inputstyle();
	//隔行换色
	trevencolor(".trlist tbody tr:even");
	//鼠标悬停变色
	trmouseon(".trlist tbody tr");
});

function tbody(){
	$(".toggle").each(function(){//table body默认设置
		tbodybox = $(this).parent("th").parent("tr").parent("tbody").children("tr").not($(this).parent("th").parent("tr"));
		$(this).attr("src") == "images/toggle_off.gif" ? tbodybox.hide() : tbodybox.show();
	}).click(function(){//table body缩放
		$(this).parent("th").parent("tr").parent("tbody").children("tr").not($(this).parent("th").parent("tr")).toggle();
		$(this).attr("src") == "images/toggle_off.gif" ? $(this).attr("src","images/toggle_on.gif") : $(this).attr("src","images/toggle_off.gif");
	});
}

function setmenu(mid){//菜单Cookie状态切换
	m[mid]=="1" ? m[mid]="0" : m[mid]="1";
	$.cookie("dede_member_menu",m.join(","));
	return m[mid];
}

function inputstyle(){	//文本输入框CSS+鼠标悬停
	$("input[type='text']").addClass("textipt").focus(function(){ $(this).addClass("textipt_on");}).blur(function(){$(this).removeClass("textipt_on");});
	$("input[type='password']").addClass("textipt").focus(function(){ $(this).addClass("textipt_on");}).blur(function(){$(this).removeClass("textipt_on");});
	$("input[type='file']").addClass("textipt").focus(function(){ $(this).addClass("textipt_on");}).blur(function(){$(this).removeClass("textipt_on");});
	$("textarea").focus(function(){ $(this).addClass("textipt_on");}).blur(function(){$(this).removeClass("textipt_on");});
}

function trevencolor(tdres){//隔行换色
	$(tdres).addClass("linecolor");
}

function trmouseon(tdres){//鼠标悬停
	$(tdres).mouseover(function(){ $(this).addClass("oncolor");}).mouseout(function(){$(this).removeClass("oncolor");	});
}
