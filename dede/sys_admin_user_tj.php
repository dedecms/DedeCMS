<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_User');

//获取用户的统计信息
if(isset($dopost) && $dopost=='getone')
{
		$row = $dsql->GetOne("Select userid From `#@__admin` where id='$uid'; ");
		$userid = $row['userid'];
		$y = intval(MyDate('Y', time()));
		$m = intval(MyDate('m', time()));
		$d = intval(MyDate('d', time()));
		//全部
		$sql="SELECT addtable FROM `#@__channeltype` WHERE issystem='-1'";
		$dsql->Execute('me', $sql);
		while($frow = $dsql->GetArray('me'))
		{
			$dd=empty($dd)? "0" : $dd;
			$cc=empty($cc)? "0" : $cc;
			$row = $dsql->GetOne("Select count(aid) as dd,sum(click) as cc From `{$frow['addtable']}` where mid='$uid'; ");
			$dd += $row['dd'];
			$cc += $row['cc'];
		}
		$row = $dsql->GetOne("Select count(id) as dd,sum(click) as cc From `#@__archives` where mid='$uid'; ");
		$dd = $row['dd'] + $dd;
		$cc = $row['cc'] + $cc;
		//季度
		$starttime = 0;
		if( ereg("[123]", $m) && $m < 10) $starttime = $y."-01-01 00:00:00";
		else if( ereg("[456]", $m) ) $starttime = $y."-04-01 00:00:00";
		else if( ereg("[789]", $m) ) $starttime = $y."-07-01 00:00:00";
		else  $starttime = $y."-10-01 00:00:00";
		$istarttime = GetMkTime($starttime);
		$sql="SELECT addtable FROM `#@__channeltype` WHERE issystem='-1'";
		$dsql->Execute('me', $sql);
		while($frow = $dsql->GetArray('me'))
		{
			$dds=empty($dds)? "0" : $dds;
			$ccs=empty($ccs)? "0" : $ccs;
			$row = $dsql->GetOne("Select count(aid) as dd,sum(click) as cc From `{$frow['addtable']}` where senddate>$istarttime And mid='$uid'; ");
			$dds += $row['dd'];
			$ccs += $row['cc'];
		}
		$row = $dsql->GetOne("Select count(id) as dd,sum(click) as cc From `#@__archives` where senddate>$istarttime And mid='$uid'; ");
		$dds = $row['dd'] + $dds;
		$ccs = $row['cc'] + $ccs;
		//当月
		$starttime = $y."-{$m}-01 00:00:00";
		$istarttime = GetMkTime($starttime);
		$sql="SELECT addtable FROM `#@__channeltype` WHERE issystem='-1'";
		$dsql->Execute('me', $sql);
		while($frow = $dsql->GetArray('me'))
		{
			$ddm=empty($ddm)? "0" : $ddm;
			$ccm=empty($ccm)? "0" : $ccm;
			$row = $dsql->GetOne("Select count(aid) as dd,sum(click) as cc From `{$frow['addtable']}` where senddate>$istarttime And mid='$uid'; ");
			$ddm += $row['dd'];
			$ccm += $row['cc'];
		}
		$row = $dsql->GetOne("Select count(id) as dd,sum(click) as cc From `#@__archives` where senddate>$istarttime And mid='$uid'; ");
		$ddm = $row['dd'] + $ddm;
		$ccm = $row['cc'] + $ccm;
		//本周
		$starttime = $y."-{$m}-{$d} 00:00:00";
		$istarttime = GetMkTime($starttime) - (7*24*3600);
		$sql="SELECT addtable FROM `#@__channeltype` WHERE issystem='-1'";
		$dsql->Execute('me', $sql);
		while($frow = $dsql->GetArray('me'))
		{
			$ddw=empty($ddw)? "0" : $ddw;
			$ccw=empty($ccw)? "0" : $ccw;
			$row = $dsql->GetOne("Select count(aid) as dd,sum(click) as cc From `{$frow['addtable']}` where senddate>$istarttime And mid='$uid'; ");
			$ddw += $row['dd'];
			$ccw += $row['cc'];
		}
		$row = $dsql->GetOne("Select count(id) as dd,sum(click) as cc From `#@__archives` where senddate>$istarttime And mid='$uid'; ");
		$ddw = $row['dd'] + $ddw;
		$ccw = $row['cc'] + $ccw;
		//当天
		$starttime = $y."-{$m}-{$d} 00:00:00";
		$istarttime = GetMkTime($starttime);
		$sql="SELECT addtable FROM `#@__channeltype` WHERE issystem='-1'";
		$dsql->Execute('me', $sql);
		while($frow = $dsql->GetArray('me'))
		{
			$ddd=empty($ddd)? "0" : $ddd;
			$ccd=empty($ccd)? "0" : $ccd;
			$row = $dsql->GetOne("Select count(aid) as dd,sum(click) as cc From `{$frow['addtable']}` where senddate>$istarttime And mid='$uid'; ");
			$ddd += $row['dd'];
			$ccd += $row['cc'];
		}
		$row = $dsql->GetOne("Select count(id) as dd,sum(click) as cc From `#@__archives` where senddate>$istarttime And mid='$uid'; ");
		$ddd = $row['dd'] + $ddd;
		$ccd = $row['cc'] + $ccd;
		
		$msg = "<table width='96%' border='0' align='center' cellpadding='3' cellspacing='1' bgcolor='#C4DB8C'>
        <tr align='center' bgcolor='#ECFCD1'>
          <td width='18%' height='26'><strong>管理员↓|统计信息→</strong></td>
          <td width='18%'><strong>全部(文档|点击)</strong></td>
          <td width='16%'><strong>季度</strong></td>
          <td width='16%'><strong>当月</strong></td>
          <td width='16%'><strong>近七天</strong></td>
          <td width='16%'><strong>当天</strong></td>
        </tr>
        <tr align='center' bgcolor='#FFFFFF'>
          <td height='26'>{$userid}</td>
          <td>{$dd} | {$cc}</td>
          <td>{$dds} | {$ccs}</td>
          <td>{$ddm} | {$ccm}</td>
          <td>{$ddw} | {$ccw}</td>
          <td>{$ddd} | {$ccd}</td>
        </tr>
    </table><br style='clear:both'/>\r\n";
    AjaxHead();
    echo $msg;
    exit();
}


include DedeInclude('templets/sys_admin_user_tj.htm');

?>