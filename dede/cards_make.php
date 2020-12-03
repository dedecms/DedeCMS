<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_Card');
if(empty($dopost))
{
	$dopost = '';
}

if($dopost == '')
{
	include(DEDEADMIN."/templets/cards_make.htm");
}

//生成点卡
elseif($dopost == 'make')
{
	$row = $dsql->GetOne("Select * From #@__moneycard_record order by aid desc");
	!is_array($row) ? $startid=100000 : $startid=$row['aid']+100000;
	$row = $dsql->GetOne("Select * From #@__moneycard_type where tid='$cardtype'");
	$money = $row['money'];
	$num = $row['num'];
	$mtime = time();
	$utime = 0;
	$ctid = $cardtype;
	$startid++;
	$endid = $startid+$mnum;

	header("Content-Type: text/html; charset={$cfg_soft_lang}");

	for(;$startid<$endid;$startid++)
	{
		$cardid = $snprefix.$startid.'-';
		for($p=0;$p<$pwdgr;$p++)
		{
			for($i=0; $i < $pwdlen; $i++)
			{
				if($ctype==1)
				{
					$c = mt_rand(49,57); $c = chr($c);
				}
				else
				{
					$c = mt_rand(65,90);
					if($c==79)
					{
						$c = 'M';
					}
					else
					{
						$c = chr($c);
					}
				}
				$cardid .= $c;
			}
			if($p<$pwdgr-1)
			{
				$cardid .= '-';
			}
		}
		$inquery = "Insert into #@__moneycard_record(ctid,cardid,uid,isexp,mtime,utime,money,num)
              Values('$ctid','$cardid','0','0','$mtime','$utime','$money','$num'); ";
		$dsql->ExecuteNoneQuery($inquery);
		echo "成功生成点卡：{$cardid}<br/>";
	}
	echo "成功生成 {$mnum} 个点卡！";
}

?>