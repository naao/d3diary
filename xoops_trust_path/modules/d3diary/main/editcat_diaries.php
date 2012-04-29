<?php

//--------------------------------------------------------------------
// Config
//--------------------------------------------------------------------

include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';

$d3dConf = & D3diaryConf::getInstance($mydirname);
$func =& $d3dConf->func ;
$mPerm =& $d3dConf->mPerm ;
$gPerm =& $d3dConf->gPerm ;

$uid = $d3dConf->uid;
if( $uid<=0 ){
    redirect_header(XOOPS_URL.'/user.php',2,_MD_IVUID_ERR);
	exit();
}

$d3dConf->set_mod_config(0,"editcat_diaries");	// needs $dcfg
$uname = $d3dConf->uname;

$_tempGperm = $gPerm->getUidsByName( array('allow_edit') );
// check edit permission by group
if(!empty($_tempGperm['allow_edit'])){
	if(!isset($_tempGperm['allow_edit'][$uid])) {
		redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
		exit();
	}
} else {
		redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
		exit();
}	unset($_tempGperm);

$req_uid = intval($func->getpost_param('req_uid'));	
$org_cid = intval($func->getpost_param('cid'));	
$swap_cid= intval($func->getpost_param('swap_cid'));	
$chk_bids= $func->getpost_param('chk_bids');		
$sel_bids = $chk_bids ? implode(",", array_map("intval" ,$chk_bids)) : Array();

if(!$mPerm->check_exist_user($req_uid)){
	if($uid>0){
		redirect_header(XOOPS_URL.'/modules/'.$mydirname.'/index.php',4,_MD_IVUID_ERR);
	}else{
		header("Location:". XOOPS_URL.'/modules/'.$mydirname.'/diarylist.php');
	}
	exit();
}

//if( !$sel_bids || $swap_cid==$org_cid ){
if( !$sel_bids  ){
		header("Location:". XOOPS_URL.'/modules/'.$mydirname.'/index.php?req_uid='.$req_uid.'&mode=category&cid='.$org_cid);
}

require XOOPS_ROOT_PATH.'/header.php';

$owner=0; $editperm=0;
if($req_uid==$uid){$owner=1;$editperm=1;}
if($mPerm->isadmin){$editperm=1;}	

if($editperm != 1) {
    redirect_header(XOOPS_URL.'/',4,_MD_NOPERM_VIEW);
	exit();
}

	$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_diary').
		" SET cid='".$swap_cid."'
		WHERE uid='".$req_uid."' and bid IN (".$sel_bids.")";

	$result = $xoopsDB->query($sql);

	redirect_header("index.php?req_uid=".$req_uid."&mode=category&cid=".$swap_cid,2,_MD_CATEGORY_UPDATED);

?>
