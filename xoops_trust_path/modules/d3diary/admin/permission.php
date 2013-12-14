<?php

require_once dirname(dirname(__FILE__)).'/class/gtickets.php';
include_once dirname(dirname(__FILE__)).'/class/d3diaryConf.class.php';

$cid = isset($_POST['cid'])? intval($_POST['cid']) : 0;
$member_handler =& xoops_gethandler('member');
$group_list =& $member_handler->getGroupList();


$d3dConf =& d3diaryConf::getInstance($mydirname);
$d3dConf->set_mod_config(0,"admin");	// needs $dcfg

$gperm_config = array(
    'allow_edit' => '_MD_D3DIARY_PERMDESC_ALLOW_EDIT',
    'allow_html' => '_MD_D3DIARY_PERMDESC_ALLOW_HTML',
    'allow_regdate' => '_MD_D3DIARY_PERMDESC_ALLOW_REGDATE'
);

if( $d3dConf->mod_config['use_mailpost'] == 1) 
	{ $gperm_config['allow_mailpost'] = '_MD_D3DIARY_PERMDESC_ALLOW_MAILPOST'; }

if( $d3dConf->gPerm->use_gp == 1) 
	{ $gperm_config['allow_gpermission'] = '_MD_D3DIARY_PERMDESC_ALLOW_GPERM'; }
if( $d3dConf->gPerm->use_pp == 1) 
	{ $gperm_config['allow_ppermission'] = '_MD_D3DIARY_PERMDESC_ALLOW_PPERM'; }

// module ID 
$module_handler =& xoops_gethandler('module');
$this_module =& $module_handler->getByDirname($mydirname);
$mid = $this_module->getVar('mid');
$mod_name = $this_module->getVar('name');

// renew group permissions
if(!empty( $_POST['gperm_renew'])) {
    if ( ! $xoopsGTicket->check( true , 'd3diary_admin' ) ) {
        redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
    }

     $cid = 0 ;

   // delete once
    $d3dConf->gPerm->deleteGroupPerm($cid, $d3dConf->mid);
    
    // And renew
    foreach(array_keys($group_list) as $gid ) {
        foreach(array_keys($gperm_config) as $gperm_name) {
            if(!empty($_POST[$gperm_name][$gid])) {
		$d3dConf->gPerm->gperm_groupid = intval($gid);
		$d3dConf->gPerm->gperm_itemid = $cid;
		$d3dConf->gPerm->gperm_modid = $d3dConf->mid;
		$d3dConf->gPerm->gperm_name = addslashes($gperm_name);
                if(!$d3dConf->gPerm->insertGroupPerm()) {
                    redirect_header(XOOPS_URL.'/modules/'.$mydirname.'/admin/index.php?page=permission', 2, _MD_D3DIARY_MESSAGE_DBUPDATE_FAILED);
                    exit();
                }
            }
        }        
    }
    
    redirect_header( XOOPS_URL.'/modules/'.$mydirname.'/admin/index.php?page=permission&amp;cid='.$cid, 1, _MD_D3DIARY_MESSAGE_DBUPDATE_SUCCESS ) ;
    exit ;
}

// get permission by group_id
$gperms = $d3dConf->gPerm->getPermsByGroup(0, $d3dConf->mid);
$groupperm = array();     // array by category id, extensible for future
foreach( $group_list as $gid=>$gname ) {
    $gid = intval($gid);
    $gname = $d3dConf->func->htmlspecialchars($gname);
    $groupperm[$cid][$gid] = array();
    $groupperm[$cid][$gid]['gid'] = $gid;
    $groupperm[$cid][$gid]['gname'] = $gname;
    $groupperm[$cid][$gid]['perms'] = array();
    foreach(array_keys($gperm_config) as $gperm_name) {
        if(isset($gperms[$cid][$gid][$gperm_name]))
            $groupperm[$cid][$gid]['perms'][$gperm_name] = 1;
        else
            $groupperm[$cid][$gid]['perms'][$gperm_name] = 0;
    }
}

xoops_cp_header();
include dirname(__FILE__).'/mymenu.php' ;
$tpl = new XoopsTpl() ;
$tpl->assign( array(
    'mydirname' => $mydirname,
    'mod_url' => sprintf("%s/modules/%s", XOOPS_URL, $mydirname),
    'myname' => $mod_name,
    'modConfig' => $xoopsModuleConfig,
    'cid' => $cid ,
    'category_name' => _MD_D3DIARY_LANG_CATEGORY_GLOBAL,
    'gperm_config' => $gperm_config,
    'groupperms' => $groupperm,
    'group_list' => $group_list,
    'gticket_hidden' => $xoopsGTicket->getTicketHtml( __LINE__ , 1800 , 'd3diary_admin') ,
) ) ;
$tpl->display( 'db:'.$mydirname.'_admin_permission.html' ) ;
xoops_cp_footer();

?>