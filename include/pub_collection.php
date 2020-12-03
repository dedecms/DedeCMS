<?php 
/*------------------------
DedeCms在线采集程序V2
作者：IT柏拉图  
开发时间 2006年9月 最后更改时间 2007-1-17
-----------------------*/
require_once(dirname(__FILE__)."/pub_httpdown.php");
require_once(dirname(__FILE__)."/pub_dedetag.php");
require_once(dirname(__FILE__)."/pub_db_mysql.php");
require_once(dirname(__FILE__)."/pub_charset.php");
require_once(dirname(__FILE__)."/pub_collection_functions.php"); //采集扩展函数
require_once(dirname(__FILE__)."/inc_photograph.php");
require_once(dirname(__FILE__)."/pub_dedehtml2.php");
@set_time_limit(0);
class DedeCollection
{
	var $Item = array(); //采集节点的基本配置信息
	var $List = array(); //采集节点的来源列表处理信息
	var $Art = array();  //采集节点的文章处理信息
	var $ArtNote = array(); //文章采集的字段信息
	var $dsql = "";
	var $NoteId = "";
	var $CDedeHtml = "";
	var $CHttpDown = "";
	var $MediaCount = 0;
	var $tmpUnitValue = "";
	var $tmpLinks = array();
	var $tmpHtml = "";
	var $breImage = "";
	//-------------------------------
	//兼容php5构造函数
	//-------------------------------
	function __construct(){
 		 $this->dsql = new DedeSql(false);
		 $this->CHttpDown = new DedeHttpDown();
		 $this->CDedeHtml = new DedeHtml2();
  }
	function DedeCollection(){
		 $this->__construct();
	}
	function Init(){
		//仅兼容性函数
	}
	//析放资源
	//---------------------------
	function Close(){
		 $this->dsql->Close();
		 unset($this->Item);
	   unset($this->List);
	   unset($this->Art);
	   unset($this->ArtNote);
	   unset($this->tmpLinks);
	   unset($this->dsql);
	   unset($this->CDedeHtml);
	   unset($this->CHttpDown);
	   unset($this->tmpUnitValue);
	   unset($this->tmpHtml);
	}
	//-------------------------------
	//从数据库里载入某个节点
	//-------------------------------
	function LoadNote($nid)
	{
		$this->NoteId = $nid;
		$this->dsql->SetSql("Select * from #@__conote where nid='$nid'");
		$this->dsql->Execute();
		$row = $this->dsql->Getarray();
		$this->LoadConfig($row["noteinfo"]);
		$this->dsql->FreeResult();
	}
	//-------------------------------
	//从数据库里载入某个节点
	//-------------------------------
	function LoadFromDB($nid)
	{
		$this->NoteId = $nid;
		$this->dsql->SetSql("Select * from #@__conote where nid='$nid'");
		$this->dsql->Execute();
		$row = $this->dsql->GetArray();
		$this->LoadConfig($row["noteinfo"]);
		$this->dsql->FreeResult();
	}
	//----------------------------
	//分析节点的配置信息
	//----------------------------
	function LoadConfig($configString)
	{
		$dtp = new DedeTagParse();
		$dtp->SetNameSpace("dede","{","}");
		$dtp2 = new DedeTagParse();
		$dtp2->SetNameSpace("dede","{","}");
		$dtp3 = new DedeTagParse();
		$dtp3->SetNameSpace("dede","{","}");
		$dtp->LoadString($configString);
		for($i=0;$i<=$dtp->Count;$i++)
		{
			$ctag = $dtp->CTags[$i];
			//item 配置
			//节点基本信息
			if($ctag->GetName()=="item")
			{
				$this->Item["name"] = $ctag->GetAtt("name");
				$this->Item["typeid"] = $ctag->GetAtt("typeid");
				$this->Item["imgurl"] = $ctag->GetAtt("imgurl");
				$this->Item["imgdir"] = $ctag->GetAtt("imgdir");
				$this->Item["language"] = $ctag->GetAtt("language");
				$this->Item["matchtype"] = $ctag->GetAtt("matchtype");
				$this->Item["isref"] = $ctag->GetAtt("isref");
				$this->Item["refurl"] = $ctag->GetAtt("refurl");
				$this->Item["exptime"] = $ctag->GetAtt("exptime"); 
				if($this->Item["matchtype"]=="") $this->Item["matchtype"]="string";
				//创建图片保存目录
				$updir = dirname(__FILE__)."/".$this->Item["imgdir"]."/";
				$updir = str_replace("\\","/",$updir);
				$updir = preg_replace("/\/{1,}/","/",$updir);
				if(!is_dir($updir)) MkdirAll($updir,777);
			}
			//list 配置
			//要采集的列表页的信息
			else if($ctag->GetName()=="list")
			{
				$this->List["varstart"]= $ctag->GetAtt("varstart");
				$this->List["varend"] = $ctag->GetAtt("varend");
				$this->List["source"] = $ctag->GetAtt("source");
				$this->List["sourcetype"] = $ctag->GetAtt("sourcetype");
				$dtp2->LoadString($ctag->GetInnerText());
				for($j=0;$j<=$dtp2->Count;$j++)
				{
					$ctag2 = $dtp2->CTags[$j];
					$tname = $ctag2->GetName();
					if($tname=="need"){
						$this->List["need"] = trim($ctag2->GetInnerText());
					}else if($tname=="cannot"){
						$this->List["cannot"] = trim($ctag2->GetInnerText());
					}
					else if($tname=="linkarea"){
						$this->List["linkarea"] = trim($ctag2->GetInnerText());
				  }else if($tname=="url")
					{
						$gurl = trim($ctag2->GetAtt("value"));
						//手工指定列表网址
						if($this->List["source"]=="app")
						{
							$turl = trim($ctag2->GetInnerText());
							$turls = explode("\n",$turl);
							$l_tj = 0;
							foreach($turls as $turl){
								$turl = trim($turl);
								if($turl=="") continue;
								if(!eregi("^http://",$turl)) $turl = "http://".$turl;
								$this->List["url"][$l_tj] = $turl;
								$l_tj++;
							}
						}
						//用分页变量产生的网址
						else
						{	
							if(eregi("var:分页",trim($ctag2->GetAtt("value")))){
								if($this->List["varstart"]=="") $this->List["varstart"]=1;
								if($this->List["varend"]=="") $this->List["varend"]=10;
								$l_tj = 0;
								for($l_em = $this->List["varstart"];$l_em<=$this->List["varend"];$l_em++){
										$this->List["url"][$l_tj] = str_replace("[var:分页]",$l_em,$gurl);
										$l_tj++;
								}
							}//if set var
							else{
								$this->List["url"][0] = $gurl;
							}
						}
					}
				}//End inner Loop1
			}
			//art 配置
			//要采集的文章页的信息
			else if($ctag->GetName()=="art")
			{
				$dtp2->LoadString($ctag->GetInnerText());
				for($j=0;$j<=$dtp2->Count;$j++)
				{
					$ctag2 = $dtp2->CTags[$j];
					//文章要采集的字段的信息及处理方式
					if($ctag2->GetName()=="note"){
						$field = $ctag2->GetAtt('field');
						if($field == "") continue;
						$this->ArtNote[$field]["value"] = $ctag2->GetAtt('value');
						$this->ArtNote[$field]["isunit"] = $ctag2->GetAtt('isunit');
						$this->ArtNote[$field]["isdown"] = $ctag2->GetAtt('isdown');
						$dtp3->LoadString($ctag2->GetInnerText());
						for($k=0;$k<=$dtp3->Count;$k++)
						{
							$ctag3 = $dtp3->CTags[$k];
							if($ctag3->GetName()=="trim"){
								$this->ArtNote[$field]["trim"][] = $ctag3->GetInnerText();
							}
							else if($ctag3->GetName()=="match"){
								$this->ArtNote[$field]["match"] = $ctag3->GetInnerText();
							}
							else if($ctag3->GetName()=="function"){
								$this->ArtNote[$field]["function"] = $ctag3->GetInnerText();
							}
						}
					}
					else if($ctag2->GetName()=="sppage"){
						$this->ArtNote["sppage"] = $ctag2->GetInnerText();
						$this->ArtNote["sptype"] = $ctag2->GetAtt('sptype');
					}
				}//End inner Loop2
			}
		}//End Loop
		$dtp->Clear();
		$dtp2->Clear();
	}
	//-----------------------------
	//下载其中一个网址，并保存
	//-----------------------------
	function DownUrl($aid,$dourl)
	{
		$this->tmpLinks = array();
	  $this->tmpUnitValue = "";
	  $this->tmpHtml = "";
	  $this->breImage = "";
		$GLOBALS['RfUrl'] = $dourl;
		$html = $this->DownOnePage($dourl);
		$this->tmpHtml = $html;
		//检测是否有分页字段，并预先处理
		if(!empty($this->ArtNote["sppage"])){
		  $noteid = "";
		  foreach($this->ArtNote as $k=>$sarr){
			  if($sarr["isunit"]==1){ $noteid = $k; break;}
		  }
		  $this->GetSpPage($dourl,$noteid,$html);
		}
		//分析所有内容，并保存
		$body = addslashes($this->GetPageFields($dourl,true));
		$query = "Update #@__courl set dtime='".mytime()."',result='$body',isdown='1' where aid='$aid'";
		$this->dsql->SetSql($query);
		if(!$this->dsql->ExecuteNoneQuery()){
			echo $this->dsql->GetError();
		}
		unset($body);
		unset($query);
		unset($html);
	}
	//------------------------
	//获取分页区域的内容
	//------------------------
	function GetSpPage($dourl,$noteid,&$html,$step=0){
		 $sarr = $this->ArtNote[$noteid];
		 $linkareaHtml = $this->GetHtmlArea("[var:分页区域]",$this->ArtNote["sppage"],$html);
		 if($linkareaHtml==""){
		 	  if($this->tmpUnitValue=="") $this->tmpUnitValue .= $this->GetHtmlArea("[var:内容]",$sarr["match"],$html);
		 	  else $this->tmpUnitValue .= "#p#副标题#e#".$this->GetHtmlArea("[var:内容]",$sarr["match"],$html);
		    return;
		 }
		 //完整的分页列表
		 if($this->ArtNote["sptype"]=="full"||$this->ArtNote["sptype"]==""){
		 	  $this->tmpUnitValue .= $this->GetHtmlArea("[var:内容]",$sarr["match"],$html);
		 	  $this->CDedeHtml->GetLinkType = "link";
				$this->CDedeHtml->SetSource($linkareaHtml,$dourl,false);
				foreach($this->CDedeHtml->Links as $k=>$t){
					$k = $this->CDedeHtml->FillUrl($k);
					if($k==$dourl) continue;
					$nhtml = $this->DownOnePage($k);
					if($nhtml!=""){ 
						$this->tmpUnitValue .= "#p#副标题#e#".$this->GetHtmlArea("[var:内容]",$sarr["match"],$nhtml);
					}
			  }
		 }
		 //上下页形式或不完整的分页列表
		 else{
		 	  if($step>50) return;
		 	  if($step==0) $this->tmpUnitValue .= "#e#".$this->GetHtmlArea("[var:内容]",$sarr["match"],$html);
		 	  $this->CDedeHtml->GetLinkType = "link";
				$this->CDedeHtml->SetSource($linkareaHtml,$dourl,false);
				$hasLink = false;
				foreach($this->CDedeHtml->Links as $k=>$t){
					$k = $this->CDedeHtml->FillUrl($k);
					if(in_array($k,$this->tmpLinks)) continue;
					else{
						$nhtml = $this->DownOnePage($k);
					  if($nhtml!=""){ 
						  $this->tmpUnitValue .= "#p#副标题#e#".$this->GetHtmlArea("[var:内容]",$sarr["match"],$nhtml);
					  }
					  $hasLink = true;
					  $this->tmpLinks[] = $k;
					  $dourl = $k;
					  $step++;
					}
			  }
			  if($hasLink) $this->GetSpPage($dourl,$noteid,$nhtml,$step);
		 } 
	}
	//-----------------------
	//获取特定区域的HTML
	//-----------------------
	function GetHtmlArea($sptag,&$areaRule,&$html){
	  //用正则表达式的模式匹配
	  if($this->Item["matchtype"]=="regex"){
	     $areaRule = str_replace("/","\\/",$areaRule);
	     $areaRules = explode($sptag,$areaRule);
	     $arr = array();
	     if($html==""||$areaRules[0]==""){ return ""; }
       preg_match("/".$areaRules[0]."(.*)".$areaRules[1]."/isU",$html,$arr);
       if(!empty($arr[1])){ return trim($arr[1]); }
       else{ return ""; }
	  //用字符串模式匹配
	  }else{
	  	 $areaRules = explode($sptag,$areaRule);
	  	 if($html==""||$areaRules[0]==""){ return ""; }
	  	 $posstart = @strpos($html,$areaRules[0]);
	  	 if($posstart===false){ return ""; }
	  	 $posend = strpos($html,$areaRules[1],$posstart);
	  	 if($posend > $posstart && $posend!==false){
	  	 	 return substr($html,$posstart+strlen($areaRules[0]),$posend-$posstart-strlen($areaRules[0]));
	  	 }else{
	  	 	 return "";
	  	 }
	  }
	}
	//--------------------------
	//下载指定网址
	//--------------------------
	function DownOnePage($dourl){
		$this->CHttpDown->OpenUrl($dourl);
		$html = $this->CHttpDown->GetHtml();
		$this->CHttpDown->Close();
		$this->ChangeCode($html);
		return $html;
	}
	//---------------------
	//下载特定资源，并保存为指定文件
	//---------------------
	function DownMedia($dourl,$mtype='img'){
		//检测是否已经下载此文件
		$isError = false;
		$errfile = $GLOBALS['cfg_phpurl'].'/img/etag.gif';
		$row = $this->dsql->GetOne("Select nurl from #@__co_mediaurl where rurl like '$dourl'");
		$wi = false;
		if(!empty($row['nurl'])){
			$filename = $row['nurl'];
			return $filename;
		}else{
		   //如果不存在，下载该文件
		   $filename = $this->GetRndName($dourl,$mtype);
		   if(!ereg("^/",$filename)) $filename = "/".$filename;
		   
		   //反盗链模式
		   if($this->Item["isref"]=='yes' && $this->Item["refurl"]!=''){
		      if($this->Item["exptime"]=='') $this->Item["exptime"] = 10;
		      $rs = DownImageKeep($dourl,$this->Item["refurl"],$GLOBALS['cfg_basedir'].$filename,"",0,$this->Item["exptime"]);
		      if($rs){
		         $inquery = "INSERT INTO #@__co_mediaurl(nid,rurl,nurl) VALUES ('".$this->NoteId."', '".addslashes($dourl)."', '".addslashes($filename)."');";
		         $this->dsql->ExecuteNoneQuery($inquery);
		      }else{
		      	$inquery = "INSERT INTO #@__co_mediaurl(nid,rurl,nurl) VALUES ('".$this->NoteId."', '".addslashes($dourl)."', '".addslashes($errfile)."');";
		        $this->dsql->ExecuteNoneQuery($inquery);
		      	$isError = true;
		      }
		      if($mtype=='img'){ $wi = true; }
	     //常规模式
	     }else{
		      $this->CHttpDown->OpenUrl($dourl);
		      $this->CHttpDown->SaveToBin($GLOBALS['cfg_basedir'].$filename);
		      $inquery = "INSERT INTO #@__co_mediaurl(nid,rurl,nurl) VALUES ('".$this->NoteId."', '".addslashes($dourl)."', '".addslashes($filename)."');";
		      $this->dsql->ExecuteNoneQuery($inquery);
		      if($mtype=='img'){ $wi = true; }
	        $this->CHttpDown->Close();
	     }
	  }
	  //生成缩略图
	  if($mtype=='img' && $this->breImage=='' && !$isError){
	  	$this->breImage = $filename;
	  	if(!eregi("^http://",$this->breImage) && file_exists($GLOBALS['cfg_basedir'].$filename)){
	  		$filenames = explode('/',$filename);
	  		$filenamed = $filenames[count($filenames)-1];
	  		$nfilename = "lit_".$filenamed;
	  		$nfilename = str_replace($filenamed,$nfilename,$filename);
	  		if(file_exists($GLOBALS['cfg_basedir'].$nfilename)){
	  			$this->breImage = $nfilename;
	  	  }else if(copy($GLOBALS['cfg_basedir'].$filename,$GLOBALS['cfg_basedir'].$nfilename)){
	  			ImageResize($GLOBALS['cfg_basedir'].$nfilename,$GLOBALS['cfg_ddimg_width'],$GLOBALS['cfg_ddimg_height']);
	  			$this->breImage = $nfilename;
	  		}
	    }
	  }
	  if($wi && !$isError) @WaterImg($GLOBALS['cfg_basedir'].$filename,'up');
		if(!$isError) return $filename;
		else return $errfile;
	}
	//------------------------------
	//获得下载媒体的随机名称
	//------------------------------
	function GetRndName($url,$v)
	{
		$this->MediaCount++;
		$mnum = $this->MediaCount;
		$timedir = strftime("%y%m%d",mytime());
		//存放路径
		$fullurl = preg_replace("/\/{1,}/","/",$this->Item["imgurl"]."/");
		if(!is_dir($GLOBALS['cfg_basedir']."/$fullurl")) MkdirAll($GLOBALS['cfg_basedir']."/$fullurl",777);
		$fullurl = $fullurl.$timedir."/";
		if(!is_dir($GLOBALS['cfg_basedir']."/$fullurl")) MkdirAll($GLOBALS['cfg_basedir']."/$fullurl",777);
		//文件名称
		$timename = str_replace(".","",ExecTime());
		$threadnum = 0;
		if(isset($_GET["threadnum"])) $threadnum = $_GET["threadnum"];
		$filename = $timename.$threadnum.$mnum.mt_rand(1000,9999);
		//把适合的数字转为字母
		$filename = dd2char($filename);
		//分配扩展名
		$urls = explode(".",$url);
		if($v=="img"){
			$shortname = ".jpg";
			if(eregi("\.gif\?(.*)$",$url) || eregi("\.gif$",$url)) $shortname = ".gif";
			else if(eregi("\.png\?(.*)$",$url) || eregi("\.png$",$url)) $shortname = ".png";
		}
		else if($v=="embed") $shortname = ".swf";
		else $shortname = "";
		//-----------------------------------------
		$fullname = $fullurl.$filename.$shortname;
		return preg_replace("/\/{1,}/","/",$fullname);
	}
	//------------------------------------------------
	//按载入的网页内容获取规则，从一个HTML文件中获取内容
	//-------------------------------------------------
	function GetPageFields($dourl,$needDown)
	{
		if($this->tmpHtml == "") return "";
		$artitem = "";
		$isPutUnit = false;
		$tmpLtKeys = array();
		foreach($this->ArtNote as $k=>$sarr)
		{
			 //可能出现意外的情况
			 if($k=="sppage"||$k=="sptype") continue;
			 if(!is_array($sarr)) continue;
		   //特殊的规则或没匹配选项
		   if($sarr['match']==''||trim($sarr['match'])=='[var:内容]'
		   ||$sarr['value']!='[var:内容]'){
		     if($sarr['value']!='[var:内容]') $v = $sarr['value'];
		     else $v = "";
		   }
		   else //需匹配的情况
		   {
		      //分多页的内容
		      if($this->tmpUnitValue!="" && !$isPutUnit && $sarr["isunit"]==1){ 
					    $v = $this->tmpUnitValue;
					    $isPutUnit = true;
			    //其它内容
			    }else{
			        $v = $this->GetHtmlArea("[var:内容]",$sarr["match"],$this->tmpHtml);
			    }
		      //过滤内容规则
			    if(isset($sarr["trim"]) && $v!=""){
				     foreach($sarr["trim"] as $nv){
					      if($nv=="") continue;
					      $nv = str_replace("/","\\/",$nv);
					      $v = preg_replace("/$nv/isU","",$v);
				     }
			    }
			    //是否下载远程资源
			    if($needDown){
			    	if($sarr["isdown"] == '1'){ $v = $this->DownMedias($v,$dourl); }
			    }
			    else{
			    	if($sarr["isdown"] == '1') $v = $this->MediasReplace($v,$dourl);
			    }
			}
			//用户自行对内容进行处理的接口
			if($sarr["function"]!=""){
				 if(!eregi('@litpic',$sarr["function"])){
				 	  $v = $this->RunPHP($v,$sarr["function"]);
				 	  $artitem .= "{dede:field name='$k'}$v{/dede:field}\r\n";
				 }else{
				   $tmpLtKeys[$k]['v'] = $v;
				   $tmpLtKeys[$k]['f'] = $sarr["function"];
				 }
			}else{
			   $artitem .= "{dede:field name='$k'}$v{/dede:field}\r\n";
			}
	  }//End Foreach
	  //处理带缩略图变量的项目
	  foreach($tmpLtKeys as $k=>$sarr){
	  	$v = $this->RunPHP($sarr['v'],$sarr['f']);
			$artitem .= "{dede:field name='$k'}$v{/dede:field}\r\n";
	  }
		return $artitem;
	}
	//----------------------------------
	//下载内容里的资源
	//----------------------------------
	function DownMedias(&$html,$url)
	{
		$this->CDedeHtml->GetLinkType = "media";
		$this->CDedeHtml->SetSource($html,$url,false);
		//下载img标记里的图片
		foreach($this->CDedeHtml->Medias as $k=>$v){
			$furl = $this->CDedeHtml->FillUrl($k);
			if($v=="embed" && !eregi("\.(swf)\?(.*)$",$k)&& !eregi("\.(swf)$",$k)){ continue; }
			$okurl = $this->DownMedia($furl,$v);
			$html = str_replace($k,$okurl,$html);
		}
		//下载超链接里的图片
		foreach($this->CDedeHtml->Links as $v=>$k){
			 if(eregi("\.(jpg|gif|png)\?(.*)$",$v) || eregi("\.(jpg|gif|png)$",$v)){ $m = "img"; }
			 else if(eregi("\.(swf)\?(.*)$",$v) || eregi("\.(swf)$",$v)){ $m = "embed"; }
			 else continue;
			 $furl = $this->CDedeHtml->FillUrl($v);
			 $okurl = $this->DownMedia($furl,$m);
			 $html = str_replace($v,$okurl,$html);
		}
		return $html;
	}
	//---------------------------------
	//仅替换内容里的资源为绝对网址
	//----------------------------------
	function MediasReplace(&$html,$dourl)
	{
		$this->CDedeHtml->GetLinkType = "media";
		$this->CDedeHtml->SetSource($html,$dourl,false);
		foreach($this->CDedeHtml->Medias as $k=>$v)
		{
			$k = trim($k);
			if(!eregi("^http://",$k)){
				$okurl = $this->CDedeHtml->FillUrl($k);
				$html = str_replace($k,$okurl,$html);
			}
		}
		return $html;
	}
	//---------------------
	//测试列表
	//---------------------
	function TestList()
	{
		if(isset($this->List["url"][0])) $dourl = $this->List["url"][0];
		else{
				echo "配置中指定列表的网址错误!\r\n";
	  		return ;
		}
		if($this->List["sourcetype"]=="archives")
		{
			echo "配置中指定的源参数为文档的原始URL：\r\n";
			$i=0;
			$v = "";
			foreach($this->List["url"] as $v){
				echo $v."\r\n"; $i++; if($i>9) break;
			}
			return $v;
		}
		$dhtml = new DedeHtml2();
		$html = $this->DownOnePage($dourl);
		if($html==""){
			echo "读取其中的一个网址： $dourl 时失败！\r\n";
			return ;
		}
		if(trim($this->List["linkarea"])!=""&&trim($this->List["linkarea"])!="[var:区域]"){
			$html = $this->GetHtmlArea("[var:区域]",$this->List["linkarea"],$html);
		}
		
		$dhtml->GetLinkType = "link";
		$dhtml->SetSource($html,$dourl,false);
		
		$testpage = "";
		$TestPage = "";
		
		if(is_array($dhtml->Links))
		{
			echo "按指定规则在 $dourl 发现的网址：\r\n";
			echo $this->List["need"];
			foreach($dhtml->Links as $k=>$v)
			{
				$k =  $dhtml->FillUrl($k);
				if($this->List["need"]!="")
				{
					if(eregi($this->List["need"],$k))
					{
						if($this->List["cannot"]==""
						||!eregi($this->List["cannot"],$k)){
							echo "$k - ".$v."\r\n";
							$TestPage = $k;
						}
					}//eg1
				}else{
					echo "$k - ".$v."\r\n";
					$TestPage = $k;
				}
			}//foreach
		}else{
			echo "分析网页的HTML时失败！\r\n";
			return ;
		}
		return $TestPage;
	}
	//测试文章规则
	function TestArt($dourl)
	{
		if($dourl==""){
			 echo "没有递交测试的网址！";
			 exit();
		}
		$this->tmpHtml = $this->DownOnePage($dourl);
		echo $this->GetPageFields($dourl,false);
	}
	//--------------------------------
	//采集种子网址
	//--------------------------------
	function GetSourceUrl($downall=0,$glstart=0,$pagesize=10)
	{
		if($downall==1 && $glstart==0){
		  $this->dsql->ExecuteNoneQuery("Delete From #@__courl where nid='".$this->NoteId."'");
		  $this->dsql->ExecuteNoneQuery("Delete From #@__co_listenurl where nid='".$this->NoteId."'");
		}
		if($this->List["sourcetype"]=="archives")
		{
			echo "配置中指定的源参数为文档的原始URL：<br/>处理中...<br/>\r\n";
			foreach($this->List["url"] as $v)
			{
				if($downall==0){
					$lrow = $this->dsql->GetOne("Select * From #@__co_listenurl where url like '".addslashes($v)."'");
	        if(is_array($lrow)) continue;
				}
				$inquery = "INSERT INTO #@__courl(nid,title,url,dtime,isdown,result) 
         VALUES ('".$this->NoteId."','用户手工指定的网址','$v','".mytime()."','0','');";
				$this->dsql->ExecuteNoneQuery($inquery);
			}
			echo "完成种子网址的处理！<br/>\r\n";
			return 0;
		}
		$tmplink = array();
		$arrStart = 0;
		$moviePostion = 0;
		$endpos = $glstart + $pagesize; 
		$totallen = count($this->List["url"]);
		foreach($this->List["url"] as $k=>$v)
		{
			$moviePostion++;
		 if($moviePostion > $endpos) break;
	   if($moviePostion > $glstart)
	   {
			  $html = $this->DownOnePage($v);
			
			  if(trim($this->List["linkarea"])!=""&&trim($this->List["linkarea"])!="[var:区域]"){
			     $html = $this->GetHtmlArea("[var:区域]",$this->List["linkarea"],$html);
		    }
		  
			  $this->CDedeHtml->GetLinkType = "link";
			  $this->CDedeHtml->SetSource($html,$v,false);
		  
		    foreach($this->CDedeHtml->Links as $k=>$v)
		    {
		  	  $k = $this->CDedeHtml->FillUrl($k);
		  	  if($this->List["need"]!=""){
					  if(eregi($this->List["need"],$k)){
						  if($this->List["cannot"]==""){	
							  $tmplink[$arrStart][0] = $this->CDedeHtml->FillUrl($k);
							  $tmplink[$arrStart][1] = $v; 
							  $arrStart++;
						  }
						  else if(!eregi($this->List["cannot"],$k)){
							  $tmplink[$arrStart][0] = $this->CDedeHtml->FillUrl($k);
							  $tmplink[$arrStart][1] = $v; 
							  $arrStart++;
						  }
					  }
				  }else{
					  $tmplink[$arrStart][0] = $this->CDedeHtml->FillUrl($k);
					  $tmplink[$arrStart][1] = $v; 
					  $arrStart++;
				  }
		    }
		    $this->CDedeHtml->Clear();
		  }//在位置内
		}//foreach
		krsort($tmplink);
		$unum = count($tmplink);
		if($unum>0){
		  //echo "完成本次种子网址抓取，共找到：{$unum} 个记录!<br/>\r\n";
		  $this->dsql->ExecuteNoneQuery();
		  foreach($tmplink as $v)
			{
				$k = addslashes($v[0]);
				$v = addslashes($v[1]);
				if($downall==0){
					$lrow = $this->dsql->GetOne("Select * From #@__co_listenurl where url like '$v' ");
	        if(is_array($lrow)) continue;
				}
				if($v=="") $v="无标题，可能是图片链接";
				$inquery = "
				INSERT INTO #@__courl(nid,title,url,dtime,isdown,result) 
         VALUES ('".$this->NoteId."','$v','$k','".mytime()."','0','');
				";
				$this->dsql->ExecuteNoneQuery($inquery);
			}
			if($endpos >= $totallen) return 0;
			else return ($totallen-$endpos);
	  }
	  else{
	  	echo "按指定规则没找到任何链接！";
	  	return -1;
	  }
	  return -1;
	}
	//---------------------------------
	//用扩展函数处理采集到的原始数据
	//-------------------------------
	function RunPHP($fvalue,$phpcode)
	{
		$DedeMeValue = $fvalue;
		$phpcode = preg_replace("/'@me'|\"@me\"|@me/isU",'$DedeMeValue',$phpcode);
		if(eregi('@body',$phpcode)){
			$DedeBodyValue = $this->tmpHtml;
			$phpcode = preg_replace("/'@body'|\"@body\"|@body/isU",'$DedeBodyValue',$phpcode);
		}
		if(eregi('@litpic',$phpcode)){
			$DedeLitPicValue = $this->breImage;
			$phpcode = preg_replace("/'@litpic'|\"@litpic\"|@litpic/isU",'$DedeLitPicValue',$phpcode);
		}
		@eval($phpcode.";");
		return $DedeMeValue;
	}
	//-----------------------
	//编码转换
	//-----------------------
	function ChangeCode(&$str)
	{
		if($this->Item["language"]=="utf-8") $str = utf82gb($str);
		if($this->Item["language"]=="big5") $str = big52gb($str);
	}
}
?>