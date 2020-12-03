<?php
$cfg_NotPrintHead='Y';

require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(DEDEINC.'/qrcode.class.php');

$action = isset($action)? $action : '';
$type = isset($type)? $type : '';
$id = (isset($id) && is_numeric($id)) ? $id : 0;
if ( !in_array($type,array('list','arc','index')) ) $url = "http://2v.dedecms.com";

if ( $action=='get_qrcode' )
{
    if ( $type=='arc' )
    {
        $url = $cfg_basehost.$cfg_plus_dir.'/view.php?aid='.$id;
    } elseif ( $type=='list' )
    {
        $url = $cfg_basehost.$cfg_plus_dir.'/list.php?tid='.$id;
    }
    if($id==0) $url = "http://2v.dedecms.com";
    if ( $type=='index' ) $url = $cfg_basehost.$cfg_plus_dir.'/index.php';

    header("Content-Type: image/png");
    $params=array();
    $params['data'] = $url;
    $params['size'] = 6;
    $qrcode = new DedeQrcode;

    $qrcode->generate($params);
} else {
    header("Content-Type: text/html; charset={$cfg_soft_lang}");
    $dtp = new DedeTemplate();
	$tplfile = DEDETEMPLATE.'/plus/qrcode.htm';
    $dtp->LoadTemplate($tplfile);
    $dtp->SetVar('id',$id);
    $dtp->SetVar('type',$type);
    $dtp->Display();
    exit();
}

    