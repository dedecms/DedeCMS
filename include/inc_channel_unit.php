<?php 
require_once(DEDEINC."/pub_dedetag.php");
require_once(DEDEINC."/inc_channel_unit_functions.php");
$GLOBALS['cfg_softinfos'] = '';
/*----------------------------------
表示特定频道的附加数据结构信息
function C____ChannelUnit();
-----------------------------------*/
class ChannelUnit
{
	var $ChannelInfos;
	var $ChannelFields;
	var $AllFieldNames;
	var $ChannelID;
	var $ArcID;
	var $dsql;
	var $SplitPageField;
	//-------------
	//php5构造函数
	//-------------
	function __construct($cid,$aid=0)
 	{
 		$this->ChannelInfos = "";
 		$this->ChannelFields = "";
 		$this->AllFieldNames = "";
 		$this->SplitPageField = "";
 		$this->ChannelID = $cid;
 		$this->ArcID = $aid;
 		$this->dsql = new DedeSql(false);
 		$this->ChannelInfos = $this->dsql->GetOne("Select * from #@__channeltype where ID='$cid'");
 		if(!is_array($this->ChannelInfos)){
 			echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>\r\n";
 			echo "<div style='font-size:14px;line-height:150%;margin-left:20px'>";
 			echo "读取频道 {$cid} 信息失败，无法进行后续操作！<br/>\r\n";
 			echo "你可以尝试先对错误文档进行清理，然后再刷新本页。<br/>\r\n";
 			echo "请选择操作： <a href='javascript:location.reload();'>[重试]</a> <a href='archives_clear.php' target='_blank'>[清理错误文档]</a> ";
 			echo "</div>";
 			exit();
 		}
 		$dtp = new DedeTagParse();
 		$dtp->SetNameSpace("field","<",">");
    $dtp->LoadSource($this->ChannelInfos["fieldset"]);
    if(is_array($dtp->CTags))
    {
    	$tnames = Array();
    	foreach($dtp->CTags as $ctag){
    		$tname = $ctag->GetName();
    		if(isset($tnames[$tname]))
    		{ return; }
    		$tnames[$tname] = 1;
    		if($this->AllFieldNames!="") $this->AllFieldNames .= ",".$tname;
    		else $this->AllFieldNames .= $tname;
    		$this->ChannelFields[$tname]["innertext"] = $ctag->GetInnerText();
    		$this->ChannelFields[$tname]["type"] = $ctag->GetAtt("type");
    		$this->ChannelFields[$tname]["default"] = $ctag->GetAtt("default");
    		$this->ChannelFields[$tname]["rename"] = $ctag->GetAtt("rename");
    		$this->ChannelFields[$tname]["function"] = $ctag->GetAtt("function");
    		$this->ChannelFields[$tname]["value"] = "";
    		//----------------------------------------------------------------
    		$this->ChannelFields[$tname]["itemname"] = $ctag->GetAtt("itemname");
    		if($this->ChannelFields[$tname]["itemname"]=="")
    		{ $this->ChannelFields[$tname]["itemname"]=$tname; }
    		$this->ChannelFields[$tname]["isnull"] = $ctag->GetAtt("isnull");
    		$this->ChannelFields[$tname]["maxlength"] = $ctag->GetAtt("maxlength");
    		if($ctag->GetAtt("page")=="split") $this->SplitPageField = $tname;
      }
    }
    $dtp->Clear();
  }
  function ChannelUnit($cid,$aid=0)
	{
		$this->__construct($cid,$aid);
	}
	
	//设置档案ID
	//-----------------------
	function SetArcID($aid)
	{
		$this->ArcID = $aid;
	}
	
	//处理某个字段的值
	//----------------------
	function MakeField($fname,$fvalue,$addvalue="")
	{
		if($fvalue==""){ $fvalue = $this->ChannelFields[$fname]["default"]; }
		$ftype = $this->ChannelFields[$fname]["type"];
		
		//执行函数
		if($this->ChannelFields[$fname]["function"]!=""){
			$fvalue = $this->EvalFunc($fvalue,$this->ChannelFields[$fname]["function"]);
		}
		//处理各种数据类型
		
		if($ftype=="text"||$ftype=="textchar"){
			$fvalue = ClearHtml($fvalue);
		}
		else if($ftype=="multitext"){
			$fvalue = ClearHtml($fvalue);
			$fvalue = Text2Html($fvalue);
		}
		else if($ftype=="img"){
			$fvalue = $this->GetImgLinks($fvalue);
		}
		else if($ftype=="textdata"){
			if(!is_file($GLOBALS['cfg_basedir'].$fvalue)) return "";
			$fp = fopen($GLOBALS['cfg_basedir'].$fvalue,'r');
			$fvalue = "";
			while(!feof($fp)){ $fvalue .= fgets($fp,1024); }
			fclose($fp);
		}
		else if($ftype=="addon"){
			$foldvalue = $fvalue;
			$tempStr = GetSysTemplets("channel/channel_addon.htm");
			$tempStr = str_replace('~phppath~',$GLOBALS['cfg_plus_dir'],$tempStr);
			$tempStr = str_replace('~link~',$foldvalue,$tempStr);
			$fvalue = $tempStr;
		}
		else if($ftype=="softlinks"){
			$fvalue = $this->GetAddLinkPage($fvalue);
		}
		else if($ftype=="specialtopic"){
			$fvalue = $this->GetSpecList($fname,$fvalue);
		}
		return $fvalue;
	}
	//获得专题文章的列表
	//--------------------------------
	function GetSpecList($fname,$noteinfo,$noteid="")
	{
		if(!isset($GLOBALS['__SpGetFullList'])) require_once(dirname(__FILE__)."/inc/inc_fun_SpFullList.php");
		if($noteinfo=="") return "";
		$rvalue = "";
		$tempStr = GetSysTemplets("channel/channel_spec_note.htm");
		$dtp = new DedeTagParse();
		$dtp->LoadSource($noteinfo);
		if(is_array($dtp->CTags))
		{
			foreach($dtp->CTags as $k=>$ctag){
				$notename = $ctag->GetAtt("name");
				if($noteid!="" && $ctag->GetAtt("noteid")!=$noteid){ continue; } //指定名称的专题节点
				$isauto = $ctag->GetAtt("isauto");
				$idlist = trim($ctag->GetAtt("idlist"));
				$rownum = trim($ctag->GetAtt("rownum"));
				if(empty($rownum)) $rownum = 40;
				$keywords = "";
				$stypeid = 0;
				
				if($isauto==1){
				  $idlist = "";
				  $keywords = trim($ctag->GetAtt("keywords"));
				  $stypeid = $ctag->GetAtt("typeid");
			  }
			  
			  //echo "hgdshgdhgdg".$idlist."|";
				
				if(trim($ctag->GetInnerText())!="") $listTemplet = $ctag->GetInnerText();
				else $listTemplet = GetSysTemplets("spec_arclist.htm");
				$idvalue = SpGetFullList($this->dsql,0,-1,$rownum,$ctag->GetAtt("titlelen"),$ctag->GetAtt("infolen"),
   $keywords,$listTemplet,$idlist,'',0,'',$ctag->GetAtt("imgwidth"),$ctag->GetAtt("imgheight"));
				/*
				SpGetArcList(
				        $this->dsql,'spec',$stypeid,$rownum,$ctag->GetAtt("col"),
				        $ctag->GetAtt("titlelen"),$ctag->GetAtt("infolen"),$ctag->GetAtt("imgwidth"),$ctag->GetAtt("imgheight"),
				"all","default",$keywords,$listTemplet,100,0,$idlist,0,"",0,"desc",0,0,'#@__archives',false);
				*/
				$notestr = str_replace("~notename~",$notename,$tempStr);
				$notestr = str_replace("~spec_arclist~",$idvalue,$notestr);
				$rvalue .= $notestr;
				if($noteid!="" && $ctag->GetAtt("noteid")==$noteid){ break; }
			}
		}
		$dtp->Clear();
		return $rvalue;
	}
	
	//获得进入附件下载页面的链接
	//---------------------------------
	function GetAddLinkPage($fvalue)
	{
		$row = $this->dsql->GetOne("Select downtype From #@__softconfig");
		$phppath = $GLOBALS["cfg_plus_dir"];
		$downlinkpage = "";
		if($row['downtype']=='0'){
		   return $this->GetAddLinks($fvalue);
	  }else{
	  	 $tempStr = GetSysTemplets("channel/channel_downlinkpage.htm");
		   $links = $phppath."/download.php?open=0&aid=".$this->ArcID."&cid=".$this->ChannelID;
		   $downlinkpage = str_replace("~link~",$links,$tempStr);
		   return $downlinkpage;
	  }
	}
	
	//获得附件的下载所有链接地址
	//-----------------------------------
	function GetAddLinks($fvalue)
	{
		global $cfg_softinfos;
		if(!is_array($cfg_softinfos)){
			$cfg_softinfos = $this->dsql->GetOne("Select ismoresite,sites,gotojump,showlocal From #@__softconfig");
	  }
		$phppath = $GLOBALS['cfg_phpurl'];
		$downlinks = "";
		$dtp = new DedeTagParse();
    $dtp->LoadSource($fvalue);
    if(!is_array($dtp->CTags)){
    	$dtp->Clear();
    	return "无链接信息！";
    }
    $tempStr = GetSysTemplets("channel/channel_downlinks.htm");
    foreach($dtp->CTags as $ctag)
    {
    	if($ctag->GetName()=="link")
    	{
    	  $links = trim($ctag->GetInnerText());
    	  $serverName = trim($ctag->GetAtt("text"));
    	  if(!isset($firstLink)){ $firstLink = $links; }
    	  if($cfg_softinfos['showlocal']==0 || $cfg_softinfos['ismoresite']!=1)
    	  {
    	     if($cfg_softinfos['gotojump']==1) $links = $phppath."/download.php?open=1&link=".urlencode(base64_encode($links));
    	     $temp = str_replace("~link~",$links,$tempStr);
    	     $temp = str_replace("~server~",$serverName,$temp);
    	     $downlinks .= $temp;
    	  }
      }
    }
    $dtp->Clear();
    //启用镜像功能的情况
    if($cfg_softinfos['ismoresite']==1 && !empty($cfg_softinfos['sites']) && isset($firstLink))
    {
    	if(!empty($GLOBALS['cfg_basehost'])) $firstLink = eregi_replace($GLOBALS['cfg_basehost'],"",$firstLink);
    	
    	$cfg_softinfos['sites'] = ereg_replace("\n{1,}","\n",str_replace("\r","\n",$cfg_softinfos['sites']));
    	$sites = explode("\n",trim($cfg_softinfos['sites']));
    	foreach($sites as $site)
    	{
    		if(trim($site)=='') continue;
    		list($link,$serverName) = explode('|',$site);
    		
    		if(!eregi("^(http|ftp)://",$firstLink)) $flink = trim($link).$firstLink;
    		else $flink = $firstLink;
    		
    		if($cfg_softinfos['gotojump']==1) $flink = $phppath."/download.php?open=1&link=".urlencode(base64_encode($flink));
    	  $temp = str_replace("~link~",$flink,$tempStr);
    	  $temp = str_replace("~server~",$serverName,$temp);
    	  $downlinks .= $temp;
    	}
    }
    return $downlinks;global $cfg_softinfos;
		if(!is_array($cfg_softinfos)){
			$cfg_softinfos = $this->dsql->GetOne("Select ismoresite,sites,gotojump,showlocal From #@__softconfig");
	  }
		$phppath = $GLOBALS['cfg_phpurl'];
		$downlinks = "";
		$dtp = new DedeTagParse();
    $dtp->LoadSource($fvalue);
    if(!is_array($dtp->CTags)){
    	$dtp->Clear();
    	return "无链接信息！";
    }
    $tempStr = GetSysTemplets("channel/channel_downlinks.htm");
    foreach($dtp->CTags as $ctag)
    {
    	if($ctag->GetName()=="link")
    	{
    	  $links = trim($ctag->GetInnerText());
    	  $serverName = trim($ctag->GetAtt("text"));
    	  if(!isset($firstLink)){ $firstLink = $links; }
    	  if($cfg_softinfos['showlocal']==0 || $cfg_softinfos['ismoresite']!=1)
    	  {
    	     if($cfg_softinfos['gotojump']==1) $links = $phppath."/download.php?open=1&link=".urlencode(base64_encode($links));
    	     $temp = str_replace("~link~",$links,$tempStr);
    	     $temp = str_replace("~server~",$serverName,$temp);
    	     $downlinks .= $temp;
    	  }
      }
    }
    $dtp->Clear();
    //启用镜像功能的情况
    if($cfg_softinfos['ismoresite']==1 && !empty($cfg_softinfos['sites']) && isset($firstLink))
    {
    	if(!empty($GLOBALS['cfg_basehost'])) $firstLink = eregi_replace($GLOBALS['cfg_basehost'],"",$firstLink);
    	
    	$cfg_softinfos['sites'] = ereg_replace("\n{1,}","\n",str_replace("\r","\n",$cfg_softinfos['sites']));
    	$sites = explode("\n",trim($cfg_softinfos['sites']));
    	foreach($sites as $site)
    	{
    		if(trim($site)=='') continue;
    		list($link,$serverName) = explode('|',$site);
    		
    		if(!eregi("^(http|ftp)://",$firstLink)) $flink = trim($link).$firstLink;
    		else $flink = $firstLink;
    		
    		if($cfg_softinfos['gotojump']==1) $flink = $phppath."/download.php?open=1&link=".urlencode(base64_encode($flink));
    	  $temp = str_replace("~link~",$flink,$tempStr);
    	  $temp = str_replace("~server~",$serverName,$temp);
    	  $downlinks .= $temp;
    	}
    }
    return $downlinks;
	}
	
	//获得图片的展示页面
	//---------------------------
	function GetImgLinks($fvalue)
	{
		$revalue = "";
		$dtp = new DedeTagParse();
    $dtp->LoadSource($fvalue);
    if(!is_array($dtp->CTags)){
    	$dtp->Clear();
    	return "无图片信息！";
    }
    $ptag = $dtp->GetTag("pagestyle");
    if(is_object($ptag)){
    	$pagestyle = $ptag->GetAtt('value');
    	$maxwidth = $ptag->GetAtt('maxwidth');
    	$ddmaxwidth = $ptag->GetAtt('ddmaxwidth');
    	$irow = $ptag->GetAtt('row');
    	$icol = $ptag->GetAtt('col');
    	if(empty($maxwidth)) $maxwidth = $GLOBALS['cfg_album_width'];
    }else{
    	$pagestyle = 2;
    	$maxwidth = $GLOBALS['cfg_album_width'];
    	$ddmaxwidth = 200;
    }
    if($pagestyle == 3){
      if(empty($irow)) $irow = 4;
      if(empty($icol)) $icol = 4;
    }
    //遍历图片信息
    $mrow = 0;
    $mcol = 0;
    $photoid = 0;
    $images = array();
    
    $sysimgpath = $GLOBALS['cfg_templeturl']."/sysimg";
    foreach($dtp->CTags as $ctag){
    	if($ctag->GetName()=="img"){
    		$iw = $ctag->GetAtt('width');
    		$ih = $ctag->GetAtt('heigth');
    		$alt = str_replace("'","",$ctag->GetAtt('text'));
    		$src = trim($ctag->GetInnerText());
    		$ddimg = $ctag->GetAtt('ddimg');
    		if($iw > $maxwidth) $iw = $maxwidth;
    		$iw = (empty($iw) ? "" : "width='$iw'");
    		//全部列出式或分页式图集
    		if($pagestyle<3){
    		   if($revalue==""){
    			   if($pagestyle==2){
                $playsys = "
			<div class='butbox'>
				<a href='$src' target='_blank' class='c1'>原始图片</a>\r\n
				<a href='javascript:dPlayPre();' class='c1'>上一张</a>\r\n
				<a href='javascript:dPlayNext();' class='c1'>下一张</a>\r\n
				<a href='javascript:dStopPlay();' class='c1'>自动 / 暂停播放</a>\r\n
			</div>\r\n";
    			   	  $revalue = " {$playsys} 
				<div class='imgview'>\r\n
				<center>
				<a href='javascript:dPlayNext();'><img src='$src' alt='$alt'/></a>\r\n
				</center>
				</div>\r\n
				<script language='javascript'>dStartPlay();</script>\r\n";
    		     }
    		     else $revalue = "
				<div class='imgview'>\r\n
				<center>
				<a href='$src' target='_blank'><img src='$src' alt='$alt' /></a>\r\n
				</center>
				</div>\r\n";
    		   }else{
    			   if($pagestyle==2){
    			   	   $playsys = "
			<div class='butbox'>
				<a href='$src' target='_blank' class='c1'>原始图片</a>\r\n
				<a href='javascript:dPlayPre();' class='c1'>上一张</a>\r\n
				<a href='javascript:dPlayNext();' class='c1'>下一张</a>\r\n
				<a href='javascript:dStopPlay();' class='c1'>自动 / 暂停播放</a>\r\n
			</div>\r\n";
    			   	   $revalue .= "#p#分页标题#e# {$playsys}
				<div class='imgview'>\r\n
				<center>
				<a href='javascript:dPlayNext();'><img src='$src' alt='$alt'/></a>\r\n
				</center>
				</div>\r\n
				<script language='javascript'>dStartPlay();</script>\r\n";
    			   }
    			   else $revalue .= "
				<div class='imgview'>\r\n
				<center>
				<a href='$src' target='_blank'><img src='$src' alt='$alt' /></a>\r\n
				</center>
				</div>\r\n";
    		   }
    		//多列式图集
    		}else if($pagestyle==3){
    			$images[$photoid][0] = $src;
    			$images[$photoid][1] = $alt;
    			$images[$photoid][2] = $ddimg;
    			$photoid++;
    		}
      }
    }
    //重新运算多列式图集
    if($pagestyle==3){
    	if(empty($ddmaxwidth)) $ddmaxwidth = 200;
    	$picnum = count($images);
    	$sPos = 0;
    	if($icol==0) $icol = 1;
    	$tdwidth = ceil(100 / $icol);
    	while($sPos < $picnum){
    		for($i=0;$i < $irow;$i++){
    			//$revalue .= "<ul class='imgline'>\r\n";
    			for($j=0;$j < $icol;$j++){
    				if(!isset($images[$sPos])){ $revalue .= ""; }
    				else{
    					$src = $images[$sPos][0];
    					$alt = $images[$sPos][1];
    					$litsrc = $images[$sPos][2];
    					$tpwidth = $ddmaxwidth;
    					if($litsrc==''){
    						$litsrc = $src;
    						$tpwidth = '';
    					}else{
    						$tpwidth = " width='$tpwidth'";
    					}
						//多行多列imgurls标签生成代码
    					$revalue .= "<dl>\r\n
						<dt><a href='{$GLOBALS['cfg_phpurl']}/showphoto.php?aid={$this->ArcID}&src=".urlencode($src)."&npos=$sPos' target='_blank'><img src='$litsrc' alt='$alt'{$tpwidth} border='0'/></a></dt>\r\n
						<dd class='title'><img src='/templets/images/ico_15.gif' /><a href='{$GLOBALS['cfg_phpurl']}/showphoto.php?aid={$this->ArcID}&src=".urlencode($src)."&npos=$sPos' target='_blank'>$alt</a></dd>\r\n
						</dl>\r\n";
    					$sPos++;
    				}
    			}
    			//$revalue .= "</ul>\r\n";
    			if(!isset($images[$sPos])) break;
    		}
    		if(!isset($images[$sPos])){
    			break;
    		}else{
    			$revalue .= "#p#分页标题#e#";
    		}
    	}
    }
    unset($dtp);
    unset($images);
    return $revalue;
	}
	
	//处理引用的函数的字段
	//-----------------------------
	function EvalFunc($fvalue,$functionname)
	{
		$DedeMeValue = $fvalue;
		$phpcode = preg_replace("/'@me'|\"@me\"|@me/isU",'$DedeMeValue',$functionname);
		eval($phpcode.";");
		return $DedeMeValue;
	}
	
	//关闭所占用的资源
 	//------------------
 	function Close(){
 		$this->dsql->Close();
 	}
	
}//End  class ChannelUnit
?>