<?
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_ArcTj');
$dsql = new DedeSql(false);
$row1 = $dsql->GetOne("Select count(*) as dd From #@__archives");
$row2 = $dsql->GetOne("Select count(*) as dd From #@__feedback");
$row3 = $dsql->GetOne("Select count(*) as dd From #@__member");

function GetArchives($dsql,$ordertype)
{
	$starttime = mytime() - (24*3600*30);
	if($ordertype=='monthFeedback'
	||$ordertype=='monthHot') $swhere = " where senddate>$starttime ";
	else $swhere = "";
	if(eregi('feedback',$ordertype)) $ordersql = " order by postnum desc ";
	else $ordersql = " order by click desc ";
	$query = "Select ID,title,click,postnum From #@__archives $swhere $ordersql limit 0,20 ";
	$dsql->SetQuery($query);
	$dsql->Execute('ga');
	while($row = $dsql->GetObject('ga')){
		if(eregi('feedback',$ordertype)) $moreinfo = "[<a target='_blank' href='".$GLOBALS['cfg_plus_dir']."/feedback.php?arcID={$row->ID}'><u>评论：{$row->postnum}</u></a>]";
		else $moreinfo = "[点击：{$row->click}]";
		echo "·<a href='archives_do.php?aid={$row->ID}&dopost=viewArchives' target='_blank'>";
		echo cn_substr($row->title,30)."</a>{$moreinfo}<br/>\r\n";
	}
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>文档信息统计</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="100%"  border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
    <tr> 
      <td height="20" colspan="2" background='img/tbg.gif'>
      	<strong>文档信息统计：</strong>
      </td>
    </tr>
    <tr> 
      
    <td height="250" colspan="2" align="center" valign="top" bgcolor="#FFFFFF">
	 <table width="98%" border="0" cellspacing="0" cellpadding="0">
        <tr><td height="10"></td></tr>
      </table>
      <table width="98%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td><strong>综合信息统计：</strong></td>
          <td><table width="300" border="0" cellpadding="1" cellspacing="1" bgcolor="#996666">
              <tr bgcolor="#FFFFFF"> 
                <td width="140" bgcolor="#F3ECDA">&nbsp;文档总数：</td>
                <td width="152">　［<?=$row1['dd']?>］</td>
              </tr>
              <tr bgcolor="#FFFFFF"> 
                <td bgcolor="#F3ECDA">&nbsp;评论总数：</td>
                <td>　［<?=$row2['dd']?>］</td>
              </tr>
              <tr bgcolor="#FFFFFF"> 
                <td bgcolor="#F3ECDA">&nbsp;会员总数：</td>
                <td>　［<?=$row3['dd']?>］</td>
              </tr>
            </table></td>
        </tr>
        <tr> 
          <td width="13%" height="80"><strong>频道信息统计：</strong></td>
          <td width="87%"><table width="98%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="10"></td>
              </tr>
            </table>
            <table width="300" border="0" cellpadding="1" cellspacing="1" bgcolor="#996666">
              <tr align="center" bgcolor="#E8E6D7"> 
                <td width="140" bgcolor="#E8E6D7">频道名称</td>
                <td>文档总数</td>
              </tr>
              <?
              $dsql->SetQuery("Select ID,typename From #@__channeltype");
              $dsql->Execute();
              while($row = $dsql->GetObject()){
              ?>
              <tr align="center" bgcolor="#FFFFFF"> 
                <td><?=$row->typename?></td>
                <td>
                ［<?
                $row1 = $dsql->GetOne("Select count(*) as dd From #@__archives where channel='{$row->ID}'");
                echo $row1['dd'];
                ?>］
                </td>
              </tr>
              <? } ?>
            </table></td>
        </tr>
        <tr> 
          <td height="194" valign="top"><strong><br>
            用户喜好文档：</strong></td>
          <td valign="top">
<table width="560" border="0" cellpadding="0" cellspacing="0">
              <tr valign="top"> 
                <td height="10" colspan="2"></td>
              </tr>
              <tr valign="top"> 
                <td width="280" height="201"> 
                  <table width="95%" border="0" cellpadding="2" cellspacing="1" bgcolor="#996666">
                    <tr> 
                      <td align="center" bgcolor="#F3ECDA"><strong>最热前二十篇文档</strong></td>
                    </tr>
                    <tr> 
                      <td height="200" align="right" valign="top" bgcolor="#FFFFFF"> 
                        <table width="98%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td>
                              <?GetArchives($dsql,'Hot')?>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
                <td width="280"> 
                  <table width="95%" border="0" cellpadding="2" cellspacing="1" bgcolor="#996666">
                    <tr> 
                      <td align="center" bgcolor="#F3ECDA"><strong>最多评论前二十篇文档</strong></td>
                    </tr>
                    <tr> 
                      <td height="200" align="right" valign="top" bgcolor="#FFFFFF"> 
                        <table width="98%" border="0" cellspacing="0" cellpadding="0">
                          <tr> 
                            <td>
                              <?GetArchives($dsql,'Feedback')?>
                            </td>
                          </tr>
                        </table> </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
            <table width="560" border="0" cellpadding="0" cellspacing="0">
              <tr valign="top"> 
                <td height="10" colspan="2"></td>
              </tr>
              <tr valign="top"> 
                <td width="280" height="201"> 
                  <table width="95%" border="0" cellpadding="2" cellspacing="1" bgcolor="#996666">
                    <tr> 
                      <td align="center" bgcolor="#E8E6D7"><strong>一个月内最热前二十篇文档</strong></td>
                    </tr>
                    <tr> 
                      <td height="200" align="right" valign="top" bgcolor="#FFFFFF"> 
                        <table width="98%" border="0" cellspacing="0" cellpadding="0">
                          <tr> 
                            <td>
                              <?GetArchives($dsql,'monthHot')?>
                            </td>
                          </tr>
                        </table> </td>
                    </tr>
                  </table>
                </td>
                <td width="280"> 
                  <table width="95%" border="0" cellpadding="2" cellspacing="1" bgcolor="#996666">
                    <tr> 
                      <td align="center" bgcolor="#E8E6D7"><strong>一个月内最多评论前二十篇文档</strong></td>
                    </tr>
                    <tr> 
                      <td height="200" align="right" valign="top" bgcolor="#FFFFFF"> 
                        <table width="98%" border="0" cellspacing="0" cellpadding="0">
                          <tr> 
                            <td>
                              <?GetArchives($dsql,'monthFeedback')?>
                            </td>
                          </tr>
                        </table> </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table></td>
        </tr>
      </table> 
      <table width="98%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="10"></td>
        </tr>
      </table></td>
    </tr>
    <tr> 
      
    <td height="20" colspan="2" bgcolor="#DFE9C0"></td>
    </tr>
</table>
<?
$dsql->Close();
?>
</body>
</html>
