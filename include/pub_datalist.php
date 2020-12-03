<?
require_once(dirname(__FILE__)."/config_base.php");
require_once(dirname(__FILE__)."/pub_dedetag.php");
$lang_pre_page = "上页";
$lang_next_page = "下页";
$lang_index_page = "首页";
$lang_end_page = "末页";
//////////////////////////////////////////////
class DataList
{
	var $sourceSql;
	var $nowPage;
	var $totalResult;
	var $pageSize;
	var $queryTime;
	var $inTagS;
	var $inTagE;
	var $getValues;
	var $dtp;
	var $dsql;
	//构造函数///////
	//-------------
	function __construct()
 	{
 		global $nowpage,$totalresult;
 		if($nowpage==""||ereg("[^0-9]",$nowpage)) $nowpage=1;
		if($totalresult==""||ereg("[^0-9]",$totalresult)) $totalresult=0;
 	  $this->sourceSql="";
	  $this->pageSize=20;
	  $this->queryTime=0;
	  $this->inTagS = "[";
	  $this->inTagE = "]";
	  $this->getValues=Array();
	  $this->dtp;
		$this->dsql = new DedeSql();
		$this->dsql->Init(false);
		$this->nowPage = $nowpage;
		$this->totalResult = $totalresult;
  }
  function DataList()
  {
  	$this->__construct();
  }
	function Init()
	{
		return "";
	}
	//设置要解析的模板
	function SetTemplet($modpage)
	{
		if(!file_exists($modpage))
		{
			$this->dsql->DisplayError("Load templet <font color='red'>".$modpage."</font> false");
			exit();
		}
		$this->dtp = new DedeTagParse();
		$this->dtp->LoadTemplate($modpage);
		$pid = $this->dtp->GetTagID("page");
		if($pid!=-1) $this->pageSize = $this->dtp->CTags[$pid]->GetAtt("pagesize");
	}
	//设置网址的Get参数键值
	function SetParameter($key,$value)
	{
		$this->getValues[$key] = $value;
	}
	function SetSource($sql)
	{
		$this->sourceSql = $sql;
		$this->dsql->SetSql($sql);
	}
	//显示模板
	function Display()
	{
		$dlistid = $this->dtp->GetTagID("datalist");
		if($this->sourceSql!="")
		{
			$this->dtp->Assign(
				$dlistid,
				$this->GetDataList($this->dtp->CTags[$dlistid]->InnerText)
			);
		}
		for($i=0;$i<=$this->dtp->Count;$i++)
		{
			if($this->dtp->CTags[$i]->TagName=="datalist")
				continue;
			else if($this->dtp->CTags[$i]->TagName=="pagelist")
				$this->dtp->Assign($i,$this->GetPageList($this->dtp->CTags[$i]->GetAtt("listsize")));
			else
				$this->dtp->Assign($i,"");
		}
		$this->dtp->Display();
	}
	//保存结果为文件
	function SaveTo($filename)
	{
		$dlistid = $this->dtp->GetTagID("datalist");
		$this->dtp->Assign(
			$dlistid,
			$this->GetDataList($this->dtp->CTags[$dlistid]->InnerText)
		);
		for($i=0;$i<=$this->dtp->Count;$i++)
		{
			if($this->dtp->CTags[$i]->TagName=="datalist")
				continue;
			else if($this->dtp->CTags[$i]->TagName=="pagelist")
				$this->dtp->Assign($i,$this->GetPageList($this->dtp->CTags[$i]->GetAtt("listsize")));
			else
				$this->dtp->Assign($i,"");
		}
		$this->dtp->SaveTo($filename);
	}
	//获得解析后的模板结果字符串
	function GetResult()
	{
		$dlistid = $this->dtp->GetTagID("datalist");
		$this->dtp->Assign(
			$dlistid,
			$this->GetDataList($this->dtp->CTags[$dlistid]->InnerText)
		);
		for($i=0;$i<=$this->dtp->Count;$i++)
		{
			if($this->dtp->CTags[$i]->TagName=="datalist")
				continue;
			else if($this->dtp->CTags[$i]->TagName=="pagelist")
				$this->dtp->Assign($i,$this->GetPageList($this->dtp->CTags[$i]->GetAtt("listsize")));
		}
		return $this->dtp->GetResult();
	}
	//获得列表内容
	function GetDataList($innertext)
	{
		$timedd = "未知";
		$starttime = $this->ExecTime(); 
		$DataListValue = "";
		if($this->totalResult==0){
			$this->dsql->Query();
			$this->totalResult = $this->dsql->GetTotalRow();
			$this->dsql->queryString .= " limit 0,".$this->pageSize;
			$this->dsql->FreeResult();
		}else{
			$this->dsql->queryString .= " limit ".(($this->nowPage-1)*$this->pageSize).",".$this->pageSize;
		}
		$this->dsql->Query();
		//计算执行时间
		$endtime = $this->ExecTime();
		if($starttime!=""&&$endtime!=""){
			$timedd=$endtime-$starttime;
			if($timedd<0) $timedd=$timedd*(-1.0);
			$timedd=substr($timedd,0,5);
		}
		$this->queryTime = $timedd;
		$GLOBALS["limittime"] = $timedd;
		$GLOBALS["totalrecord"] = $this->totalResult;
		////////////////////////////////////////
		$dtp2 = new DedeTagParse();
		$dtp2->TagStartWord="[";
		$dtp2->TagEndWord="]";
		$dtp2->NameSpace="field";
		$dtp2->CharToLow=FALSE;
		$dtp2->LoadSource($innertext);
		$fnum = 0;
		while($GLOBALS["row"] = $this->dsql->GetArray())
		{
			$fnum++;
			for($i=0;$i<=$dtp2->Count;$i++)
			{
				if(isset($GLOBALS["row"][$dtp2->CTags[$i]->TagName]))
					$dtp2->Assign($i,$GLOBALS["row"][$dtp2->CTags[$i]->TagName]);
				else
					$dtp2->Assign($i,"");
			}
			$DataListValue .= $dtp2->GetResult();
		}
		$GLOBALS["row"] = "";
		return $DataListValue;
	}
	//获取分页列表
	function GetPageList($list_len)
	{
		global $lang_pre_page;
		global $lang_next_page;
		global $lang_index_page;
		global $lang_end_page;
		$prepage="";
		$nextpage="";
		$prepagenum = $this->nowPage-1;
		$nextpagenum = $this->nowPage+1;
		if($list_len==""||ereg("[^0-9]",$list_len)) $list_len=3;
		$totalpage = ceil($this->totalResult/$this->pageSize);
		
		if($totalpage<=1&&$this->totalResult>0) return "共1页/".$this->totalResult."条记录"; 
		if($this->totalResult == 0) return "共0页/".$this->totalResult."条记录"; 
		
		$purl = $this->GetCurUrl();
		$geturl="";
		$hidenform="";
		if($this->totalResult!=0) $this->SetParameter("totalresult",$this->totalResult);
		if(count($this->getValues)>0)
		{
			foreach($this->getValues as $key=>$value)
			{
				$value = urlencode($value);
				$geturl.="$key=$value"."&";
				$hidenform.="<input type='hidden' name='$key' value='$value'>\r\n";
			}
		}
		$purl .= "?".$geturl;
		
		//获得上一页和下一页的链接
		if($this->nowPage!=1)
		{
			$prepage.="<td width='50'><a href='".$purl."nowpage=$prepagenum'>$lang_pre_page</a></td>\r\n";
			$indexpage="<td width='30'><a href='".$purl."nowpage=1'>$lang_index_page</a></td>\r\n";
		}
		else
		{
			$indexpage="<td width='30'>$lang_index_page</td>\r\n";
		}	
		if($this->nowPage!=$totalpage&&$totalpage>1)
		{
			$nextpage.="<td width='50'><a href='".$purl."nowpage=$nextpagenum'>$lang_next_page</a></td>\r\n";
			$endpage="<td width='30'><a href='".$purl."nowpage=$totalpage'>$lang_end_page</a></td>\r\n";
		}
		else
		{
			$endpage="<td width='30'>$lang_end_page</td>\r\n";
		}
		//获得数字链接
		$listdd="";
		$total_list = $list_len * 2 + 1;
		if($this->nowPage>=$total_list) 
		{
    		$j=$this->nowPage-$list_len;
    		$total_list=$this->nowPage+$list_len;
    		if($total_list>$totalpage) $total_list=$totalpage;
		}	
		else
		{ 
   			$j=1;
   			if($total_list>$totalpage) $total_list=$totalpage;
		}
		for($j;$j<=$total_list;$j++)
		{
   			if($j==$this->nowPage) $listdd.= "<td width='20'>$j</td>\r\n";
   			else $listdd.="<td width='20'><a href='".$purl."nowpage=$j'>[".$j."]</a></td>\r\n";
		}
	
		$plist = "<table border='0' cellpadding='0' cellspacing='0'>\r\n";
		$plist.="<tr align='center' style='font-size:10pt'>\r\n";
		$plist.="<form name='pagelist' action='".$this->GetCurUrl()."'>$hidenform";
		$plist.=$indexpage;
		$plist.=$prepage;
		$plist.=$listdd;
		$plist.=$nextpage;
		$plist.=$endpage;
		if($totalpage>$total_list)
		{
			$plist.="<td width='36'><input type='text' name='nowpage' style='width:30;height:18'></td>\r\n";
			$plist.="<td width='30'><input type='submit' name='plistgo' value='GO' style='width:24;height:18;font-size:9pt'></td>\r\n";
		}
		$plist.="</form>\r\n</tr>\r\n</table>\r\n";
		return $plist;
	}
	//清除系统所占用资源
	function ClearList()
	{
		$this->dsql->Close();
		$this->dsql="";
		$dtp="";
	}
	function Clear()
	{
		$this->ClearList();
	}
	function Close()
	{
		$this->ClearList();
	}
	function ExecTime(){ 
		$time = explode(" ", microtime());
		$usec = (double)$time[0]; 
		$sec = (double)$time[1]; 
		return $sec + $usec; 
	}
	function GetCurUrl()
	{
		if(!empty($_SERVER["REQUEST_URI"])){
			$nowurl = $_SERVER["REQUEST_URI"];
			$nowurls = explode("?",$nowurl);
			$nowurl = $nowurls[0];
		}
		else
		{ $nowurl = $_SERVER["PHP_SELF"]; }
		return $nowurl;
	}
}
/*
//使用示范
$dlist = new DataList();
$dlist->Init();
$dlist->SetTemplet("dlist.htm");
$dlist->SetSource("select * from #@__admin");
$dlist->Display();//结果等同于 echo $dlist->GetResult();但前者并非是一次性输出的
$dlist->Close();
//dlist.htm
//===============================
//<br/>
//{dede:page pagesize="10"/}
//{dede:datalist}
//[field:ID/] -
//[field:uname/]
//<br/>
//{/dede:datalist}
//{dede:pagelist listsize=1/}
<br/>
*/
?>