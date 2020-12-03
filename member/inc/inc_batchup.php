<?
require_once(dirname(__FILE__)."/../../include/inc_channel_unit_functions.php");
function DelArc($aid)
{
	  global $dsql;
	  $aid = ereg_replace("[^0-9]","",$aid);
    //读取文档信息
    $arctitle = "";
    $arcurl = "";
    $arcQuery = "
    Select #@__archives.ID,#@__archives.title,#@__archives.typeid,
      #@__archives.ismake,#@__archives.senddate,#@__archives.arcrank,#@__channeltype.addtable,
 		  #@__archives.money,#@__arctype.typedir,#@__arctype.typename,#@__archives.adminID,
 		  #@__arctype.namerule,#@__arctype.namerule2,#@__arctype.ispart,
 		  #@__arctype.moresite,#@__arctype.siteurl,#@__arctype.siterefer,#@__arctype.sitepath 
		  from #@__archives
		  left join #@__arctype on #@__archives.typeid=#@__arctype.ID
		  left join #@__channeltype on #@__channeltype.ID=#@__archives.channel
		where #@__archives.ID='$aid'";
    $arcRow = $dsql->GetOne($arcQuery);
    if(!is_array($arcRow)) return false;
    //删除数据库的内容
    $dsql->ExecuteNoneQuery("Delete From #@__archives where ID='$aid'");
    if($arcRow['addtable']!=""){
        $dsql->ExecuteNoneQuery("Delete From ".$arcRow['addtable']." where aid='$aid'");
    }
    $dsql->ExecuteNoneQuery("Delete From #@__feedback where aid='$aid'");
    $dsql->ExecuteNoneQuery("Delete From #@__memberstow where arcid='$aid'");
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
    $ipath = $GLOBALS['cfg_cmspath']."/include/textdata/".(ceil($aid/5000))."/";
		$filename = $GLOBALS['cfg_basedir'].$ipath."{$aid}.txt";
		if(is_file($filename)) unlink($filename);
    return true;
}
//获取真实路径
//--------------------------
function GetTruePath($siterefer,$sitepath)
{
 		if($GLOBALS['cfg_multi_site']=='是'){
 		   if($siterefer==1) $truepath = ereg_replace("/{1,}","/",$GLOBALS["cfg_basedir"]."/".$sitepath);
	     else if($siterefer==2) $truepath = $sitepath;
	     else $truepath = $GLOBALS["cfg_basedir"];
	  }else{
	  	$truepath = $GLOBALS["cfg_basedir"];
	  }
	  return $truepath;
}
?>