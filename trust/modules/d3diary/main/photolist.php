<?php

//--------------------------------------------------------------------
// Config
//--------------------------------------------------------------------
include_once dirname( dirname(__FILE__) ).'/class/diary.class.php';
include_once dirname( dirname(__FILE__) ).'/class/category.class.php';
include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';

$diary =& Diary::getInstance();
$category =& Category::getInstance();

//--------------------------------------------------------------------
// GET Initial Valuses
//--------------------------------------------------------------------

$myname = "photolist.php";
$yd_list=array(); $yd_com_key=""; $yd_monthnavi="";

$d3dConf =& D3diaryConf::getInstance($mydirname, 0, "photolist");
$myts =& $d3dConf->myts;

$uid = $d3dConf->uid;
$req_uid = $d3dConf->req_uid;

if ($req_uid > 0) {
	$rtn = $d3dConf->func->get_xoopsuname($req_uid);
	$yd_uname = $rtn['uname'];
	$yd_name = (!empty($rtn['name'])) ? $rtn['name'] : "" ;
} else { $yd_uname = ""; $yd_name = ""; } 

$editperm=0;
$owner=0;
// get permission unames for each groupPermission
$_tempGperm = $d3dConf->gPerm->getUidsByName( array_keys($d3dConf->gPerm->gperm_config) );
// check edit permission by group
if(isset($_tempGperm['allow_edit'])){
	if(in_array($uid, $_tempGperm['allow_edit'])) {
		if($req_uid==$uid){$owner=1;$editperm=1;}
		if($d3dConf->mPerm->check_isadmin()){$editperm=1;}
	}	//unset($_tempGperm);
}
	$params['ofst_key'] = "phofst" ;
	$_offset_ = $d3dConf->func->getpost_param($params['ofst_key']);
	$offset = isset($_offset_) ?(int)$_offset_ : 0;
	
	if ($req_uid > 0) {
		$ret = $d3dConf->func->get_xoopsuname($req_uid) ;
		$yd_uname = $ret['uname'];
		$yd_name = $ret['name'];
	}
	
	$params['order'] = htmlspecialchars( $d3dConf->func->getpost_param('odr'), ENT_QUOTES ) ;
	$params['order'] = $params['order'] ? $params['order'] :'time' ; 
	$params['getnav'] = true ;	// not render but get navi

	$params['mode'] = htmlspecialchars( $d3dConf->func->getpost_param('mode'), ENT_QUOTES );
	$cid = (int)$d3dConf->func->getpost_param('cid') ;
	$cname = "";
	if ( strcmp($params['mode'], "category")==0 ) {
		if ( $cid > 0 ) {
			$category->uid = $req_uid ;
			$category->cid = $cid ;
			$category->getchildren($mydirname) ;
			$params['cname'] = htmlspecialchars( $category->cname, ENT_QUOTES ) ;
			$params['cids'] = $category->children ;
		} else {
			// for no-category's diary
			$params['cname'] = constant("_MD_NOCNAME") ;
			$params['cids'] = array(0) ;
		}
	}

	$tag_name = $d3dConf->func->getpost_param('tag_name');
	if (!empty($tag_name)) {
		if (!get_magic_quotes_gpc()) {
			$params['tags'] = array( addslashes($d3dConf->func->getpost_param('tag_name')) ) ;
		} else {
			$params['tags'] = array( $d3dConf->func->getpost_param('tag_name') ) ;
		}
	}

	$params['year'] = (int)$d3dConf->func->getpost_param('year');
	$params['month'] = (int)$d3dConf->func->getpost_param('month');
	$params['day'] = (int)$d3dConf->func->getpost_param('day');

	list( $photolist, $photonavi ) = $d3dConf->func->get_photolist
		( $req_uid, $uid, $d3dConf->mod_config['block_diarynum'], $offset, $params );

	$yd_param['openarea']=intval($d3dConf->dcfg->openarea);
	$yd_openarea = !empty($category->openarea) ? intval($category->openarea) :$yd_param['openarea'] ;

	$params['cid'] = $cid ;

	$num_rows = 0;
	if ( count($photonavi) > 1 ) {
		$num_rows = $photonavi['count'] ;
		array_pop( $photonavi ) ; // erase $photonavi['count'] 
	}
	
	if ( $offset <= 0 ) {
		$offset2 = 0 ;
		$startnum = $offset2 + 1 ;
		$endnum = $d3dConf->mod_config['block_diarynum'] ;
	} else {
		$offset2 = $offset-1 ;
		$startnum = $offset + 1 ;
		$endnum = $offset + $d3dConf->mod_config['block_diarynum'] ;
	}
	if(empty($num_rows)){$startnum = 0;	$endnum = 0;}
	if ($endnum > $num_rows) { $endnum = $num_rows;	}

	// create url for sort
	$url = ''; $url4ex_cat = ''; $url4ex_tag = ''; $url4ex_date = '';
	if( !empty($_SERVER['QUERY_STRING'])) {
		// create url for sort
		$url = preg_replace("/^(.*)\&odr=[0-9a-z_]+/", "$1", $_SERVER['QUERY_STRING']);
		// create url for exclude date
		$url4ex_date = preg_replace("/^(.*)\&mode=month/", "$1", $_SERVER['QUERY_STRING']);
		$url4ex_date = preg_replace("/^(.*)\&mode=date/", "$1", $url4ex_date);
		$url4ex_date = preg_replace("/^(.*)\&year=[0-9]+/", "$1", $url4ex_date);
		$url4ex_date = preg_replace("/^(.*)\&month=[0-9]+/", "$1", $url4ex_date);
		$url4ex_date = preg_replace("/^(.*)\&day=[0-9]+/", "$1", $url4ex_date);
		// create url for exclude category
		$url4ex_cat = preg_replace("/^(.*)\&mode=category/", "$1", $_SERVER['QUERY_STRING']);
		$url4ex_cat = preg_replace("/^(.*)\&cid=[0-9]+/", "$1", $url4ex_cat);
		// create url for exclude tag
		$url4ex_tag = preg_replace("/^(.*)\&tag_name=[%A-Z0-9a-z_]+/", "$1", $_SERVER['QUERY_STRING']);
	}
        $sort_baseurl = XOOPS_URL.'/modules/'.$mydirname.'/index.php?'.$url;
        $url4ex_date = XOOPS_URL.'/modules/'.$mydirname.'/index.php?'.$url4ex_date;
        $url4ex_cat =  XOOPS_URL.'/modules/'.$mydirname.'/index.php?'.$url4ex_cat;
        $url4ex_tag = XOOPS_URL.'/modules/'.$mydirname.'/index.php?'.$url4ex_tag;


// define Template
$xoopsOption['template_main']= $mydirname.'_photolist.html';

require XOOPS_ROOT_PATH.'/header.php';

// assign module header for css
$d3diary_header = '<link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=main_css" />'."\r\n";

$yd_avaterurl = $d3dConf->func->get_user_avatar(array($req_uid));
$xoops_pagetitle = ($d3dConf->mod_config['use_name']==1) ? $yd_name.constant("_MD_DIARY_PERSON") : 
			$yd_uname.constant("_MD_DIARY_PERSON") ;

$yd_param = $params ;
	
// menu
if($d3dConf->mod_config['menu_layout']==1){
	$yd_layout = "left";
}elseif($d3dConf->mod_config['menu_layout']==2){
	$yd_layout = "";
}else{
	$yd_layout = "right";
}

	list( $arr_weeks, $arr_monthes, $arr_dclass, $arr_wclass ) = $d3dConf->func->initBoxArr();

// breadcrumbs
	$bc_para['diary_title'] = $xoopsTpl->get_template_vars('xoops_modulename');
	$bc_para['path'] = "index.php?page=photolist";
	$bc_para['uname'] = $yd_uname;
	$bc_para['name'] = (!empty($yd_name)) ? $yd_name : $yd_uname ;
	$bc_para['mode'] = "photolist";
	//$bc_para['mode'] = $yd_param['mode'];
	$bc_para['bc_name'] = constant('_MD_NEWPHOTO');
	
	/* TODO: currently no good way for operation
	if((strcmp($bc_para['mode'], "category")==0)){
		$bc_para['cid'] = $yd_param['cid'];
		$bc_para['cname'] = $yd_param['cname'] ? $yd_param['cname'] : constant('_MD_NOCNAME');
	} elseif(strcmp($bc_para['mode'], "friends")==0){
		$bc_para['bc_name'] = constant('_MD_DIARY_FRIENDSVIEW');;
	}

	if($yd_param['month'] > 0){
		$bc_para['year'] = $yd_param['year'];
		$bc_para['month'] = $yd_param['month'];
	} elseif($yd_param['day'] > 0){
		$bc_para['year'] = $yd_param['year'];
		$bc_para['month'] = $yd_param['month'];
		$bc_para['day'] = $yd_param['day'];
	} */
	
	// added requested tag_name
	$b_tag=rawurldecode($d3dConf->func->getpost_param('tag_name'));
	if(!empty($b_tag) && $d3dConf->mod_config['use_tag']>0) {
		$yd_param['tag'] = htmlSpecialChars(urldecode($d3dConf->func->getpost_param('tag_name')), ENT_QUOTES);
		//$bc_para['tag'] = $yd_param['tag'];
	}

	$breadcrumbs = $d3dConf->func->get_breadcrumbs( $uid, $bc_para['mode'], $bc_para );
	//var_dump($breadcrumbs);

if ($req_uid>0){
    if($d3dConf->mod_config['menu_layout']<=1){
	list( $yd_calender, $yd_cal_month ) =  $d3dConf->func->get_calender ($req_uid,date("Y"),date("m"), $uid);
	list( $yd_monlist, $yd_monthnavi ) =  $d3dConf->func->get_monlist ($req_uid,$uid);
	list( $yd_friends, $yd_friendsnavi ) =  $d3dConf->func->get_friends ($d3dConf->mPerm->req_friends);
	$yd_list = $d3dConf->func->get_blist ($req_uid,$uid,10);
	list( $yd_comment, $yd_com_key ) =  $d3dConf->func->get_commentlist ($req_uid,$uid,10,false);
	$yd_counter = $d3dConf->func->get_count_diary($req_uid);
    } else {
	$yd_calender=""; $yd_cal_month=""; $yd_friends=""; $yd_friendsnavi="";
	$yd_comment=""; $yd_monlist=""; $yd_monthnav=""; $yd_counter="";
    }

	$xoopsTpl->assign(array(
		"yd_avaterurl" => $yd_avaterurl[$req_uid],
		"yd_calender" => $yd_calender,
		"yd_cal_month" => $yd_cal_month,
		"yd_monlist" => $yd_monlist,
		"yd_monthnavi" => $yd_monthnavi,
		"yd_friends" => $yd_friends,
		"yd_friendsnavi" => $yd_friendsnavi,
		"yd_list" => $yd_list,
		"yd_comment"  => $yd_comment,
		"yd_counter" => $yd_counter,
		));
}

	$xoopsTpl->assign(array(
		"yd_uid" => $req_uid,
		"yd_uname" => $yd_uname,
		"yd_name" => $yd_name,
		"yd_editperm" => $editperm,
		"yd_layout" => $yd_layout,
		"yd_openarea" => $yd_openarea,
		"yd_param" => $yd_param,
		"yd_com_key"  => $yd_com_key,			
		"catopt"  => $d3dConf->func->get_categories($req_uid,$uid),
		"yd_cid" => $cid,
		"yd_cname" => $yd_param['cname'],
		"tag_name" => htmlspecialchars( $tag_name, ENT_QUOTES ),
		"yd_photolist" => $photolist,
		"yd_pagenavi" => $photonavi,
		"lang_datanum" => constant('_MD_DATANUM1').$num_rows. constant('_MD_DATANUM2').
					$startnum. constant('_MD_DATANUM3').$endnum.
					constant('_MD_DATANUM4'),
		"sort_baseurl" => $sort_baseurl,
	        "url4ex_cat" => $url4ex_cat,
	        "url4ex_tag" => $url4ex_tag,
	        "url4ex_date" => $url4ex_date,
		"mydirname" => $mydirname,
		"xoops_pagetitle" => $xoops_pagetitle,
		"xoops_breadcrumbs" => $breadcrumbs,
		"xoops_module_header" => 
			$xoopsTpl->get_template_vars( 'xoops_module_header' ).$d3diary_header,
		"mod_config" =>  $d3dConf->mod_config
		));

include_once XOOPS_ROOT_PATH.'/footer.php';

?>