<?php

//--------------------------------------------------------------------
// Config
//--------------------------------------------------------------------

	include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';

global $xoopsUser,$xoopsDB;

//	$max_entry = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
	$max_entry = 100;
//	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$constpref = '_MB_' . strtoupper( $mydirname ) ;

$d3dConf =& D3diaryConf::getInstance($mydirname, 0, "bloggerlist");
$myts =& $d3dConf->myts;

	$uid = $d3dConf->uid;

	// first, get external blogger list
	$sql = "SELECT DISTINCT cfg.uid, u.name, u.uname from "
			.$xoopsDB->prefix($mydirname."_config")." cfg LEFT JOIN "
			.$xoopsDB->prefix("users")
			." u ON cfg.uid=u.uid WHERE cfg.blogtype>'0'" ;
	$result = $xoopsDB->query($sql);
	
	$blogger2 = array(); $blogger2_ids = array(); 
	while( $row = $xoopsDB->fetchArray( $result ) ) {
		$blogger2[]=$row;
		$blogger2_ids[]=$row['uid'];
	}
	
	$now_order=intval($d3dConf->func->getpost_param('order'));

	if ( $now_order == 'time' ) {
		$whr_order = "max_create_time DESC";
	} elseif ( $now_order == 'posted' ) {
		$whr_order = "count DESC";
	} else {
		$whr_order = "max_create_time DESC";
	}

	$sql = "SELECT d.uid, count(d.uid) AS count, MAX(d.create_time) AS max_create_time, u.name, u.uname from "
			.$xoopsDB->prefix($mydirname."_diary")." d LEFT JOIN "
			.$xoopsDB->prefix("users")
			." u ON d.uid=u.uid GROUP BY d.uid, u.name, u.uname ORDER BY ".$whr_order." LIMIT ".$max_entry ;
	$blogger = array();
	$result = $xoopsDB->query($sql);
	while( $row = $xoopsDB->fetchArray( $result ) ) {
		// exclude external bloggers
		if(!in_array($row['uid'],$blogger2_ids)) $blogger[]=$row;
	}
	
	//$mid = $d3dConf->mid;
	$yd_config = $d3dConf->mod_config;
	
// define Template
$xoopsOption['template_main']= $mydirname.'_bloggerlist.html';

include XOOPS_ROOT_PATH."/header.php";

// breadcrumbs
	$bc_para['diary_title'] = $xoopsTpl->get_template_vars('xoops_modulename');
	$bc_para['path'] = "index.php?page=bloggerlist";
	$bc_para['mode'] = "bloggerlist";
	$bc_para['uname'] = "";
	$bc_para['name'] = "";
	$bc_para['bc_name'] = constant('_MD_POSTER_LIST');
	
	$breadcrumbs = $d3dConf->func->get_breadcrumbs( 0, $bc_para['mode'], $bc_para );

	$xoopsTpl->assign(array(
			'blogger' => $blogger,
			'blogger2' => $blogger2,
			'mydirname' => $mydirname,
			'mod_config' => $d3dConf->mod_config,
			'xoops_breadcrumbs' => $breadcrumbs
		));

include_once XOOPS_ROOT_PATH.'/footer.php';

?>