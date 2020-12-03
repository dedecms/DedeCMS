<?php   if(!defined('DEDEINC')) exit('Request Error!');

helper('mda');
helper('cache');

function lib_mda(&$ctag,&$refObj)
{
    global $dsql, $envs, $cfg_soft_lang;
    //属性处理
    $type = empty($type)? 'code' : $type;
    $class = empty($class)? '_DEDECY' : $class;
    $version = MDA_VER;
    $attlist="uuid|,name|";
    FillAttsDefault($ctag->CAttribute->Items,$attlist);
    extract($ctag->CAttribute->Items, EXTR_SKIP);

    if ( empty($uuid) AND empty($name) ) return '填写正确的uuid 或 name';
    
    $reval="";
    
    //if( !$dsql->IsTable("#@__plus_mda_setting") ) return '没安装<a href="'.MDA_APIHOST.'" target="_blank">德得广告模块</a>';
    
    $email = mda_get_setting('email');
    $channel_uuid = mda_get_setting('channel_uuid');
    
    $channel_secret = mda_get_setting('channel_secret');
        
    //if(empty($channel_uuid)) return '尚未绑定德得广告账号，请<a href="'.MDA_APIHOST.'/home/register" target="_blank">注册</a>并到系统后台绑定';
    
    $prefix = 'mda';
    $key = 'code'.md5($uuid.$name);
    $row = GetCache($prefix, $key);

    if(!is_array($row))
    {
        $ts = time();
        $paramsArr=array(
            'channel_uuid'=>$channel_uuid, 
            'channel_secret'=>$channel_secret,
            'ts'=>$ts,
            'crc'=>md5($channel_uuid.$channel_secret.$ts),
        );
        if ( !empty($uuid) )
        {
            $paramsArr['place_uuid'] = $uuid;
        } else {
            $paramsArr['tag_name'] = urlencode($name);
        }

        $place = json_decode(mda_http_send(MDA_API_GET_PLACE,0,$paramsArr),TRUE);
        
        if (!isset($place['data']['place_code']) )
        {
            return '广告位API接口通信错误，查看<a href="'.MDA_APIHOST.'/help/apicode/'.$place['code'].'" target="_blank">德得广告</a>获取帮助';
        }
    
        $row['reval'] = htmlspecialchars($place['data']['place_code']);
        SetCache($prefix, $key, $row, 60*60*12);
    }

    if($cfg_soft_lang != 'utf-8') $row = AutoCharset($row, 'utf-8', 'gb2312');
    
    $reval .= htmlspecialchars_decode($row['reval']);
        
    return $reval;
}

