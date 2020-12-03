<?php 
/*******************************
//织梦HTML解析类V1.1 PHP版
//www.dedecms.com
function c____DedeHtml2();
这个类针对于采集程序，与DedeHtml类功能不尽相同
********************************/
class DedeHtml2
{
	var $CAtt;
	var $SourceHtml;
	var $Title;
	var $Medias;
	var $MediaInfos;
	var $Links;
	var $CharSet;
	var $BaseUrl;
	var $BaseUrlPath;
	var $HomeUrl;
	var $IsHead;
	var $ImgHeight;
	var $ImgWidth;
	var $GetLinkType;
	//-------------------------
	//构造函数
	//-------------------------
	function __construct()
 	{
 		$this->CAtt = "";
 		$this->SourceHtml = "";
 		$this->Title = "";
 		$this->Medias = Array();
 		$this->MediaInfos = Array();
 		$this->Links = Array();
    $this->CharSet = "";
    $this->BaseUrl = "";
    $this->BaseUrlPath = "";
    $this->HomeUrl = "";
    $this->IsHead = false;
    $this->ImgHeight = 30;
    $this->ImgWidth = 50;
    $this->GetLinkType = "all";
  }
  function DedeHtml2()
 	{
 		$this->__construct();
  }
	//设置HTML的内容和来源网址
	//gethead 是指是否要分析html头
	//如果是局部HTML,此项必须设为false,否则无法分析网页
	function SetSource(&$html,$url="",$gethead=false)
	{
		$this->__construct();
		if($gethead) $this->IsHead = false;
		else $this->IsHead = true;
		$this->CAtt = new DedeAttribute2();
		$url = trim($url);
		$this->SourceHtml = $html;
		$this->BaseUrl = $url;
		//判断文档相对于当前的路径
		$urls = @parse_url($url);
		$this->HomeUrl = $urls["host"];
		$this->BaseUrlPath = $this->HomeUrl.$urls["path"];
		$this->BaseUrlPath = preg_replace("/\/([^\/]*)\.(.*)$/","/",$this->BaseUrlPath);
		$this->BaseUrlPath = preg_replace("/\/$/","",$this->BaseUrlPath);
		if($html!="") $this->Analyser();
	}
	//-----------------------
	//解析HTML
	//-----------------------
	function Analyser()
	{
		$cAtt = new DedeAttribute2();
		$cAtt->IsTagName = false;
		$c = "";
		$i = 0;
		$startPos = 0;
		$endPos = 0;
		$wt = 0;
		$ht = 0;
		$scriptdd = 0;
		$attStr = "";
		$tmpValue = "";
		$tmpValue2 = "";
		$tagName = "";
		$hashead = 0;
		$slen = strlen($this->SourceHtml);
		
		if($this->GetLinkType=="link")
		{ $needTag = "a|meta|title|/head|body"; }
		else if($this->GetLinkType=="media")
		{ $needTag = "img|embed|a"; $this->IsHead = true; }
		else
		{ $needTag = "img|embed|a|meta|title|/head|body"; }
		
		for(;$i < $slen; $i++)
		{
			$c = $this->SourceHtml[$i];
			if($c=="<")
			{
				//这种情况一般是用于采集程序的模式
				$tagName = "";
				$j = 0;
				for($i=$i+1; $i < $slen; $i++){
					if($j>10) break;
					$j++;
					if(!ereg("[ <>\r\n\t]",$this->SourceHtml[$i]))
					{ $tagName .= $this->SourceHtml[$i]; }
					else{ break; }
				}
				$tagName = strtolower($tagName);
				if($tagName=="!--"){
					$endPos = strpos($this->SourceHtml,"-->",$i);
					if($endPos!==false) $i=$endPos+3;
					continue;
				}
				if(ereg($needTag,$tagName)){
					$startPos = $i;
					$endPos = strpos($this->SourceHtml,">",$i+1);
					if($endPos===false) break;
					$attStr = substr($this->SourceHtml,$i+1,$endPos-$startPos-1);
					$cAtt->SetSource($attStr);
				}else{
					continue;
				}
				//检测HTML头信息
				if(!$this->IsHead)
				{
					if($tagName=="meta"){
					  //分析name属性
					  $tmpValue = strtolower($cAtt->GetAtt("http-equiv"));
					  if($tmpValue=="content-type"){
							  $this->CharSet = strtolower($cAtt->GetAtt("charset"));
						}
				  } //End meta 分析
				  else if($tagName=="title"){
						$this->Title = $this->GetInnerText($i,"title");
						$i += strlen($this->Title)+12;
					}
				  else if($tagName=="/head"||$tagName=="body"){
				  	$this->IsHead = true;
				  	$i = $i+5;
					}
			  }
			  else
			  {
					//小型分析的数据
					//只获得内容里的多媒体资源链接，不获取text
					if($tagName=="img"){ //获取图片中的网址
						$this->InsertMedia($cAtt->GetAtt("src"),"img"); 
					}
					else if($tagName=="embed"){ //获得Flash或其它媒体的内容
						$rurl = $this->InsertMedia($cAtt->GetAtt("src"),"embed");
						if($rurl != ""){
						  $this->MediaInfos[$rurl][0] = $cAtt->GetAtt("width");
						  $this->MediaInfos[$rurl][1] = $cAtt->GetAtt("height");
						}
					}
					else if($tagName=="a"){ //获得Flash或其它媒体的内容
						$this->InsertLink($cAtt->GetAtt("href"),$this->GetInnerText($i,"a"));
					}
				}//结束解析body的内容
			}//End if char
		}//End for
		if($this->Title=="") $this->Title = $this->BaseUrl;
	}
	//
	//重置资源
	//
	function Clear()
	{
		$this->CAtt = "";
		$this->SourceHtml = "";
		$this->Title = "";
		$this->Links = "";
		$this->Medias = "";
		$this->BaseUrl = "";
		$this->BaseUrlPath = "";
	}
	//
	//分析媒体链接
	//
	function InsertMedia($url,$mtype)
	{
		if( ereg("^(javascript:|#|'|\")",$url) ) return "";
		if($url=="") return "";
		$this->Medias[$url]=$mtype;
		return $url;
	}
	function InsertLink($url,$atitle)
	{
		if( ereg("^(javascript:|#|'|\")",$url) ) return "";
		if($url=="") return "";
		$this->Links[$url]=$atitle;
		return $url;
	}
	//
	//分析content-type中的字符类型
	//
	function ParCharSet($att)
	{
		$startdd=0;
		$taglen=0;
		$startdd = strpos($att,"=");
		if($startdd===false) return "";
		else
		{
			$taglen = strlen($att)-$startdd-1;
			if($taglen<=0) return "";
			return trim(substr($att,$startdd+1,$taglen));
		}
	}
	//
	//分析refresh中的网址
	//
	function ParRefresh($att)
	{
		return $this->ParCharSet($att);
	}
	//
	//补全相对网址
	//
	function FillUrl($surl)
  {
    $i = 0;
    $dstr = "";
    $pstr = "";
    $okurl = "";
    $pathStep = 0;
    $surl = trim($surl);
    if($surl=="") return "";
    $pos = strpos($surl,"#");
    if($pos>0) $surl = substr($surl,0,$pos);
    if($surl[0]=="/"){
    	$okurl = "http://".$this->HomeUrl."/".$surl;
    }
    else if($surl[0]==".")
    {
      if(strlen($surl)<=2) return "";
      else if($surl[0]=="/")
      {
      	$okurl = "http://".$this->BaseUrlPath."/".substr($surl,2,strlen($surl)-2);
    	}
      else{
        $urls = explode("/",$surl);
        foreach($urls as $u){
          if($u=="..") $pathStep++;
          else if($i<count($urls)-1) $dstr .= $urls[$i]."/";
          else $dstr .= $urls[$i];
          $i++;
        }
        $urls = explode("/",$this->BaseUrlPath);
        if(count($urls) <= $pathStep)
        	return "";
        else{
          $pstr = "http://";
          for($i=0;$i<count($urls)-$pathStep;$i++)
          { $pstr .= $urls[$i]."/"; }
          $okurl = $pstr.$dstr;
        }   		
      }
    }
    else
    {
      if(strlen($surl)<7)
        $okurl = "http://".$this->BaseUrlPath."/".$surl;
      else if(strtolower(substr($surl,0,7))=="http://")
        $okurl = $surl;
      else
        $okurl = "http://".$this->BaseUrlPath."/".$surl;
    }
    $okurl = eregi_replace("^(http://)","",$okurl);
    $okurl = eregi_replace("/{1,}","/",$okurl);
    return "http://".$okurl;
  }
  //
	//获得和下一个标记之间的文本内容
	//
	function GetInnerText($pos,$tagname)
	{
		$startPos=0;
		$endPos=0;
		$textLen=0;
		$str="";
		$startPos = strpos($this->SourceHtml,'>',$pos);
		if($tagname=="title")
			$endPos = strpos($this->SourceHtml,'<',$startPos);
		else{
			$endPos = strpos($this->SourceHtml,'</a',$startPos);
			if($endPos===false) $endPos = strpos($this->SourceHtml,'</A',$startPos);
		}
		if($endPos>$startPos){
			$textLen = $endPos-$startPos;
			$str = substr($this->SourceHtml,$startPos+1,$textLen-1);
		}
		if($tagname=="title")
			return trim($str);
		else{
			$str = eregi_replace("</(.*)$","",$str);
			$str = eregi_replace("^(.*)>","",$str);
			return trim($str);
		}
	}
}//End class
/*******************************
//属性解析器
function c____DedeAttribute2();
********************************/
class DedeAttribute2
{
	var $SourceString = "";
	var $SourceMaxSize = 1024;
	var $CharToLow = FALSE;  //属性值是否不分大小写(属性名统一为小写)
	var $IsTagName = TRUE; //是否解析标记名称
	var $Count = -1;
  var $Items = ""; //属性元素的集合
  //设置属性解析器源字符串
	function SetSource($str="")
	{
		$this->Count = -1;
  	$this->Items = "";
		$strLen = 0;
		$this->SourceString = trim(preg_replace("/[ \t\r\n]{1,}/"," ",$str));
		$strLen = strlen($this->SourceString);
		$this->SourceString .= " "; //增加一个空格结尾,以方便处理没有属性的标记
		if($strLen>0&&$strLen<=$this->SourceMaxSize){
			$this->PrivateAttParse();
		}
	}
  //获得某个属性
  function GetAtt($str){
    if($str=="") return "";
    $str = strtolower($str);
    if(isset($this->Items[$str])) return $this->Items[$str];
    else return "";
  }
  //判断属性是否存在
  function IsAtt($str){
    if($str=="") return false;
    $str = strtolower($str);
    if(isset($this->Items[$str])) return true;
    else return false;
  }
  //获得标记名称
  function GetTagName(){
     return $this->GetAtt("tagname");
  }
  // 获得属性个数
  function GetCount(){
      return $this->Count+1;
	}
	//解析属性(仅给SetSource调用)
	function PrivateAttParse()
	{
		$d = "";
		$tmpatt="";
		$tmpvalue="";
		$startdd=-1;
		$ddtag="";
		$strLen = strlen($this->SourceString);
		$j = 0;
		//这里是获得标记的名称
		if($this->IsTagName)
		{
			//如果属性是注解，不再解析里面的内容，直接返回
			if(isset($this->SourceString[2]))
			{
				if($this->SourceString[0].$this->SourceString[1].$this->SourceString[2]=="!--")
				{ $this->Items["tagname"] = "!--"; return ;}
			}
			//
			for($i=0;$i<$strLen;$i++){
				$d = $this->SourceString[$i];
				$j++;
				if(ereg("[ '\"\r\n\t]",$d)){
					$this->Count++;
					$this->Items["tagname"]=strtolower(trim($tmpvalue));
					$tmpvalue = ""; break;
				}
				else
				{	$tmpvalue .= $d;}
			}
			if($j>0) $j = $j-1;
	  }
		//遍历源字符串，获得各属性
		for($i=$j;$i<$strLen;$i++)
		{
			$d = $this->SourceString[$i];
			//获得属性的键
			if($startdd==-1){
				if($d!="=")	$tmpatt .= $d;
				else{
					$tmpatt = strtolower(trim($tmpatt));
					$startdd=0;
				}
			}
			//检测属性值是用什么包围的，允许使用 '' "" 或空白
			else if($startdd==0){
				switch($d){
					case ' ':
						continue;
						break;
					case '\'':
						$ddtag='\'';
						$startdd=1;
						break;
					case '"':
						$ddtag='"';
						$startdd=1;
						break;
					default:
						$tmpvalue.=$d;
						$ddtag=' ';
						$startdd=1;
						break;
				}
			}
			//获得属性的值
			else if($startdd==1)
			{
				if($d==$ddtag){
					$this->Count++;
          if($this->CharToLow) $this->Items[$tmpatt] = strtolower(trim($tmpvalue));
					else $this->Items[$tmpatt] = trim($tmpvalue);
					$tmpatt = "";
					$tmpvalue = "";
					$startdd=-1;
				}
				else
					$tmpvalue.=$d;
			}
	  }//End for
	  //处理没有值的属性(必须放在结尾才有效)如："input type=radio name=t1 value=aaa checked"
	  if($tmpatt!="")
	  { $this->Items[$tmpatt] = "";}
 }//End Function PrivateAttParse

}//End Class DedeAttribute2

?>