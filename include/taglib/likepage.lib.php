<?php
/**
 * 单页文档相同标识调用标签
 *
 * @version        $Id: likepage.lib.php 1 9:29 2010年7月6日 $
 * @package        DedeCMS.Taglib
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
 
/*>>dede>>
<name>单页文档相同标识调用标签</name>
<type>全局标记</type>
<for>V55,V56,V57</for>
<description>调用相同标识单页文档</description>
<demo>
{dede:likepage likeid='' row=''/}
</demo>
<attributes>
    <iterm>row:调用条数</iterm> 
    <iterm>likeid:标识名</iterm>
</attributes> 
>>dede>>*/
 
if(!defined('DEDEINC')) exit('DedeCMS Error: Request Error!');
require_once(dirname(__FILE__).'/likesgpage.lib.php');

function lib_likepage(&$ctag,&$refObj)
{
    return lib_likesgpage($ctag, $refObj);
}
