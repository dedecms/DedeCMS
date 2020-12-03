<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_Card');
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__moneycard_record order by aid desc");
if(!is_array($row)){ $startid = 100000; }
else $startid = $row['aid'] + 100000;
$row = $dsql->GetOne("Select * From #@__moneycard_type where tid='$cardtype'");
$money = $row['money'];
$num = $row['num'];
$mtime = time();
$utime = 0;
$ctid = $cardtype;
$startid++;
$endid = $startid+$mnum;

header("Content-Type: text/html; charset={$cfg_ver_lang}");

for(;$startid<$endid;$startid++){
	$cardid = $snprefix.$startid.'-';
	for($p=0;$p<$pwdgr;$p++){
	  for($i=0; $i < $pwdlen; $i++){
		   if($ctype==1){ $c = mt_rand(49,57); $c = chr($c); }
		   else{ 
			   $c = mt_rand(65,90);
			   if($c==79) $c = 'M';
			   else $c = chr($c);
		  }
		  $cardid .= $c;
	  }
	  if($p<$pwdgr-1) $cardid .= '-';
	}
	$inquery = "Insert into #@__moneycard_record(ctid,cardid,uid,isexp,mtime,utime,money,num)
              Values('$ctid','$cardid','0','0','$mtime','$utime','$money','$num'); ";
  $dsql->ExecuteNoneQuery($inquery);
  echo "成功生成点卡：{$cardid}<br/>";
}
ClearAllLink();
echo "成功生成 {$mnum} 个点卡！";
?>