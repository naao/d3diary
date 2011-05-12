<?php

// ===========================================================================
// The permission file is necessary in each module.
// If target module is "D3 module", the permission file is necessary
// in each directory in XOOPS_TRUST_PATH (not XOOPS_ROOT_PATH).
//
// -- argument --
// 
// $mydirname			: attachfile's dirname in XOOPS_ROOT_PATH
// $module_dirname		: target module's dirname in XOOPS_ROOT_PATH
// $mytrustdirname		: attachfile's dirname in XOOPS_TRUST_PATH
// $targettrustdirname	: target module's dirname in XOOPS_TRUST_PATH
// $target_id			: target mosule's contents id (target to attach)
// 
// -- return value --
// 
// true					: allow access
// false				: deny access
// ===========================================================================

function attachfile_check_upload_permission_plugin( $mydirname , $module_dirname , $mytrustdirname , $targettrustdirname , $target_id )
{
	// emulate d3diary
	$mytrustdirname = $targettrustdirname ;
	$mytrustdirpath = XOOPS_TRUST_PATH.'/modules/'.$targettrustdirname ;
	$mydirname = $module_dirname ;

	include_once $mytrustdirpath.'/class/diary.class.php';
	include_once $mytrustdirpath.'/class/category.class.php';
	include_once $mytrustdirpath.'/class/d3diaryConf.class.php' ;

	$diary =& Diary::getInstance();
	$cat =& Category::getInstance();
	
	$diary->bid = $target_id ;
	$diary->readdb($mydirname);
	if(empty($diary->uid)){
		return false;
	}
	
	$d3dConf = & D3diaryConf::getInstance ( $mydirname, (int)$diary->uid, "attachfile" ) ;
	$d3dConf->mPerm->get_allowed_openarea();

	$uid = $d3dConf->uid;

	$editperm=0;
	$owner=0;
	$_tempGperm = $d3dConf->gPerm->getUidsByName( array('allow_edit') );
	// check edit permission by group
	if(in_array($uid, $_tempGperm['allow_edit'])) {
		if($diary->uid==$uid){$owner=1;$editperm=1;}
		if($d3dConf->mPerm->isadmin){$editperm=1;}
	}	unset($_tempGperm);

	if ($editperm==1) {
		return true; 
	} else {
		return false;
	}
}

function attachfile_check_download_permission_plugin( $mydirname , $module_dirname , $mytrustdirname , $targettrustdirname , $target_id )
{
	// emulate d3diary
	$mytrustdirname = $targettrustdirname ;
	$mytrustdirpath = XOOPS_TRUST_PATH.'/modules/'.$targettrustdirname ;
	$mydirname = $module_dirname ;

	include_once $mytrustdirpath.'/class/diary.class.php';
	include_once $mytrustdirpath.'/class/category.class.php';
	include_once $mytrustdirpath.'/class/d3diaryConf.class.php' ;

	$diary =& new Diary();
	$cat =& new Category();
	
	$diary->bid = $target_id ;
	$diary->readdb($mydirname);
	if(empty($diary->uid)){
		return false;
	}

	$d3dConf = & D3diaryConf::getInstance ( $mydirname, (int)$diary->uid, "attachfile" ) ;
	$d3dConf->mPerm->get_allowed_openarea();

	$uid = $d3dConf->uid;

	$cat->uid = $diary->uid;
	$cat->cid = $cid = $diary->cid;
	$cat->getchildren($mydirname);

	$_tmp_isfriend  = $d3dConf->mPerm->check_is_friend($diary->uid);
	$_tmp_isfriend2 = $d3dConf->mPerm->check_is_friend2($diary->uid);
	
	$_tmp_op = intval($d3dConf->dcfg->openarea);
		list( $_got_op , $_slctd_op , $_tmp_gperms, $_tmp_pperms ) 
			= $d3dConf->mPerm->override_openarea( $_tmp_op, intval($diary->openarea), intval($cat->openarea), 
				$diary->vgids, $diary->vpids, $cat->vgids, $cat->vpids );

		$yd_data['openarea'] = $_got_op;

			// var_dump($_tmp_gperms); var_dump($_tmp_pperms);
		$yd_data['can_disp'] = $d3dConf->mPerm->can_display($diary->uid, $_got_op, 
				$diary->create_time, $_tmp_isfriend, $_tmp_isfriend2, $_tmp_gperms, $_tmp_pperms);


	return $yd_data['can_disp'] ; 
}

?>