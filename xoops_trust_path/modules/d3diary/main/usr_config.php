<?php

//--------------------------------------------------------------------
// Config
//--------------------------------------------------------------------

include_once dirname( dirname(__FILE__) ).'/class/diaryconfig.class.php';
include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';

	global $xoopsUser ;
	if (is_object( @$xoopsUser )){
		$uid = intval($xoopsUser->getVar('uid'));
	} else {
		$uid = 0 ;
	}

	if($uid<=0) {
		redirect_header(XOOPS_URL.'/user.php',2,_MD_IVUID_ERR);
		exit();
	}

$d3dConf = & D3diaryConf::getInstance($mydirname, 0, "usr_config");
$func =& $d3dConf->func ;
$dcfg =& $d3dConf->dcfg;
$myts =& $d3dConf->myts;
$mPerm =& $d3dConf->mPerm ;
$gPerm =& $d3dConf->gPerm ;
$mod_config =& $d3dConf->mod_config ;

//--------------------------------------------------------------------
// GET Initial Values
//--------------------------------------------------------------------

if( $uid<=0 ){
    redirect_header(XOOPS_URL.'/user.php',2,_MD_IVUID_ERR);
	exit();
}

$d3dConf->req_uid = $req_uid = 0<(int)$func->getpost_param('req_uid') ? (int)$func->getpost_param('req_uid') : $uid;
$mPerm->ini_set();

if ( $mPerm->isadmin && 0 < $d3dConf->req_uid ) {
	$req_uid = $d3dConf->req_uid;
	$query_req_uid = "&amp;req_uid=".$req_uid;
	$rtn = $func->get_xoopsuname($req_uid);
	$uname = $rtn['uname'];
	$name = (!empty($rtn['name'])) ? $rtn['name'] : "" ;
	$rtn = $func->get_xoopsuname($uid);
	$myuname = $rtn['uname'];
	$myname = (!empty($rtn['name'])) ? $rtn['name'] : "" ;
} elseif ( !$mPerm->isadmin && 0 < $d3dConf->req_uid && $d3dConf->req_uid != $uid ) {
	redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
	exit;
} else {
	$req_uid = $uid;
	$query_req_uid = "";
	$rtn = $func->get_xoopsuname($uid);
	$uname = $rtn['uname'];
	$name = $rtn['name'];
}

$_tempGperm = $gPerm->getUidsByName( array_keys($gPerm->gperm_config) );
// check edit permission for access user's group
if(!empty($_tempGperm['allow_edit'])){
	if(!isset($_tempGperm['allow_edit'][$uid])) {
		redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
		exit();
	}	//unset($_tempGperm);
} else {
	redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
	exit();
}

// check mailpost permission for access user's group
$allow_mailpost = 0;
if(!empty($_tempGperm['allow_mailpost'])){
	if(isset($_tempGperm['allow_mailpost'][$req_uid])) {
		$allow_mailpost = 1;
	}
}

// define Template
$xoopsOption['template_main']= $mydirname.'_usr_config.html';

include XOOPS_ROOT_PATH."/header.php";
// this page uses smarty template
// this must be set before including main header.php

// change config
if(!empty($_POST['submit1'])){

	$dcfg->uid = $req_uid;

	$dcfg->blogurl= $func->htmlspecialchars($func->getpost_param('blogurl'));
	$dcfg->blogtype= intval($func->getpost_param('blogtype'));
	$dcfg->rss= $func->htmlspecialchars($func->getpost_param('rss'));
	$dcfg->openarea= intval($func->getpost_param('openarea'));
	$dcfg->keep = intval($func->getpost_param('jump'));
	if ($dcfg->blogtype==0 && $allow_mailpost==1) {
		$dcfg->mailpost = intval($func->getpost_param('mailpost'));
		$dcfg->address = $func->htmlspecialchars($func->getpost_param('address'));
		$dcfg->uptime = intval($func->getpost_param('uptime'));
	} else {
		$dcfg->mailpost = 0;
		$dcfg->address = '';
		$dcfg->uptime = 0;
	}

	// set update time before uptime ago
	$dcfg->updated = time() - $dcfg->uptime;

	if($dcfg->blogtype>0 and empty($dcfg->blogurl)){
		redirect_header(XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=usr_config'.$query_req_uid,3,_MD_FAIL_UPDATED._MD_NODIARYURL);
		exit();
	}

	$_url = $dcfg->blogurl;
	$_rss = $dcfg->rss;
	
	// $_url, $_rss are by ref value
	if ( $func->get_ext_rssurl( $dcfg->blogtype, $_url, $_rss )!=true ) {
		redirect_header(XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=usr_config'.$query_req_uid,3,_MD_FAIL_UPDATED._MD_NORSSURL);
		exit();
	} else {
		$dcfg->blogurl = $_url;
		$dcfg->rss = $_rss;
	}

	//var_dump($dcfg);  echo("<br />");

	// check email for mailpost when it's enabled
	if ( d3diary_check_existmail($mydirname, $uid, $dcfg->address) == true) {
		redirect_header(XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=usr_config'.$query_req_uid,3,_MD_CONF_AL_EXISTMAIL);
	} else {

		if($dcfg->blogtype==0){
			// update this diary
			d3diary_update_newentry($mydirname, $uid);
		}

		$dcfg->deletedb($mydirname);
		$dcfg->insertdb($mydirname);
		redirect_header( $func->htmlspecialchars($func->getpost_param('referrer')), 3,_MD_CONF_UPDATED );
	}


// show config
} else {

	//--------------------------------------------------------------------
	// Read Config
	//--------------------------------------------------------------------
	$sql = "SELECT *
			  FROM ".$xoopsDB->prefix($mydirname.'_config').
			  " WHERE uid='".$req_uid."'";

	$result = $xoopsDB->query($sql);

	if ( $dbdat = $xoopsDB->fetchArray($result) ) {
		$yd_cfg['blogtype'] = $dbdat['blogtype'];
		$yd_cfg['openarea'] = $dbdat['openarea'];
		$yd_cfg['mailpost'] = $dbdat['mailpost'];
		$yd_cfg['address'] = $dbdat['address'];
		$yd_cfg['jump'] = $dbdat['keep'];
		$yd_cfg['uptime'] = $dbdat['uptime'];
		if($dbdat['blogtype']>0){
			$yd_cfg['blogurl'] = $dbdat['blogurl'];
			if($dbdat['blogtype']==100){
				$yd_cfg['rss'] = $dbdat['rss'];
			}
		}
	}else{
		$yd_cfg['blogtype'] = 0;
		$yd_cfg['openarea'] = 0;
		$yd_cfg['mailpost'] = 0;
		$yd_cfg['address'] = '';
		$yd_cfg['jump'] = 0;
		$yd_cfg['uptime'] = 0;
	}
}

// breadcrumbs
	$bc_para['diary_title'] = $xoopsTpl->get_template_vars('xoops_modulename');
	$bc_para['path'] = "index.php";
	$bc_para['uname'] = $uname;
	$bc_para['name'] = (!empty($name)) ? $name : $uname ;
	$bc_para['mode'] = "usr_config";
	$bc_para['bc_name'] = constant('_MD_CONF_LINK');
	
	$breadcrumbs = $func->get_breadcrumbs( $req_uid, $bc_para['mode'], $bc_para );
	//var_dump($breadcrumbs);

$xoopsTpl->assign(array(
		"req_uid" => $req_uid,
		"query_req_uid" => $query_req_uid,
		"yd_uid" => $uid,
		"yd_uname" => $uname,
		"yd_name" => $name,
		"yd_cfg" => $yd_cfg,
		"yd_mailpost"	=> $allow_mailpost,
		"yd_use_friend" => $mod_config['use_friend'],
		"mydirname" => $mydirname,
		"mod_config" => $mod_config,
		"xoops_breadcrumbs" => $breadcrumbs
		));


// newentry update
function d3diary_update_newentry($mydirname, $uid)
{
	global $xoopsDB;
	
	$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_newentry')." WHERE uid='".$uid."' AND cid='0'";
	$result = $xoopsDB->queryF($sql);
	
	$sql = "SELECT *
			  FROM ".$xoopsDB->prefix($mydirname.'_diary')."
	          WHERE uid='".intval($uid)."' ORDER BY create_time DESC";

	$result = $xoopsDB->query($sql);
	if ( $dbdat = $xoopsDB->fetchArray($result) ) {
	
        if (!get_magic_quotes_gpc()) {
			$tmptitle=addslashes($dbdat['title']);
		}else{
			$tmptitle=$dbdat['title'];
		}
		
		$sql = "INSERT INTO ".$xoopsDB->prefix($mydirname.'_newentry')."
				(uid, cid, title, url, create_time, blogtype)
				VALUES (
				'".$dbdat['uid']."',
				'0',
				'".$tmptitle."',
				'".XOOPS_URL."/modules/".$mydirname."/index.php?page=detail&bid=".$dbdat['bid']."',
				'".$dbdat['create_time']."',
				'0'
				)";
		$result = $xoopsDB->queryF($sql);
	}

}

function d3diary_check_existmail($mydirname, $uid, $_address)
{
	global $xoopsDB;
	
	if ( empty($_address) ) { return false ; }

	$sql1 = "SELECT uid from ".$xoopsDB->prefix('users')." WHERE email = '".$_address."'";
	$result = $xoopsDB->query($sql1,1,0);
	$exist1 = $xoopsDB -> fetchArray($result);

	$sql2 = "SELECT uid from ".$xoopsDB->prefix($mydirname.'_config')." WHERE address = '".$_address."' 
			AND uid <> '".$uid."'";
	$result = $xoopsDB->query($sql2,1,0);
	$exist2 = $xoopsDB -> fetchArray($result);

	if ( empty($exist1) && empty($exist2) ) { return false; }
	
	return true;
}

?>
