<?php

//--------------------------------------------------------------------
// Config
//--------------------------------------------------------------------

	include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';

global $xoopsUser,$xoopsDB;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$constpref = '_MB_' . strtoupper( $mydirname ) ;

$d3dConf =& D3diaryConf::getInstance($mydirname, 0, "bloggerlist");
$myts =& $d3dConf->myts;

	$uid = $d3dConf->uid;
	$req_uid = $d3dConf->req_uid; // overrided by d3dConf

	$params['ofst_key'] = "bgofst" ;
	$_offset_ = $d3dConf->func->getpost_param($params['ofst_key']);
	$offset = isset($_offset_) ?(int)$_offset_ : 0;

	//$max_entry = 100;
	$max_entry = (int)$d3dConf->mod_config['block_diarynum'];
	$params['getnav'] = true ;	// not render but get navi

	$params['order'] = htmlspecialchars( $d3dConf->func->getpost_param('odr'), ENT_QUOTES ) ;
	$params['order'] = $params['order'] ? $params['order'] :'time' ; 

	list( $blogger, $blogger2, $bloggernavi ) = $d3dConf->func->get_bloggerlist ( $req_uid, $uid, $max_entry, $offset, $params );
	
	// create url for sort
	$url = '';
	if( !empty($_SERVER['QUERY_STRING'])) {
		if( !ereg("^odr=[0-9a-z_]+", $_SERVER['QUERY_STRING']) ) {
			$url = preg_replace("/^(.*)\&odr=[0-9a-z_]+/", "$1", $_SERVER['QUERY_STRING']);
		}
	}
        $sort_baseurl = XOOPS_URL.'/modules/'.$mydirname.'/index.php?'.$url;

// define Template
$xoopsOption['template_main']= $mydirname.'_bloggerlist.html';

include XOOPS_ROOT_PATH."/header.php";

// assign module header for css
$d3diary_header = '<link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=main_css" />'."\r\n";

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
			'bloggernavi' => $bloggernavi,
			'mydirname' => $mydirname,
			'sort_baseurl' => $sort_baseurl,
			'xoops_module_header' => 
				$xoopsTpl->get_template_vars( 'xoops_module_header' ).$d3diary_header,
			'mod_config' => $d3dConf->mod_config,
			'xoops_breadcrumbs' => $breadcrumbs
		));

include_once XOOPS_ROOT_PATH.'/footer.php';

?>