<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_vote.php");
$aid = ereg_replace("[^0-9]","",$aid);
$vt = new DedeVote($aid);
$vcode = $vt->GetVoteForm();
$vt->Close();

require_once(dirname(__FILE__)."/templets/vote_getcode.htm");

ClearAllLink();
?>