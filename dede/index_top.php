<?php 
require(dirname(__FILE__)."/config.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>dedecms</title>
<link href="base.css" rel="stylesheet" type="text/css">
<style>
	#ldd1 { float:left; position:absolute; left:0px; top:0px; }
	#ldd2 { float:right; }
	#ldd2 dd{ float:right }
	#ldd2 dl{ margin-right:26px }
	#sktop{ text-align:right; margin-right:28px;
  height:22; margin-top:4px; margin-bottom:3px; line-height:22px  }
	.bdd{ float:right; height:26px; padding-left:6px; padding-right:6px;
	     background-image:url(img/tn2.gif); line-height:29px;
	     border-right:1px solid #2C6FA8;border-left:1px solid #efefef }
	.bdd2{ float:right; height:26px; padding-left:6px; padding-right:6px;
	     background-image:url(img/tn22.gif); line-height:29px;
	     border-right:1px solid #2C6FA8;border-left:1px solid #efefef }
	#bdds{ float:right; height:26px; padding-left:3px; padding-right:6px;
	     background-image:url(img/tn2.gif); line-height:29px;
	     border-right:1px solid #2C6FA8; } 
	#bdde{ float:right; height:26px; padding-left:6px; padding-right:3px;
	     background-image:url(img/tn2.gif); line-height:29px;
	     border-left:1px solid #efefef }
	#main{ margin:0px; padding:0px; width:100%; height:60px; background-image:url(img/ntbg2.gif) }
</style>
<script language='javascript'>

function $Nav(){
	if(window.navigator.userAgent.indexOf("MSIE")>=1) return 'IE';
  else if(window.navigator.userAgent.indexOf("Firefox")>=1) return 'FF';
  else return "OT";
}

var preID = 0;

function OpenMenu(cid,lurl,rurl,bid){
   if($Nav()=='IE'){
     if(rurl!='') top.document.frames.main.location = rurl;
     if(cid > -1) top.document.frames.menu.location = 'index_menu.php?c='+cid;
     else if(lurl!='') top.document.frames.menu.location = lurl;
     if(bid>0) document.getElementById("d"+bid).className = 'bdd2';
     if(preID>0 && preID!=bid) document.getElementById("d"+preID).className = 'bdd';
     preID = bid;
   }else{
     if(rurl!='') top.document.getElementById("main").src = rurl;
     if(cid > -1) top.document.getElementById("menu").src = 'index_menu.php?c='+cid;
     else if(lurl!='') top.document.getElementById("menu").src = lurl;
     if(bid>0) document.getElementById("d"+bid).className = 'bdd2';
     if(preID>0 && preID!=bid) document.getElementById("d"+preID).className = 'bdd';
     preID = bid;
   }
}

var preFrameW = '160,*';
var FrameHide = 0;
function ChangeMenu(way){
	var addwidth = 10;
	var fcol = top.document.all.bodyFrame.cols;
	if(way==1) addwidth = 10;
	else if(way==-1) addwidth = -10;
	else if(way==0){
		if(FrameHide == 0){
			preFrameW = top.document.all.bodyFrame.cols;
			top.document.all.bodyFrame.cols = '0,*';
			FrameHide = 1;
			return;
		}else{
			top.document.all.bodyFrame.cols = preFrameW;
			FrameHide = 0;
			return;
		}
	}
	fcols = fcol.split(',');
	fcols[0] = parseInt(fcols[0]) + addwidth;
	top.document.all.bodyFrame.cols = fcols[0]+',*';
}

function resetBT(){
	if(preID>0) document.getElementById("d"+preID).className = 'bdd';
	preID = 0;
}

</script>
</head>
<body bgColor='#ffffff' leftMargin='0' topMargin='0'>
<div id='ldd1'><a href="http://www.dedecms.com" target="_blank"><img src="img/nttitle2.gif" width="177" height="55" border="0" alt="织梦网站管理系统"></a></div>
<div id='main'>
	<div id='ldd2'>
    <div id='sktop'>
    你好：<?php echo $cuserLogin->getUserName()?> ，欢迎登录织梦内容管理系统！
    <a href="javascript:ChangeMenu(-1)">
    	<img src='img/frame-l.gif' border='0' alt="减小左框架">
    </a>
    <a href="javascript:ChangeMenu(0)">
    <img src='img/frame_on.gif' border='0' alt="隐藏/显示左框架">
    </a>
    <a href="javascript:ChangeMenu(1)" title="增大左框架">
    	<img src='img/frame-r.gif' border='0' alt="增大左框架">
    </a>
    </div>
    <dl>
      <dd><img src='img/ttn3.gif' width='7' height='26'></dd>
      <dd id='bdde'><a href='exit.php' target='_parent'>『注销』</a></dd>
      <dd class='bdd' id='d9'><a href="javascript:OpenMenu(0,'index_menu.php','',9)">「全部」</a></dd>
      <dd class='bdd' id='d8'><a href="javascript:OpenMenu(7,'','sys_info.php',8)">系统设置</a></dd>
      <dd class='bdd' id='d7'><a href="javascript:OpenMenu(6,'','member_main.php',7)">功能模块</a></dd>
      <dd class='bdd' id='d6'><a href="javascript:OpenMenu(5,'','plus_main.php',6)">辅助插件</a></dd>
      <dd class='bdd' id='d5'><a href="javascript:OpenMenu(4,'','file_manage_main.php?activepath=<?php echo $cfg_templets_dir?>',5)">模板管理</a></dd>
      <dd class='bdd' id='d4'><a href="javascript:OpenMenu(3,'','makehtml_list.php',4)">HTML更新</a></dd>
      <dd class='bdd' id='d3'><a href="javascript:OpenMenu(2,'','feedback_main.php',3)">内容维护</a></dd>
      <dd class='bdd' id='d2'><a href="javascript:OpenMenu(-1,'catalog_menu.php','article_add.php?channelid=1',2)">内容发布</a></dd>
      <dd class='bdd' id='d1'><a href="javascript:OpenMenu(1,'','catalog_main.php',1)">频道管理</a></dd>
      <dd id='bdds'><a href="javascript:OpenMenu(9,'','index_body.php',0)">主页</a></dd>
      <dd><img src='img/ttn1.gif' width='20' height='26'></dd>
    </dl>
  </div>
</div>
</div>
</body>
</html>