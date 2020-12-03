<?
require_once(dirname(__FILE__)."/pub_httpdown.php");
require_once(dirname(__FILE__)."/pub_dedetag.php");
require_once(dirname(__FILE__)."/pub_dedehtml2.php");
require_once(dirname(__FILE__)."/pub_db_mysql.php");
require_once(dirname(__FILE__)."/pub_charset.php");
require_once(dirname(__FILE__)."/pub_collection_functions.php"); //采集扩展函数
@set_time_limit(0);
class DedeCollection
{
	var $Item = Array(); //采集节点的基本配置信息
	var $List = Array(); //采集节点的来源列表处理信息
	var $Art = Array();  //采集节点的文章处理信息
	var $ArtNote = Array(); //文章采集的字段信息
	var $TypeEnums = Array(); //类别对应的枚举
	var $dsql = "";
	var $NoteId = "";
	var $CDedeHtml = "";
	var $CHttpDown = "";
	var $MediaCount = 0;
	var $tmpUnitValue = "";
	//---------------------------
	//初始化
	//---------------------------
	function Init(){
		$this->dsql = new DedeSql(false);
	}
	//---------------------------
	//析放资源
	//---------------------------
	function Close(){
		$this->dsql->Close();
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
	//载入节点的配置信息
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
		for($i=0;$i<$dtp->GetCount();$i++)
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
				//创建图片保存目录
				$updir = dirname(__FILE__)."/".$this->Item["imgdir"]."/";
				$updir = str_replace("\\","/",$updir);
				$updir = preg_replace("/\/{1,}/","/",$updir);
				if(!is_dir($updir)) mkdir($updir,0755);
			}
			//list 配置
			//要采集的列表页的信息
			else if($ctag->GetName()=="list")
			{
				$this->List["source"] = $ctag->GetAtt("source");
				$this->List["varstart"]= $ctag->GetAtt("varstart");
				$this->List["varend"] = $ctag->GetAtt("varend");
				$this->List["sourcetype"] = $ctag->GetAtt("sourcetype");
				$dtp2->LoadString($ctag->GetInnerText());
				for($j=0;$j<$dtp2->GetCount();$j++)
				{
					$ctag2 = $dtp2->CTags[$j];
					if($ctag2->GetName()=="need"){
						$this->List["need"] = trim($ctag2->GetInnerText());
					}
					else if($ctag2->GetName()=="cannot"){
						$this->List["cannot"] = trim($ctag2->GetInnerText());
					}
					else if($ctag2->GetName()=="url")
					{
						$gurl = trim($ctag2->GetAtt("value"));
						if($this->List["source"]=="app")
						{
							$turl = trim($ctag2->GetInnerText());
							$turls = explode("\n",$turl);
							$l_tj = 0;
							foreach($turls as $turl){
								$turl = trim($turl);
								if($turl=="") continue;
								else{
									if(!eregi("^http://",$turl)) $turl = "http://".$turl;
									$this->List["url"][$l_tj] = $turl;
									$l_tj++;
								}
							}
						}
						else
						{	
							if(eregi("var:分页",trim($ctag2->GetAtt("value")))){
								if($this->List["varstart"]>0&&$this->List["varend"]>0){
									$l_tj = 0;
									for($l_em = $this->List["varstart"];$l_em<=$this->List["varend"];$l_em++){
										$this->List["url"][$l_tj] = str_replace("[var:分页]",$l_em,$gurl);
										$l_tj++;
									}
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
				for($j=0;$j<$dtp2->GetCount();$j++)
				{
					$ctag2 = $dtp2->CTags[$j];
					//文章要采集的字段的信息及处理方式
					if($ctag2->GetName()=="note"){
						$field = $ctag2->GetAtt("field");
						if($field == "") continue;
						$this->ArtNote[$field]["value"] = $ctag2->GetAtt("value");
						$this->ArtNote[$field]["isunit"] = $ctag2->GetAtt("isunit");
						$this->ArtNote[$field]["isdown"] = $ctag2->GetAtt("isdown");
						$this->ArtNote[$field]["function"] = $ctag2->GetAtt("function");
						$dtp3->LoadString($ctag2->GetInnerText());
						for($k=0;$k<$dtp3->GetCount();$k++)
						{
							$ctag3 = $dtp3->CTags[$k];
							if($ctag3->GetName()=="trim")
								$this->ArtNote[$field]["trim"][] = $ctag3->GetInnerText();
							else if($ctag3->GetName()=="match")
								$this->ArtNote[$field]["match"] = $ctag3->GetInnerText();
						}
					}
					else if($ctag2->GetName()=="sppage"){
						$this->ArtNote["sppage"] = $ctag2->GetInnerText();
					}
				}//End inner Loop2
			}
			//typeenum 配置
			//被采集网站的类别名称对应本地的类别ID值
			else if($ctag->GetName()=="typeenum")
			{
				$dtp2->LoadString($ctag->GetInnerText());
				for($j=0;$j<$dtp2->GetCount();$j++)
				{
					$ctag2 = $dtp2->CTags[$j];
					$this->TypeEnums[$ctag2->GetAtt("name")] = $ctag2->GetAtt("id");
				}//End inner Loop3
			}
		
		}//End Loop
		$dtp->Clear();
		$dtp2->Clear();
	}
	//---------------------
	//测试列表
	//---------------------
	function TestList()
	{
		if(isset($this->List["url"][0])) $dourl = $this->List["url"][0];
		else
		{
				echo "配置中指定列表的网址错误!\r\n";
	  		return ;
		}
		
		if($this->List["sourcetype"]=="archives")
		{
			echo "配置中指定的源参数为文档的原始URL：\r\n";
			$i=0;
			$v = "";
			foreach($this->List["url"] as $v)
			{
				echo $v."\r\n";
				$i++;
				if($i>9) break;
			}
			return $v;
		}
		
		$dhtml = new DedeHtml2();
		$dhtml->GetLinkType = "link";
		
		$dhd = new DedeHttpDown();
		$dhd->OpenUrl($dourl);
		$html = $dhd->GetHtml();
		$dhd->Close();
		
		if($html=="")
		{
			echo "读取其中的一个网址： $dourl 时失败！\r\n";
			return ;
		}
		$dhtml->GetLinkType = "link";
		$dhtml->SetSource($html,$dourl);
		$testpage = "";
		if(is_array($dhtml->Links))
		{
			echo "按指定规则在 $dourl 发现的网址：\r\n";
			foreach($dhtml->Links as $k=>$v)
			{
				$k =  $dhtml->FillUrl("$k");
				if($this->List["need"]!=""){
					if(eregi($this->List["need"],$k)){
						if($this->List["cannot"]==""){
							echo "$k - ".$v."\r\n";
							$TestPage = $k;
						}
						else if(!eregi($this->List["cannot"],$k))
						{	echo "$k - ".$v."\r\n"; $TestPage = $k; }
					}//eg1
				}//eg no null
				else{
					echo "$k - ".$v."\r\n"; $TestPage = $k;
				}
			}//foreach
		}
		else
		{
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
		
		$dhd = new DedeHttpDown();
		$dhd->OpenUrl($dourl);
		$html = $dhd->GetHtml();
		$dhd->Close();
		
		echo $this->GetPageField($html,$dourl,false);
	}
	//-----------------------------
	//下载其中一个网址，并保存
	//-----------------------------
	function DownUrl($aid,$dourl)
	{
		if($this->CHttpDown=="") $this->CHttpDown = new DedeHttpDown();
		$this->CHttpDown->OpenUrl($dourl);
		$html = $this->CHttpDown->GetHtml();
		$this->CHttpDown->Close();
		
		//下载分页内容
		if(!empty($this->ArtNote["sppage"]))
		{
			$fieldname = "";
			foreach($this->ArtNote as $k=>$sarr){
				if($sarr["isunit"]==1){ $fieldname = $k; break;}
			}
			$sarr_start = "";
			$sarr_end = "";
			$v = "";
			$urls = Array();
			if($fieldname!="")
			{
				@list($sarr_start,$sarr_end) = explode("[var:分页区域]",$this->ArtNote["sppage"]);
				$pos = strpos($html,$sarr_start);
				$endpos = strpos($html,$sarr_end,$pos);
				if($endpos>$pos && $pos>0)
				{	$v = substr($html,$pos+strlen($sarr_start),$endpos-$pos-strlen($sarr_start)); }
				if($v!="")
				{
					$v = "-".$v;
					$dhtml = new DedeHtml2();
					$dhtml->GetLinkType = "link";
					$dhtml->SetSource($v,$dourl,false);
					foreach($dhtml->Links as $k=>$v){
						$k = $dhtml->FillUrl($k);
						if($k==$dourl) continue;
						$this->CHttpDown->OpenUrl($k);
						$nhtml = $this->CHttpDown->GetHtml();
						$this->CHttpDown->Close();
						if($nhtml!=""){ 
							$this->tmpUnitValue .= "#p#".$this->GetOneField($nhtml,$k,$fieldname);
						}
					}
				}//$v!=""
			}//if $fieldname!=""
		}
		
		//echo $html;
		//exit();
		$body = addslashes($this->GetPageField($html,$dourl,true));
		
		$query = "Update #@__courl set dtime='".time()."',result='$body',isdown='1' where aid='$aid'";
		$this->dsql->SetSql($query);
		if(!$this->dsql->ExecuteNoneQuery())
		{
			echo $this->dsql->GetError();
		}
		$body = "";
		$html = "";
	}
	//--------------------------------
	//获得网页指定field的内容
	//--------------------------------
 function GetOneField(&$html,$dourl,$field)
 {
		if($html == "") return "";
		if(isset($this->ArtNote[$field]))
		{
			$sarr = $this->ArtNote[$field];
			$pos = 0;
			$endpos = 0;
			$v = "";
			$sarr_start = "";
			$sarr_end = "";
			if(!empty($sarr["match"])&&$sarr["value"]=="[var:内容]")
			{
				@list($sarr_start,$sarr_end) = explode("[var:内容]",$sarr["match"]);
				$pos = strpos($html,$sarr_start);
				$endpos = strpos($html,$sarr_end,$pos);
				if($endpos>$pos & $pos>=0)
				{	$v = substr($html,$pos+strlen($sarr_start),$endpos-$pos-strlen($sarr_start)); }
				if(isset($sarr["trim"])){
					foreach($sarr["trim"] as $nv){
					  if($v=="") break;
					  if($nv=="") continue;
						$v = eregi_replace($nv,"",$v);
					}
				}
				$v = trim($v);
				//判断是否要下载内容里的资源
				if($sarr["isdown"] == 1){
						$v = $this->DownMedias($v,$dourl);
				}
			}
		}
		
		if($sarr["function"]!=""){
			$v = $this->EvalFunc($v,$sarr["function"]);
		}
		
		return $v;
	}
	//------------------------------------------------
	//按载入的网页内容获取规则，从一个HTML文件中获取内容
	//-------------------------------------------------
	function GetPageField(&$html,$dourl,$needDown)
	{
		if($html == "") return "";
		$artitem = "";
		$isPutUnit = false;
		foreach($this->ArtNote as $k=>$sarr)
		{
			 if($k=="sppage") continue;
			 if(!is_array($sarr)){
				 $artitem .= "{dede:field name='$k'}".$sarr["value"]."{/dede:field}\r\n";
				 continue;
			 }
		   $pos = 0;
			 $endpos = 0;
			 $v = "";
			 $sarr_start = "";
			 $sarr_end = "";
			
			 //处理普通字段
			 if(!empty($sarr["match"])&&$sarr["value"]=="[var:内容]")
			 {
					list($sarr_start,$sarr_end) = explode("[var:内容]",$sarr["match"]);
					$pos = strpos($html,$sarr_start);
					
					if($pos>=0&&!empty($sarr_end)){	$endpos = strpos($html,$sarr_end,$pos);}
					
					if($endpos>$pos) $v = substr($html,$pos+strlen($sarr_start),$endpos-$pos-strlen($sarr_start));
					else $v = substr($html,$pos,strlen($html)-$pos);
					
					if(isset($sarr["trim"])){
						foreach($sarr["trim"] as $nv){
							if($v=="") break;
							if($nv=="") continue;
							$v = eregi_replace($nv,"",$v);
						}
					}
					$v = trim($v);
					//判断是否要下载内容里的资源
					if($needDown){
						if($sarr["isdown"] == 1){
							$v = $this->DownMedias($v,$dourl);
						}
					}
					else{
						if($sarr["isdown"] == 1){
							$v = $this->MediasReplace($v,$dourl);
						}
					}
					//---------------------------
					if($this->tmpUnitValue!="" && !$isPutUnit && $sarr["isunit"]==1)
					{ 
						$v = $v.$this->tmpUnitValue;
						$isPutUnit = true;
					}
					if($sarr["function"]!=""){
						$v = $this->EvalFunc($v,$sarr["function"]);
					}
					$artitem .= "{dede:field name='$k'}$v{/dede:field}\r\n";
			 }
			 //处理枚举分类的字段
			 else if($sarr["value"]=="[var:枚举]")
			 {
					if(!empty($sarr["match"]))
					{
					  list($sarr_start,$sarr_end) = explode("[var:枚举]",$sarr["match"]);
					  $pos = strpos($html,$sarr_start);
					
					  if($pos>=0&&!empty($sarr_end)) {	$endpos = strpos($html,$sarr_end,$pos);}
					
					  if($endpos>$pos) $v = substr($html,$pos+strlen($sarr_start),$endpos-$pos-strlen($sarr_start));
					  else $v = substr($html,$pos,strlen($html)-$pos);
					
					  if(isset($sarr["trim"])){
					  	foreach($sarr["trim"] as $nv)
					  	{
					  	  if($v=="") break;
					  	  if($nv=="") continue;
					  	  $v = eregi_replace($nv,"",$v);
					  	}
					  }
					
					  $v = trim($v);
					
					  if(isset($this->TypeEnums[$v])) $v = $this->TypeEnums[$v];
					  else $v = "";
					  if($sarr["function"]!=""){
						  $v = $this->EvalFunc($v,$sarr["function"]);
					  }
					  $artitem .= "{dede:field name='$k'}".$v."{/dede:field}\r\n";
				 }
				 else
				 {	$artitem .= "{dede:field name='$k'}{/dede:field}\r\n"; }
		 }
		 //处理含默认参数的情况
		 else{
				$artitem .= "{dede:field name='$k'}".$sarr["value"]."{/dede:field}\r\n";
		 }
	  }//End Foreach
		return $artitem;
	}
	//------------------------------
	//获得下载媒体的随机名称
	//------------------------------
	function GetRndName($url,$v)
	{
		$this->MediaCount++;
		$mnum = $this->MediaCount;
		$timename = strftime("%H%M%S",time());
		$threadnum = 0;
		if(isset($_GET["threadnum"])) $threadnum = $_GET["threadnum"];
		$filename = $timename."_".$threadnum.$mnum."_".mt_rand(100,999);
		$urls = explode(".",$url);
		if($v=="img"){
			$shortname = ".jpg";
			if(eregi("\.gif\?(.*)$",$v) || eregi("\.gif$",$v)) $shortname = ".gif";
			else if(eregi("\.png\?(.*)$",$v) || eregi("\.png$",$v)) $shortname = ".png";
		}
		else if($v=="embed") $shortname = ".swf";
		else $shortname = "";
		if(is_array($urls)){
			$sname = trim($urls[count($urls)-1]);
			if($sname!=""){
				$shortname = ".".$sname;
			}
		}
		return $filename.$shortname;
	}
	//---------------------------------
	//从数据库中搜索已下载的图片
	//---------------------------------
	function GetHasMedia($dourl)
	{
		$dourl = addslashes($dourl);
		$this->dsql->SetQuery("Select nurl from #@__co_mediaurl where nid='".$this->NoteId."' And rurl like '$dourl'");
		$this->dsql->Execute();
		$row = $this->dsql->GetObject();
		if(!empty($row->nurl)) return $row->nurl;
		else return "";
	}
	//------------------------------
	//下载一个图片
	//------------------------------
	function SaveBin($dourl,$v)
	{
		
		//检查以前是否下载过这个资源
		$mname = $this->GetHasMedia($dourl);
		if($mname!="") return $mname;
		
		if($this->CHttpDown=="") $this->CHttpDown = new DedeHttpDown();
		$ndir = str_replace("\\","/",dirname(__FILE__));
		
		$fullpath = preg_replace("/\/{1,}/","/",$ndir."/".$this->Item["imgdir"]."/");
		if(!is_dir($fullpath)) mkdir($fullpath,0755);
		$cupath = preg_replace("/\/{1,}/","/",$this->Item["imgurl"]."/");
		$filename = $this->GetRndName($dourl,$v);
		$timedir = strftime("%Y-%m-%d",time());
		$fullpath = $fullpath.$timedir."/";
		if(!is_dir($fullpath)) mkdir($fullpath,0755);
		$cupath = $cupath.$timedir."/";
		$fullname = $fullpath.$filename;
		$fullurl = $cupath.$filename;
		
		//下载并保存内容
		
		$this->CHttpDown->OpenUrl($dourl);
		if(!$this->CHttpDown->SaveToBin($fullname)){
			$this->CHttpDown->Close();
			return $dourl;
		}
		$this->CHttpDown->Close();
		
		$inquery = "INSERT INTO #@__co_mediaurl(nid,rurl,nurl) VALUES ('".$this->NoteId."', '".addslashes($dourl)."', '".addslashes($fullurl)."');";
		$this->dsql->SetQuery($inquery);
		$this->dsql->ExecuteNoneQuery();
		
		return $fullurl;
	}
	//----------------------------------
	//下载内容里的资源
	//----------------------------------
	function DownMedias($html,$url)
	{
		if($this->CDedeHtml=="") $this->CDedeHtml = new DedeHtml2();
		$this->CDedeHtml->GetLinkType = "media";
		$this->CDedeHtml->SetSource($html,$url);
		//下载img标记里的图片
		foreach($this->CDedeHtml->Medias as $k=>$v)
		{
			$k = trim($k);
			if(!eregi("^http://",$k)) {	$furl = $this->CDedeHtml->FillUrl($k); }
			else {	$furl = $k; }
			
			if($v=="embed" && !eregi("\.(swf)\?(.*)$",$k) && !eregi("\.(swf)$",$k))
			{ continue; }
			
			$okurl = $this->SaveBin($furl,$v);
			$html = str_replace($k,$okurl,$html);
		}
		//下载超链接里的图片
		foreach($this->CDedeHtml->Links as $v)
		{
			$v = trim($v);
			
			if(eregi("\.(jpg|gif|png)\?(.*)$",$v) || eregi("\.(jpg|gif|png)$",$v))
			{ $m = "img"; }
			else if(eregi("\.(swf)\?(.*)$",$v) || eregi("\.(swf)$",$v))
			{ $m = "embed"; }
			else continue;
			
			if(!eregi("^http://",$v))
			{	$furl = $this->CDedeHtml->FillUrl($v); }
			else
			{	$furl = $v; }
			
			$okurl = $this->SaveBin($furl,$m);
			$html = str_replace($v,$okurl,$html);
		}
		return $html;
	}
	//---------------------------------
	//仅替换内容里的资源为绝对网址
	//----------------------------------
	function MediasReplace($html,$url)
	{
		if($this->CDedeHtml=="") $this->CDedeHtml = new DedeHtml2();
		$this->CDedeHtml->GetLinkType = "media";
		$this->CDedeHtml->SetSource($html,$url);
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
	//--------------------------------
	//采集种子网址
	//--------------------------------
	function GetSourceUrl()
	{
		if($this->List["sourcetype"]=="archives")
		{
			echo "配置中指定的源参数为文档的原始URL：<br/>处理中...<br/>\r\n";
			$this->dsql->SetSql("Delete From #@__courl where nid='".$this->NoteId."'");
		  $this->dsql->ExecuteNoneQuery();
			foreach($this->List["url"] as $v)
			{
				$inquery = "
				INSERT INTO #@__courl(nid,title,url,dtime,isdown,result) 
         VALUES ('".$this->NoteId."','用户指定的网址','$v','".time()."','0','');
				";
				$this->dsql->SetSql($inquery);
				$this->dsql->ExecuteNoneQuery();
			}
			echo "完成处理！<br/>\r\n";
			echo "<script>parent.location.reload();</script>"; 
			return ;
		}
		if($this->CHttpDown=="") $this->CHttpDown = new DedeHttpDown();
		if($this->CDedeHtml=="") $this->CDedeHtml = new DedeHtml2();
		$this->CDedeHtml->GetLinkType = "link";
		$tmplink = array();
		foreach($this->List["url"] as $k=>$v)
		{
			echo "下载并分析: $v <br/><br/>\r\n";
			
			$this->CHttpDown->OpenUrl($v);
			$html = $this->CHttpDown->GetHtml();
			$this->CHttpDown->Close();
			
			$this->CDedeHtml->GetLinkType = "link";
			$this->CDedeHtml->SetSource($html,$v);
			if($this->CDedeHtml->CharSet=="big5"){ $this->Item["language"] = "big5"; }
			else if($this->CDedeHtml->CharSet=="utf-8"){ $this->Item["language"] = "utf-8";}
			else{	$this->Item["language"]="gb2312"; }
		  if(is_array($this->CDedeHtml->Links))
		  foreach($this->CDedeHtml->Links as $k=>$v)
		  {
		  	$k = $this->CDedeHtml->FillUrl($k);
		  	if($this->List["need"]!=""){
					if(eregi($this->List["need"],$k)){
						if($this->List["cannot"]=="")
						{	
							$tmplink[$this->CDedeHtml->FillUrl($k)] = $v; 
						}
						else if(!eregi($this->List["cannot"],$k))
						{	$tmplink[$this->CDedeHtml->FillUrl($k)] = $v; }
					}
				}
				else{
					$tmplink[$this->CDedeHtml->FillUrl($k)] = $v;
				}
		  }
		  $this->CDedeHtml->Clear();
		}//foreach
		$unum = count($tmplink);
		if($unum>0){
		  echo "完成种子网址抓取，共找到：".count($tmplink)." 个记录!<br/>\r\n";
		  $this->dsql->SetSql("Delete From #@__courl where nid='".$this->NoteId."'");
		  $this->dsql->ExecuteNoneQuery();
		  foreach($tmplink as $k=>$v)
			{
				$k = $this->ChangeCode($k);
				$v = $this->ChangeCode($v);
				if($v=="") $v="无标题，可能是图片链接";
				$v = addslashes($v);
				$k = addslashes($k);
				$inquery = "
				INSERT INTO #@__courl(nid,title,url,dtime,isdown,result) 
         VALUES ('".$this->NoteId."','$v','$k','".time()."','0','');
				";
				$this->dsql->SetSql($inquery);
				$this->dsql->ExecuteNoneQuery();
			}
			echo "<script>parent.location.reload();</script>"; 
	  }
	  else
	  {
	  	echo "按指定规则没找到任何链接！";
	  }
	}
	//---------------------------------
	//获得指定名称的枚举类目ID
	//---------------------------------
	function GetEnum($sname)
	{
		if(isset($this->TypeEnums[$sname])) return $this->TypeEnums[$sname];
		else return 0;
	}
	//-----------------------
	//编码转换
	//-----------------------
	function ChangeCode($str)
	{
		if($this->Item["language"]=="utf-8") $str = utf82gb($str);
		if($this->Item["language"]=="big5") $str = big52gb($str);
		return $str;
	}
	//用扩展函数处理采集到的原始数据
	//-----------------------------
	function EvalFunc($fvalue,$functionname)
	{
		$functionname = str_replace("{\"","[\"",$functionname);
		$functionname = str_replace("\"}","\"]",$functionname);
		$functionname = "\$fieldvalue = ".str_replace("@me",$fvalue,$functionname).";";
		eval($functionname);
		if(empty($fieldvalue)) return "";
		else return $fieldvalue;
	}
}
?>