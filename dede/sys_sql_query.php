<?php 
require(dirname(__FILE__)."/config.php");
CheckPurview('sys_Data');
if(empty($dopost)) $dopost = "";
header("Content-Type: text/html; charset={$cfg_ver_lang}");
$dsql = new DedeSql(false);
if($dopost=="viewinfo") //查看表结构
{
	if(empty($tablename)) echo "没有指定表名！";
	else{
			$dsql->SetQuery("SHOW CREATE TABLE ".$dsql->dbName.".".$tablename);
		$dsql->Execute();
		$row2 = $dsql->GetArray();
		$ctinfo = $row2[1];
		echo "<xmp>".trim($ctinfo)."</xmp>";
	}
	$dsql->Close();
	exit();
}
else if($dopost=="opimize") //优化表
{
	if(empty($tablename)) echo "没有指定表名！";
	else{
	  $dsql->ExecuteNoneQuery("OPTIMIZE TABLE '$tablename'");
	  $dsql->Close();
	  echo "执行优化表： $tablename  OK！";
	}
	exit();
}else if($dopost=="repair") //修复表
{
	if(empty($tablename)) echo "没有指定表名！";
	else{
	  $rs = $dsql->ExecuteNoneQuery("REPAIR TABLE '$tablename'");
	  $dsql->Close();
	  echo "修复表： $tablename  OK！";
	}
	exit();
}else if($dopost=="query") //执行SQL语句
{
	$t1 = ExecTime();
	$sqlquery = trim(stripslashes($sqlquery));
	if(eregi("drop(.*)table",$sqlquery) 
	|| eregi("drop(.*)database",$sqlquery)){
		echo "<span style='font-size:10pt'>删除'数据表'或'数据库'的语句不允许在这里执行。</span>";
		$dsql->Close();
	  exit();
	}
	//运行查询语句
	if(eregi("^select ",$sqlquery))
	{
		$dsql->SetQuery($sqlquery);
	  $dsql->Execute();
	  if($dsql->GetTotalRow()<=0) echo "运行SQL：{$sqlquery}，无返回记录！";
	  else echo "运行SQL：{$sqlquery}，共有".$dsql->GetTotalRow()."条记录，最大返回100条！";
	  $j = 0;
	  while($row = $dsql->GetArray())
	  {
	  	 $j++;
	  	 if($j>100) break;
	  	 echo "<hr size=1 width='100%'/>";
	  	 echo "记录：$j";
	  	 echo "<hr size=1 width='100%'/>";
	  	 foreach($row as $k=>$v){
	  		  if(ereg("[^0-9]",$k)){ echo "<font color='red'>{$k}：</font>{$v}<br/>\r\n"; }
	  	 }
	  }
	  $t2 = ExecTime();
	  echo "<hr>执行时间：".($t2-$t1);
	  exit();
	}
	if($querytype==2){
	   //普通的SQL语句
	   $sqlquery = str_replace("\r","",$sqlquery);
	   $sqls = split(";[ \t]{0,}\n",$sqlquery);
	   $nerrCode = ""; $i=0;
	   foreach($sqls as $q){
	     $q = trim($q); if($q==""){ continue; }
	     $dsql->ExecuteNoneQuery($q);
	     $errCode = trim($dsql->GetError());
	     if($errCode=="") $i++;
	     else $nerrCode .= "执行： <font color='blue'>$q</font> 出错，错误提示：<font color='red'>".$errCode."</font><br>";
     }
	   echo "成功执行{$i}个SQL语句！<br><br>";
	   echo $nerrCode;
  }else{
  	$dsql->ExecuteNoneQuery($sqlquery);
  	$nerrCode = trim($dsql->GetError());
  	echo "成功执行1个SQL语句！<br><br>";
	  echo $nerrCode;
	}
	$dsql->Close();
	$t2 = ExecTime();
	echo "<hr>执行时间：".($t2-$t1);
	exit();
}

require_once(dirname(__FILE__)."/templets/sys_sql_query.htm");

ClearAllLink();
?>