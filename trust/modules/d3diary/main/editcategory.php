<?php

//--------------------------------------------------------------------
// Config
//--------------------------------------------------------------------

include_once dirname( dirname(__FILE__) ).'/class/category.class.php';
include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';

$category =& Category::getInstance();

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

	$req_uid = $uid;

$d3dConf = & D3diaryConf::getInstance($mydirname, $req_uid, "editcategory");
$myts =& $d3dConf->myts;

//--------------------------------------------------------------------
// GET Initial Valuses
//--------------------------------------------------------------------

$myname = "editcategory.php";

$uname = $d3dConf->uname;
$name = $d3dConf->name;

$_tempGperm = $d3dConf->gPerm->getUidsByName( array('allow_edit') );
// check edit permission by group
if(!empty($_tempGperm['allow_edit'])){
	if(!in_array($uid, $_tempGperm['allow_edit'])) {
		redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
		exit();
	}	unset($_tempGperm);
} else {
	redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
	exit();
}

// dort /rename / add / delete¡Ê --> non nategory ¡Ë
$common_cat=$d3dConf->func->getpost_param('common_cat') ? intval($d3dConf->func->getpost_param('common_cat')) : 0 ;
// uid overrides for common category
if ($common_cat==1){
	$category->uid=0;
} else {
	$category->uid=$uid;
}
$category->cid=intval($d3dConf->func->getpost_param('cid'));

// define Template
$xoopsOption['template_main']= $mydirname.'_editcategory.html';
include XOOPS_ROOT_PATH."/header.php";
// this page uses smarty template
// this must be set before including main header.php

$yd_common_cat = array();

// edit
if(!empty($_POST['editsub']) and $category->cid>0){
	$corder = ( $category->cid < 10000 ) ? (int)$_POST['corder'] : (int)$_POST['corder'] + 10000;
	
	$category->readdb($mydirname);
	if( $corder != $category->corder ) {
		$category->corder = d3diary_change_corder($mydirname, $category->cid, $category->corder, $corder);
	}
	
	$category->cname= $d3dConf->func->getpost_param('cname');
	$category->subcat= intval($d3dConf->func->getpost_param('subcat'));
	if(empty($category->cname)){
		redirect_header("editcategory.php",2,_MD_CATEGORY_NONAME);exit();
	}
	$category->updatedb($mydirname);
	redirect_header("index.php?page=editcategory",2,_MD_CATEGORY_UPDATED);

// create
}elseif(!empty($_POST['createsub'])){
	$category->cname= $d3dConf->func->getpost_param('cname');
	if(empty($category->cname)){
		redirect_header("index.php?page=editcategory",2,_MD_CATEGORY_NONAME);exit();
	}
	$category->insertdb($mydirname, $common_cat);
	redirect_header("index.php?page=editcategory",2,_MD_CATEGORY_CREATED);

// delete
}elseif(!empty($_POST['delsub'])){
	$category->deletedb($mydirname);
	
	// update blogs
	if ($common_cat==1){
		$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_diary')." SET cid='0'
			WHERE cid='".$category->cid."'";
	} else {
		$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_diary')." SET cid='0'
			WHERE uid='".$uid."' and cid='".$category->cid."'";
	}
	$result = $xoopsDB->query($sql);

	redirect_header("index.php?page=editcategory",2,_MD_CATEGORY_DELETED);

// swap  .. it's old and conventional function, to be removed in future
}elseif(!empty($_POST['swapsub'])){
	$cid1=$d3dConf->func->getpost_param('cid1');
	$cid2=$d3dConf->func->getpost_param('cid2');
	
	$category->cid=$cid1;
	$category->readdb($mydirname);
	if(empty($category->cname)){
		redirect_header("index.php?page=editcategory",2,_MD_IVCID);exit();
	}
	$corder1=$category->corder;
	
	$category->cid=$cid2;
	$category->readdb($mydirname);
	if(empty($category->cname)){
		redirect_header("index.php?page=editcategory",2,_MD_IVCID);exit();
	}
	$corder2=$category->corder;
	
	// update
	$category->corder=$corder1;
	$category->updatedb($mydirname);

	$category->cid=$cid1;
	$category->readdb($mydirname);
	$category->corder=$corder2;
	$category->updatedb($mydirname);
	
	redirect_header("index.php?page=editcategory",2,_MD_CATEGORY_SWAPPED);

// input form
}else{
	list( $yd_category, $yd_common_cat ) = d3diary_assign_category_foredit2($mydirname);
}

// breadcrumbs
	$bc_para['diary_title'] = $xoopsTpl->get_template_vars('xoops_modulename');
	$bc_para['path'] = "index.php?page=editcategory";
	$bc_para['uname'] = $uname;
	$bc_para['name'] = (!empty($name)) ? $name : $uname ;
	$bc_para['mode'] = "editcategory";
	$bc_para['bc_name'] = constant('_MD_CATEGORY_EDIT');
	
	$breadcrumbs = $d3dConf->func->get_breadcrumbs( $uid, $bc_para['mode'], $bc_para );
	//var_dump($breadcrumbs);
	
$xoopsTpl->assign(array(
		"yd_uid" => $uid,
		"yd_uname" => $uname,
		"yd_name" => $name,
		"yd_isadmin" => $d3dConf->mPerm->isadmin,
		"yd_use_open_cat" => intval($d3dConf->mod_config['use_open_cat']),
		"yd_category" => $yd_category,
		"yd_common_cat" => $yd_common_cat,
		"mydirname" => $mydirname,
		"mod_config" => $d3dConf->mod_config,
		"xoops_breadcrumbs" => $breadcrumbs
		));

function d3diary_assign_category_foredit2($mydirname){
	global $uid, $myts, $xoopsDB, $xoopsTpl, $d3dConf;
	
	$yd_category = array();
	$yd_common_cat = array();

	$sql = "SELECT * FROM ".$xoopsDB->prefix($mydirname.'_category')."
	          WHERE uid='".intval($uid)."' ORDER BY corder";

	$result = $xoopsDB->query($sql);

	$i=0;
	while ( $dbdat = $xoopsDB->fetchArray($result) ) {
		if($i>0){
			$_tmp_cat['cid_before']   = $yd_category[$i-1]['cid'];
			$yd_category[$i-1]['cid_after']   = (int)$dbdat['cid'];
		}
		$op = (int)$dbdat['openarea'];
		$_tmp_cat['cid']   = (int)$dbdat['cid'];
		$_tmp_cat['cname']   = $myts->makeTboxData4Show($dbdat['cname']);
		$_tmp_cat['corder']   = (int)$dbdat['corder'];
		$_tmp_cat['subcat']   = (int)$dbdat['subcat'];
		$_tmp_cat['blogtype']   = (int)$dbdat['blogtype'];
		$_tmp_cat['blogurl']   = $dbdat['blogurl'];
		$_tmp_cat['rss']   = $dbdat['rss'];
		$_tmp_cat['openarea']   = $op;
		$_tmp_cat['dohtml']   = (int)$dbdat['dohtml'];
		
		$yd_category[] = $_tmp_cat;
		$i++;
	}

	$sql = "SELECT * FROM ".$xoopsDB->prefix($mydirname.'_category')."
	          WHERE uid='0' ORDER BY corder";

	$result = $xoopsDB->query($sql);

	$i=0;
	while ( $dbdat = $xoopsDB->fetchArray($result) ) {
		if($i>0){
			$_tmp_cat['cid_before']   = $yd_common_cat[$i-1]['cid'];
			$yd_common_cat[$i-1]['cid_after']   = (int)$dbdat['cid'];
		}
		$op = (int)$dbdat['openarea'];
		$_tmp_cat['cid']   = (int)$dbdat['cid'];
		$_tmp_cat['cname']   = $myts->makeTboxData4Show($dbdat['cname']);
		$_tmp_cat['corder']   = ( $_tmp_cat['cid'] < 10000 ) ? (int)$dbdat['corder'] : (int)$dbdat['corder'] - 10000;
		$_tmp_cat['subcat']   = (int)$dbdat['subcat'];
		$_tmp_cat['blogtype']   = (int)$dbdat['blogtype'];
		$_tmp_cat['blogurl']   = $dbdat['blogurl'];
		$_tmp_cat['rss']   = $dbdat['rss'];
		$_tmp_cat['openarea']   = $op;
		$_tmp_cat['dohtml']   = (int)$dbdat['dohtml'];
		
		if($op ==10 || $op==20) {
			$_tmp_gperms = isset($dbdat['vgids']) ? 
					array_map("intval", explode('|', trim($dbdat['vgids'],'|'))) : array();
			if ($d3dConf->mPerm->isadmin || array_intersect($d3dConf->mPerm->mygids, $_tmp_gperms)) {
				$yd_common_cat[] = $_tmp_cat;
				$i++;
			}
		} elseif( $op == 20 ) {
			$_tmp_pperms = isset($dbdat['vpids']) ? 
					array_map("intval", explode( '|', trim( $dbdat['vpids'] ,'|' ))) : array();
			if ($d3dConf->mPerm->isadmin || in_array( $uid, $_tmp_pperms )) {
				$yd_common_cat[] = $_tmp_cat;
				$i++;
			}
		} else {
			$yd_common_cat[] = $_tmp_cat;
			$i++;
		}
	}
	return array( $yd_category, $yd_common_cat );
}

	// return modified corder
	function d3diary_change_corder($mydirname, $cid, $oldorder, $neworder) {
		global $uid, $xoopsDB, $d3dConf;
		
		if ( $cid < 10000 ) {
			$whr_uid = " uid='".$uid."'";
			$min_corder = 1;
		} else {
			$whr_uid = " uid='0'";
			$min_corder = 10001;
		}
		
		$sql = "SELECT corder FROM ".$xoopsDB->prefix($mydirname.'_category')."
				 WHERE corder='".$neworder."' AND".$whr_uid;
		
		$result = $xoopsDB->query($sql);
		while( $row = $xoopsDB->fetchArray( $result ) ) {
			$_corder = $row['corder'];
		}

		if ( !empty($_corder) ) {
			$sql = "SELECT MAX(corder) as max FROM ".$xoopsDB->prefix($mydirname.'_category')."
				 WHERE ".$whr_uid;
		
			$result = $xoopsDB->query($sql);
			while( $row = $xoopsDB->fetchArray( $result ) ) {
				$max_corder = $row['max'];
			}
			
			if ( $max_corder < $neworder ) {
				$neworder = $max_corder ;
			} elseif ($neworder < $min_corder) {
				$neworder = $min_corder ;
			}

			if ( $oldorder > $neworder ) {
				$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_category')." SET corder=corder+1 
				          WHERE '".$neworder."'<=corder AND corder<='".$oldorder."' AND cid<>'".$cid."' AND".$whr_uid;
			} elseif ( $oldorder < $neworder ) {
				$sql = "UPDATE ".$xoopsDB->prefix($mydirname.'_category')." SET corder=corder-1 
				          WHERE '".$oldorder."'<=corder AND corder<='".$neworder."' AND cid<>'".$cid."' AND".$whr_uid;
			}
		
			$result = $xoopsDB->query($sql);
		}
		
		return $neworder ;
	}


include_once XOOPS_ROOT_PATH.'/footer.php';

?>
	