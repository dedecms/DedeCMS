<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(dirname(__FILE__).'/../include/memberlogin.class.php');
AjaxHead();
$ajaxtype = isset($ajaxtype) && in_array($ajaxtype,array('info', 'single')) ? $ajaxtype : 'single';
$bid = (isset($bid) && is_numeric($bid)) ? $bid : 0;
if($bid==0){die(" Request Error! ");}

$curinfo = array();
$username = "";
$cfg_ml = new MemberLogin();

$sql = "SELECT * FROM #@__story_bookstars WHERE bid='$bid' ";
$dsql->Execute('rt',$sql);
if($row = $dsql->GetTotalRow('rt') == 0){
	$defaultarry = serialize(array(1=>'0',2=>'0',3=>'0',4=>'0',5=>'0'));
	$sql = "INSERT INTO #@__story_bookstars (`bid`,`totalvotes`, `totalvalue`, `voteinfo`, `usedids`) VALUES ('$bid', '0', '0','$defaultarry', '')";
	$result = $dsql->ExecuteNoneQuery($sql);
}
$numbers = $dsql->GetArray('rt');
if ($numbers['totalvotes'] < 1) {
	$count = 0;
} else {
	$count = $numbers['totalvotes']; 
}
$currating = $numbers['totalvalue']; 
$curinfo = unserialize($numbers['voteinfo']);
if(is_array($curinfo )) krsort($curinfo);
$sql = "SELECT usedids FROM #@__story_bookstars WHERE usedids LIKE '%".$cfg_ml->M_UserName."%' AND bid='".$bid."' ";
$dsql->Execute('vt',$sql);
$myvoteinfo = $dsql->GetArray('vt');
$voted = $dsql->GetTotalRow('vt');

/*--------------------------
//单个星星点评显示内容信息
function single(){ }
---------------------------*/
if($ajaxtype == 'single')
{
	if($cfg_ml->M_ID > 0)
	{
		$username = $cfg_ml->M_UserName;
	}else{
		echo "登陆后才可以进行评价!";
	}
	if($voted)
	{
		//发现已经评价过该书
		$myvoteinfo = !empty($myvoteinfo['usedids'])? unserialize($myvoteinfo['usedids']) : "";
		$uservote = array();
		foreach ($myvoteinfo as $key => $value) {
  		$uservote = explode("^", $value);
  		if($uservote[0] == $username) $infostr = '<ul class="rating '.switchNum($uservote[1]).'star"> </ul> <div class="pjtext">您的评分为:'.$uservote[1].'分!</div>';
		}
		echo $infostr;
		
	}else{
		//没有评论过用户可以提交评论
		$infostr = '<ul class="rating ">';
		for ($i = 1; $i <=5 ; $i++) {
			$infostr .= '  <li class="'.switchNum($i).'"><a href="/book/stardb.php?bid='.$bid.'&star='.$i.'" title="评分操作'.$i.'分" class="boxy">'.$i.'</a></li>';
		}
		echo $infostr;
	}
	
}
/*--------------------------
//用于显示点评信息的ajax
function info(){ }
---------------------------*/
elseif($ajaxtype == 'info')
{
	//这里用来处理显示的投票信息
	$avgstar = @number_format($currating/$count,1);//计算出平均分:3.2
	$ranknum = floor(ceil($avgstar)/2);
	$infostr = '<div class="book_average">';
	$infostr .= '  <ul class="average star'.$ranknum.'"></ul>';
	$infostr .= '  <span class="fl mark">'.$avgstar.'分</span>';
	$infostr .= '</div>';
	$infostr .= '<div class="fr eva">（'.$count.'人评价）</div>';
	//单条投票的统计信息
	$infostr .= '<div class="book_average"><ul class="smallstar">';
	foreach ($curinfo as $key => $value) {
  	$infostr .= '<li><span class="mstar s'.$key.'"></span><span class="power" style="width:'.floor(@number_format(($value/$count)*55,1)).'px;"></span>'.@number_format(($value/$count)*100,1).'%</li>'."\n\r";
	}
	echo $infostr;
}

//转换几个因为数字为英文字符
function switchNum($num=5)
{
	$snumarray = array(1=>'one',2=>'two',3=>'three',4=>'four',5=>'five');
	return empty($snumarray[$num])? '' : $snumarray[$num];
}

?>