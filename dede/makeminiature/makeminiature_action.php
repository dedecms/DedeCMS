<?php 
require_once(dirname(__FILE__)."/../config.php");
require_once(dirname(__FILE__)."/creatminiature.php");
CheckPurview('sys_ArcBatch');
$t1 = ExecTime();
require_once(dirname(__FILE__)."/../../include/inc_archives_view.php");
if(empty($startid)) $startid = 1;//起始ID号
if(empty($endid)) $endid = 0;//结束ID号
if(empty($startdd)) $startdd = 0;//结果集起始记录值
if(empty($pagesize)) $pagesize = 20;
if(empty($totalnum)) $totalnum = 0;
if(empty($typeid)) $typeid = 0;
if(empty($seltime)) $seltime = 0;
if(empty($stime)) $stime = "";
if(empty($etime)) $etime = "";
$body = "";
$litpic = "";
$fieldear = array();


$dsql = new DedeSql(false);
$mkimg=new CreatMiniature();
//获取条件
//------------------------
$gwhere = " where ID>=$startid And arcrank=0 ";
if($endid > $startid) $gwhere .= " And ID<= $endid ";
$idsql = "";
if($typeid!=0){
	$idArrary = TypeGetSunTypes($typeid,$dsql,0);
	if(is_array($idArrary))
	{
	  foreach($idArrary as $tid){
		  if($idsql=="") $idsql .= " typeid=$tid ";
		  else $idsql .= " or typeid=$tid ";
	  }
	  $idsql = " And (".$idsql.")";
  }
  $idsql = $gwhere.$idsql;
}
if($idsql=="") $idsql = $gwhere;
if($seltime==1){
	 $t1 = GetMkTime($stime);
	 $t2 = GetMkTime($etime);
	 $idsql .= " And (senddate >= $t1 And senddate <= $t2) ";
}
//统计记录总数
//------------------------
if($totalnum==0)
{
	$row = $dsql->GetOne("Select count(*) as dd From #@__archives $idsql");
	$totalnum = $row['dd'];
}

//获取记录，并生成缩略图---
if($totalnum > $startdd+$pagesize) $limitSql = " limit $startdd,$pagesize";
else $limitSql = " limit $startdd,".($totalnum - $startdd);
$tjnum = $startdd;
$dsql->SetQuery("Select ID,litpic,channel From #@__archives $idsql $limitSql");
$dsql->Execute();

while($row=$dsql->GetObject())
{
	$dsql1= new DedeSql(false);
	$tjnum++;
	$ID = $row->ID;
	$imgfile=$row->litpic;
	$channel=$row->channel;
	$filear=split("/",$imgfile);
	if($isall==1)
	{
		switch($channel)
		{
			case 1:
				$dsql1->SetQuery("Select body From #@__addonarticle where aid=$ID");
				$dsql1->Execute();
				$row1=$dsql1->GetObject();
				$body=$row1->body;
				break;
			case 2:
				$dsql1->SetQuery("Select imgurls From #@__addonimages where aid=$ID");
				$dsql1->Execute();
				$row1=$dsql1->GetObject();
				$body=$row1->imgurls;
				break;
		}
		$old_cfg_medias_dir = str_replace('/','\/',$cfg_medias_dir);
		$picname=preg_replace("/.+?".$old_cfg_medias_dir."(.+?)( |\"|').*$/is",$cfg_medias_dir."$1",$body);
		//$picname = preg_replace("/.+?".$old_cfg_medias_dir."(.*)( |\"|').*$/isU",$cfg_medias_dir."$1",$body);
		$picname=rtrim($picname);
		if(eregi("\.(jpg|gif|png)$",$picname))
		{
			$mkimg->SetVar($cfg_basedir.$picname,"file");
			$litpic=str_replace('.','_lit.',$picname);
			$litpic=stripslashes($litpic);
			switch($maketype)
			{
				case 1:
					$mkimg->Distortion($cfg_basedir.$litpic,$imgwidth,$imgheight);
					break;
				case 2:
					$mkimg->Prorate($cfg_basedir.$litpic,$imgwidth,$imgheight);
					break;
				case 3:
					$mkimg->Cut($cfg_basedir.$litpic,$imgwidth,$imgheight);
					break;
				case 4:
					$mkimg->BackFill($cfg_basedir.$litpic,$imgwidth,$imgheight,$backcolor1,$backcolor2,$backcolor3);
					break;
			}
			$dsql1->SetQuery("update #@__archives set litpic='$litpic' where ID='$ID'");
			$dsql1->ExecuteNoneQuery();
		}
		//echo "update #@__archives set litpic=$litpic where ID=$ID";
		echo $litpic."---ID:".$ID."<BR>";
	}
	else
	{
		if(is_array($filear) && count($fieldear)>3 &&($filear[3]!="litimg"||$filear[0]==""))
		{
			switch($channel)
			{
				case 1:
					$dsql1->SetQuery("Select body From #@__addonarticle where aid=$ID");
					$dsql1->Execute();
					$row1=$dsql1->GetObject();
					$body=$row1->body;
					break;
				case 2:
					$dsql1->SetQuery("Select imgurls From #@__addonimages where aid=$ID");
					$dsql1->Execute();
					$row1=$dsql1->GetObject();
					$body=$row1->imgurls;
					break;
			}
			$old_cfg_medias_dir = str_replace('/','\/',$cfg_medias_dir);
			$picname=preg_replace("/.+?".$old_cfg_medias_dir."(.+?)( |\"|').*$/is",$cfg_medias_dir."$1",$body);
			//echo preg_replace("/.+?".$old_cfg_medias_dir."(.*?)( |\"|').*$/is",$cfg_medias_dir."\\1",$body);
			$picname=rtrim($picname);
			//echo $picname;
			if(eregi("\.(jpg|gif|png)$",$picname))
			{
				$mkimg->SetVar($cfg_basedir.$picname,"file");
				$litpic=str_replace('.','_lit.',$picname);
				switch($maketype)
				{
					case 1:
						$mkimg->Distortion($cfg_basedir.$litpic,$imgwidth,$imgheight);
						break;
					case 2:
						$mkimg->Prorate($cfg_basedir.$litpic,$imgwidth,$imgheight);
						break;
					case 3:
						$mkimg->Cut($cfg_basedir.$litpic,$imgwidth,$imgheight);
						break;
					case 4:
						$mkimg->BackFill($cfg_basedir.$litpic,$imgwidth,$imgheight,$backcolor1,$backcolor2,$backcolor3);
						break;
				}
				$dsql1->SetQuery("update #@__archives set litpic='$litpic' where ID='$ID'");
				$dsql1->ExecuteNoneQuery();
			}
		}
				//echo "update #@__archives set litpic=$litpic where ID=$ID";
		echo $litpic."---ID:".$ID."<BR>";
	}
	
	$dsql1->Close();
}

$t2 = ExecTime();
$t2 = ($t2 - $t1);

//返回提示信息
if($totalnum>0) $tjlen = ceil( ($tjnum/$totalnum) * 100 );
else $tjlen=100;
$dvlen = $tjlen * 2;
$tjsta = "<div style='width:200;height:15;border:1px solid #898989;text-align:left'><div style='width:$dvlen;height:15;background-color:#829D83'></div></div>";
$tjsta .= "<br/>本次用时：".number_format($t2,2)." 到达位置：".($startdd+$pagesize)."<br/>完成创建文件总数的：$tjlen %，继续执行任务...";

if($tjnum < $totalnum)
{
	$nurl  = "makeminiature_action.php?endid=$endid&startid=$startid&typeid=$typeid";
	$nurl .= "&totalnum=$totalnum&startdd=".($startdd+$pagesize)."&pagesize=$pagesize";
	$nurl .= "&seltime=$seltime&stime=".urlencode($stime)."&etime=".urlencode($etime);
	$nurl .="&isall=$isall&maketype=$maketype&imgwidth=$imgwidth&imgheight=$imgheight&backcolor1=$backcolor1&backcolor2=$backcolor2&backcolor3=$backcolor3";
	$dsql->Close();
	ShowMsg($tjsta,$nurl,0,100);
	exit();
}
else
{
	$dsql->Close();
	echo "完成所有创建任务！";
	exit();
}

?>