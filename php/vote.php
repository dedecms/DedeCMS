<?
require("config.php");
require("../dede/inc_vote.php");
if(empty($id))
{
	echo "参数不对，禁止访问！";
	exit();
}
$vo = new DedeVote();
$vo->SetVote($id);
if(!empty($voteitem))
{
	$vo->AddVoteCount($voteitem);
	$vo->SaveVote();
	$vo->SetVote($id);
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>投票结果</title>
<link href="../base.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style2 {
	color: #CC0000;
	font-size: 11pt;
}
-->
</style>
</head>
<body>
<table width="650" border="0" align="center" cellspacing="2">
  <tr> 
    <td><img src="img/vote.gif" width="320" height="46"></td>
  </tr>
  <tr> 
    <td bgcolor="#CCCC99" height="6"></td>
  </tr>
  <tr>
    <td height="28"><span class="style2">&nbsp;投票名称：
      <?=$vo->voteName?>
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#F0F2EA">&nbsp;参与这次投票的总人数：<?=$vo->GetTotalCount()?></td>
  </tr>
  <tr>
    <td bgcolor="#DFEAE4">&nbsp;投票结果：</td>
  </tr>
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="3"></td>
        </tr>
		<tr>
          <td height="100" align="center" valign="top">
          <?
          echo $vo->GetVoteResult("98%",30,"40%");
          ?>
          
</td>
        </tr>
		<tr>
          <td height="3"></td>
        </tr>
      </table> 
    </td>
  </tr>
  <tr> 
    <td bgcolor="#CCCC99" height="6"></td>
  </tr>
  <tr> 
    <td align="center"><a href="http://www.dedecms.com" target="_blank">Power by DedeCms Dede织梦之旅</a></td>
  </tr>
</table>
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>
