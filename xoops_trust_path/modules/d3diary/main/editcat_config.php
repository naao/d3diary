<?php

//--------------------------------------------------------------------
// Config
//--------------------------------------------------------------------

include_once dirname( dirname(__FILE__) ).'/class/category.class.php';
include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';

$category =& D3diaryCategory::getInstance();

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

$d3dConf = & D3diaryConf::getInstance($mydirname, $req_uid, "editcat_config");
$func =& $d3dConf->func ;
$mod_config =& $d3dConf->mod_config ;
$myts =& $d3dConf->myts;
$mPerm =& $d3dConf->mPerm ;
$gPerm =& $d3dConf->gPerm ;

//--------------------------------------------------------------------
// GET Initial Valuses
//--------------------------------------------------------------------

$myname = "editcat_config.php";

$uname = $d3dConf->uname;
$name = $d3dConf->name;

// define Template
$xoopsOption['template_main']= $mydirname.'_editcat_config.html';

include XOOPS_ROOT_PATH."/header.php";
// this page uses smarty template
// this must be set before including main header.php

$_tempGperm = $gPerm->getUidsByName( array_keys($gPerm->gperm_config) );
// check edit permission by group
if(!empty($_tempGperm['allow_edit'])){
	if(!isset($_tempGperm['allow_edit'][$uid])) {
		redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
		exit();
	}
} else {
	redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
	exit();
}

// dort /rename / add / delete（ --> non nategory ）
$common_cat=$func->getpost_param('common_cat') ? intval($func->getpost_param('common_cat')) : 0 ;

// uid overrides for common category
if ($common_cat==1){
	$category->uid=0;
} else {
	$category->uid=$uid;
}

$cid = $category->cid=intval($func->getpost_param('cid'));

// edit
if(!empty($_POST['submit1']) and $cid>0){
	$category->readdb($mydirname);
	$category->blogtype=intval($func->getpost_param('blogtype'));
	$category->blogurl=$func->getpost_param('blogurl');
	$category->rss=$func->getpost_param('rss');

	if($category->blogtype>0 and empty($category->blogurl)){
		redirect_header('index.php?page=editcat_config',3,_MD_FAIL_UPDATED._MD_NODIARYURL);
		exit();
	}

	$_url = $category->blogurl;
	$_rss = $category->rss;
	
	// $_url, $_rss are by ref value
	if ( $func->get_ext_rssurl( $category->blogtype, $_url, $_rss )!=true ) {
		redirect_header('index.php?page=editcat_config',3,_MD_FAIL_UPDATED._MD_NORSSURL);
		exit();
	} else {
		$category->blogurl = $_url;
		$category->rss = $_rss;
	}

	$category->openarea=intval($func->getpost_param('openarea'));
	$category->dohtml=intval($func->getpost_param('dohtml'));
	$chk_vgids= $func->getpost_param('vgids');
	$category->vgids = $chk_vgids ? "|".implode("|", array_map("intval" ,$chk_vgids))."|" : "";
	$chk_vpids= $func->getpost_param('vpids');
	$category->vpids = $chk_vpids ? "|".implode("|", array_map("intval" ,explode("," , $chk_vpids)))."|" : "";
	
	$category->updatedb($mydirname);

	if($d3dConf->dcfg->blogtype==0){
		// このブログ
		d3diary_update_newentry_cat($mydirname, $uid, $cid);
	}

	redirect_header("index.php?page=editcategory",2,_MD_CATEGORY_UPDATED);

// input form
}else{
	$category->readdb($mydirname);

	$yd_category['cid']   = $category->cid;
	$yd_category['cname']   = htmlspecialchars($category->cname, ENT_QUOTES);
	$yd_category['corder']   = $category->corder;
	$yd_category['blogtype']   = $category->blogtype;
	$yd_category['blogurl']   = $category->blogurl;
	$yd_category['rss']   = $category->rss;
	$yd_category['openarea']   = $category->openarea;
	$yd_category['dohtml']   = $category->dohtml;

	$_selcted = explode( "|", trim( $category->vgids ,"|" ) );

	//var_dump($gPerm->group_list);
	$yd_category['group_list'] = array();
	$_oc = (int)$mod_config['use_open_cat'];
	if( $_oc == 10 || $_oc == 20 ) {
		$_selcted = explode( "|", trim( $category->vgids ,"|" ) );
		$group_list = array();
		foreach ( $gPerm->group_list as $_gid => $_name) {
		    if($_gid >= 4 && (in_array($_gid, $mPerm->mygids) || $mPerm->isadmin)){
			$group_list[$_gid]['gname'] = $_name;
			$group_list[$_gid]['gsel'] = (in_array( $_gid, $_selcted )) ? 1 : 0;
		    }
		}
		$yd_category['group_list'] = $group_list;
	}
	if( $_oc == 20 ) {
		$p_selcted = array_map("intval", explode( "|", trim( $category->vpids ,"|" )) );
		$pperm_list = implode( "," , $p_selcted ) ;
		$yd_category['pperm_list'] = $pperm_list;
		$unames = array(); $names = array();

		foreach ($p_selcted as $vpid) {
			if( $vpid >1 ) {
				$rtn = $func->get_xoopsuname($vpid);
				$uname = $rtn['uname'];
				$name = (!empty($rtn['name'])) ? $rtn['name'] : "" ;
				$unames[] = htmlspecialchars( $uname.'['.$vpid.'] ', ENT_QUOTES );
				$names[] = htmlspecialchars( $name.'['.$vpid.'] ', ENT_QUOTES );
			}
		}
		if( $mod_config['use_name'] == 1 ) {
			$yd_category['pperm_names'] = $names;
		} else {
			$yd_category['pperm_names'] = $unames;
		}
	}
}

	// assign module header for tags
	$d3diary_header = '<link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=main_css" />'."\r\n";
	if(!empty($_tempGperm['allow_ppermission'])){
		if(isset($_tempGperm['allow_ppermission'][$uid])){
			$d3diary_header .= '<script type="text/javascript" src="'.XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=loader&src=prototype,suggest,log.js"></script>'."\r\n";
		}
	}

// breadcrumbs
	$bc_para['diary_title'] = $xoopsTpl->get_template_vars('xoops_modulename');
	$bc_para['path'] = "index.php?page=editcat_config";
	$bc_para['uname'] = $uname;
	$bc_para['name'] = (!empty($name)) ? $name : $uname ;
	$bc_para['mode'] = "editcat_config";
	$bc_para['bc_name'] = constant('_MD_CATEGORY_EDIT');
	$bc_para['bc_name2'] = htmlspecialchars( $category->cname, ENT_QUOTES ) ;
	
	$breadcrumbs = $func->get_breadcrumbs( $uid, $bc_para['mode'], $bc_para );
	//var_dump($breadcrumbs);


$xoopsTpl->assign(array(
		"yd_uid" => $uid,
		"yd_uname" => $uname,
		"yd_name" => $name,
		"yd_isadmin" => $mPerm->isadmin,
		"yd_use_friend" => intval($mod_config['use_friend']),
		"yd_use_open_cat" => intval($mod_config['use_open_cat']),
		"yd_cfg" => $yd_category,
		"yd_openarea" => intval($d3dConf->dcfg->openarea),
		"common_cat" => $common_cat,
		"mydirname" => $mydirname,
		"mod_config" => $mod_config,
		"charset" => _CHARSET,
		"xoops_breadcrumbs" => $breadcrumbs,
		"xoops_module_header" => 
			$xoopsTpl->get_template_vars( 'xoops_module_header' ).$d3diary_header,
		"allow_edit" => !empty($_tempGperm['allow_edit']) ? isset($_tempGperm['allow_edit'][$uid]) : false,
		"allow_html" => !empty($_tempGperm['allow_html']) ? isset($_tempGperm['allow_html'][$uid]) : false,
		"allow_regdate" => !empty($_tempGperm['allow_regdate']) ? isset($_tempGperm['allow_regdate'][$uid]) : false
		));
		
	if(!empty($_tempGperm['allow_gpermission']))
		{ $xoopsTpl->assign( 'allow_gpermission' , isset($_tempGperm['allow_gpermission'][$uid])); }
	if(!empty($_tempGperm['allow_ppermission']))
		{ $xoopsTpl->assign( 'allow_ppermission' , isset($_tempGperm['allow_ppermission'][$uid])); }
	

// newentry更新
function d3diary_update_newentry_cat($mydirname, $uid, $cid)
{
	global $xoopsDB;
	
	$sql = "DELETE FROM ".$xoopsDB->prefix($mydirname.'_newentry')." WHERE uid='".$uid."' AND cid='".$cid."'";
	$result = $xoopsDB->queryF($sql);
	
	$sql = "SELECT *
			  FROM ".$xoopsDB->prefix($mydirname.'_diary')."
	          WHERE uid='".intval($uid)."' AND cid='".$cid."' ORDER BY create_time DESC";

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
				'".$cid."',
				'".$tmptitle."',
				'".XOOPS_URL."/modules/".$mydirname."/index.php?page=detail&bid=".$dbdat['bid']."',
				'".$dbdat['create_time']."',
				'0'
				)";
		$result = $xoopsDB->queryF($sql);
	}

}
?>
