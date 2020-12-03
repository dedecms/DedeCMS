<?php
require_once(dirname(__FILE__)."/../../include/inc_channel_unit_functions.php");
function DelArc($aid,$onlyfile=false,$channelid=0)
{
	  global $dsql;
	  if(!is_object($dsql)) $dsql = new DedeSql(false);
	  $tables = GetChannelTable($dsql,$aid,'arc');
    //读取文档信息
    $arctitle = "";
    $arcurl = "";
    $arcQuery = "
    Select a.ID,a.title,a.typeid,
    a.ismake,a.senddate,a.arcrank,c.addtable,
 		a.money,t.typedir,t.typename,a.adminID,
 		t.namerule,t.namerule2,t.ispart,
 		t.moresite,t.siteurl,t.siterefer,t.sitepath 
		from `{$tables['maintable']}` a 
		left join `#@__arctype` t on a.typeid=t.ID
		left join `#@__channeltype` c on c.ID=a.channel
    where a.ID='$aid'
    ";
    $arcRow = $dsql->GetOne($arcQuery);
    if(!is_array($arcRow)) return false;
    //删除数据库的内容
    $rs = $dsql->ExecuteNoneQuery("Delete From `{$tables['maintable']}` where ID='$aid'");
    if($rs){
       $dsql->ExecuteNoneQuery("Delete From `#@__full_search` where aid='$aid'");
       if($arcRow['addtable']!=""){
         $dsql->ExecuteNoneQuery("Delete From `{$tables['addtable']}` where aid='$aid'");
       }
       $dsql->ExecuteNoneQuery("Delete From `#@__feedback` where aid='$aid'");
       $dsql->ExecuteNoneQuery("Delete From `#@__memberstow` where arcid='$aid'");
    }
    //删除HTML
    if($arcRow['ismake']==-1||$arcRow['arcrank']!=0
    ||$arcRow['typeid']==0||$arcRow['money']>0){
  		return true;
  	}
  	$arcurl = GetFileUrl($arcRow['ID'],$arcRow['typeid'],$arcRow['senddate'],$arcRow['title'],$arcRow['ismake'],
           $arcRow['arcrank'],$arcRow['namerule'],$arcRow['typedir'],$arcRow['money'],false,'');
    if(!ereg("\?",$arcurl)){
    	 $truedir = GetTruePath($arcRow['siterefer'],$arcRow['sitepath']);
    	 $htmlfile = $truedir.$arcurl;
    	 if(file_exists($htmlfile) && !is_dir($htmlfile)) unlink($htmlfile);
    	 $arcurls = explode(".",$arcurl);
    	 $sname = $arcurls[count($arcurls)-1];
    	 $fname = ereg_replace("(\.$sname)$","",$arcurl);
    	 for($i=2;$i<=100;$i++){
    		 $htmlfile = $truedir.$fname."_$i".".".$sname;
    		 if(file_exists($htmlfile) && !is_dir($htmlfile)) unlink($htmlfile);
    		 else break;
    	 }
    }
    //删除文本文件
    $ipath = $GLOBALS['cfg_cmspath']."/data/textdata/".(ceil($aid/5000))."/";
		$filename = $GLOBALS['cfg_basedir'].$ipath."{$aid}.txt";
		if(is_file($filename)) unlink($filename);
    return true;
}
//获取真实路径
//--------------------------
function GetTruePath($siterefer,$sitepath)
{
 		if($GLOBALS['cfg_multi_site']=='Y'){
 		   if($siterefer==1) $truepath = ereg_replace("/{1,}","/",$GLOBALS["cfg_basedir"]."/".$sitepath);
	     else if($siterefer==2) $truepath = $sitepath;
	     else $truepath = $GLOBALS["cfg_basedir"];
	  }else{
	  	$truepath = $GLOBALS["cfg_basedir"];
	  }
	  return $truepath;
}
?>