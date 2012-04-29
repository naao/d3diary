<?php

//--------------------------------------------------------------------
// Config
//--------------------------------------------------------------------
include_once dirname( dirname(__FILE__) ).'/class/diary.class.php';
include_once dirname( dirname(__FILE__) ).'/class/category.class.php';
include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';
include_once dirname( dirname(__FILE__) ).'/class/photo.class.php';

$diaryObj =& D3diaryDiary::getInstance();
$category =& D3diaryCategory::getInstance();
$photoObj =& D3diaryPhoto::getInstance();

//--------------------------------------------------------------------
// GET Initial Valuses
//--------------------------------------------------------------------

$myname = "photolist.php";
$yd_list=array(); $yd_com_key=""; $yd_monthnavi="";

$d3dConf =& D3diaryConf::getInstance($mydirname, 0, "photolist");
$func =& $d3dConf->func ;
$myts =& $d3dConf->myts;
$mPerm =& $d3dConf->mPerm ;
$gPerm =& $d3dConf->gPerm ;
$mod_config =& $d3dConf->mod_config ;

// query values
$uid = $d3dConf->uid;
$req_uid = $d3dConf->req_uid;
$b_tag_noquote = $d3dConf->q_tag_noquote;
$b_tag = $d3dConf->q_tag;
$yd_param = array() ;
$yd_param['cid'] = $req_cid = $d3dConf->q_cid;
$yd_param['mode'] = $d3dConf->q_mode;
$yd_param['friend'] = strcmp($yd_param['mode'], "friends")==0 ? 1 : $d3dConf->q_fr ;
$yd_param['year'] = $yd_year = $d3dConf->q_year;
$yd_param['month'] = $yd_month = $d3dConf->q_month;
$yd_param['day'] = $yd_day = $d3dConf->q_day;
$yd_param['order'] = $d3dConf->q_odr ;
$yd_param['fr_mode'] = $d3dConf->q_fr ;

if ($req_uid > 0) {
	$rtn = $func->get_xoopsuname($req_uid);
	$yd_uname = $rtn['uname'];
	$yd_name = (!empty($rtn['name'])) ? $rtn['name'] : "" ;
} else { $yd_uname = ""; $yd_name = ""; } 

$editperm=0;
$owner=0;

// get permission unames for each groupPermission
$_tempGperm = $gPerm->getUidsByName( array_keys($gPerm->gperm_config) );
// check edit permission by group
if(isset($_tempGperm['allow_edit'])){
	if(isset($_tempGperm['allow_edit'][$uid])) {
		if($req_uid==$uid){$owner=1;$editperm=1;}
		if($mPerm->isadmin){$editperm=1;}
	}	//unset($_tempGperm);
}

$action = (int)$func->getpost_param('action') ;
// edit parameters
$eparam = array() ;
$q_bids = $func->getpost_param('bids') ;
if ( !empty( $q_bids  ) ) {
	$eparam['bids']  = array_map( 'intval' , $q_bids ) ;
}
$eparam['pvinfo'] = $func->getpost_param('pvinfo') ;

// assign module header for css
$d3diary_header = '<link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=main_css" />'."\r\n";

// $action =  1:edit info | 2:left rotate | 3:right rotate | 4:move | 5:delete
if( $action > 0 ) {
	if( $uid<=0 ){
	    redirect_header(XOOPS_URL.'/user.php',2,_MD_IVUID_ERR);
		exit();
	}

	// photo parameters
	
	if( $editperm != 1 ){
		redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
		exit();
	}
	// photo delete check by submit with photos
	$temp_psels = $func->getpost_param('psel');
	$psels = array(); $psel_names = array();
	if (!empty($temp_psels)) {
		foreach ( $temp_psels as $temp_psel ) {
			list( $psels[], $psel_names[] ) = explode( "::", $temp_psel );
		}
	}
	
	$rtnurl = $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4_all ;
	$result = $func->manage_photos( $photoObj, $diaryObj, $psels, $psel_names, $action, $eparam ) ;
	$_msg="";
	switch ($action) {
		case 1:
			$photolist = $photoObj->photos ;
			$i=0 ;
			foreach ( $photolist as $_photo ) {
				$photolist[$i]['pinfo']   = $myts->makeTboxData4Show( $_photo['info'] );
				$i++ ;
			}
			break;
		case 11:
			$_msg = ( $result == true ) ? _MD_DIARY_UPDATED : _MD_NODIARY_ERR ;
			redirect_header( $rtnurl, 3, $_msg );
			break;
		case 2:
		case 3:
			$_msg = ( $result == true ) ? _MD_FILEROTATED : _MD_FAILED_FILEROTATE;
			redirect_header( $rtnurl, 3, $_msg );
			break;
		case 4:
			$photolist = $photoObj->photos ;
			break;
		case 41:
			$_msg = ( $result == true ) ? _MD_DIARY_UPDATED : _MD_NODIARY_ERR ;
			redirect_header( $rtnurl, 3, $_msg );
			break;
		case 5:
			$_msg = ( $result == true ) ? _MD_FILEDELETED : _MD_FAILED_FILEDELETE ;
			redirect_header( $rtnurl, 3, $_msg );
			break;
		default:
	}
	// assign module header for css
	$d3diary_header .= '<meta http-equiv="Pragma" content="no-cache">'."\r\n";
	$d3diary_header .= '<meta http-equiv="Cache-Control" content="no-cache">'."\r\n";
	$d3diary_header .= '<meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT">'."\r\n";

} else {
	// read photolist for page view
	$yd_param['ofst_key'] = "phofst" ;
	$_offset_ = $func->getpost_param($yd_param['ofst_key']);
	$offset = isset($_offset_) ?(int)$_offset_ : 0;
	
	if ($req_uid > 0) {
		$ret = $func->get_xoopsuname($req_uid) ;
		$yd_uname = $ret['uname'];
		$yd_name = $ret['name'];
	}
	
	$yd_param['order'] = $yd_param['order'] ? $yd_param['order'] :'time' ; 
	$yd_param['getnav'] = true ;	// not render but get navi

	$yd_param['cname'] = "";
	if ( strcmp($yd_param['mode'], "category")==0 ) {
		if ( $req_cid > 0 ) {
			$category->uid = $req_uid ;
			$category->cid = $req_cid ;
			$category->getchildren($mydirname) ;
			$yd_param['cname'] = htmlspecialchars( $category->cname, ENT_QUOTES ) ;
			$yd_param['cids'] = $category->children ;
		} else {
			// for no-category's diary
			$yd_param['cname'] = constant("_MD_NOCNAME") ;
			$yd_param['cids'] = array(0) ;
		}
	}

	if (!empty($b_tag_noquote)) {
		if (!get_magic_quotes_gpc()) {
			$yd_param['tags'] = array( addslashes($b_tag_noquote) ) ;
		} else {
			$yd_param['tags'] = array( $b_tag_noquote ) ;
		}
	}

	$arr_req_uids = $yd_param['friend'] ==1 ? $mPerm->req_friends : 
			$req_uid > 0 ? array($req_uid) : array() ;
	$params['max_info'] = (int)$mod_config['preview_charmax'] ;
	$params['enc'] = _CHARSET ;
	$params['f_truncate']= true ;

	list( $photolist, $photonavi ) = $func->get_photolist
		( $arr_req_uids, $uid, $mod_config['block_diarynum'], $offset, $yd_param );

	$yd_param['openarea']=intval($d3dConf->dcfg->openarea);
	$yd_openarea = !empty($category->openarea) ? intval($category->openarea) :$yd_param['openarea'] ;

	$num_rows = 0;
	if ( count($photonavi) > 1 ) {
		$num_rows = $photonavi['count'] ;
		array_pop( $photonavi ) ; // erase $photonavi['count'] 
	}
	
	if ( $offset <= 0 ) {
		$offset2 = 0 ;
		$startnum = $offset2 + 1 ;
		$endnum = $mod_config['block_diarynum'] ;
	} else {
		$offset2 = $offset-1 ;
		$startnum = $offset + 1 ;
		$endnum = $offset + $mod_config['block_diarynum'] ;
	}
	if(empty($num_rows)){$startnum = 0;	$endnum = 0;}
	if ($endnum > $num_rows) { $endnum = $num_rows;	}
}

// define Template
$xoopsOption['template_main']= $mydirname.'_photolist.html';

require XOOPS_ROOT_PATH.'/header.php';

$yd_avaterurl = $func->get_user_avatar(array($req_uid));
$xoops_pagetitle = ($mod_config['use_name']==1) ? $yd_name.constant("_MD_DIARY_PERSON") : 
			$yd_uname.constant("_MD_DIARY_PERSON") ;

// menu
if($mod_config['menu_layout']==1){
	$yd_layout = "left";
}elseif($mod_config['menu_layout']==2){
	$yd_layout = "";
}else{
	$yd_layout = "right";
}

	list( $arr_weeks, $arr_monthes, $arr_dclass, $arr_wclass ) = $func->initBoxArr();

$yd_param['year'] = !empty($yd_year) ? $yd_year : (int)date("Y") ;
$yd_param['month'] = !empty($yd_month) ? $yd_month : (int)date("n") ;
$yd_param['day'] = !empty($yd_day) ? $yd_day : 0 ;

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
	if(!empty($b_tag) && $mod_config['use_tag']>0) {
		$yd_param['tag'] = $b_tag;
		//$bc_para['tag'] = $b_tag;
	}

	$breadcrumbs = $func->get_breadcrumbs( $uid, $bc_para['mode'], $bc_para );
	//var_dump($breadcrumbs);

if ($req_uid>0){
    if($mod_config['menu_layout']<=1){
	list( $yd_calender, $yd_cal_month ) =  $func->get_calender ($req_uid,$yd_param['year'],$yd_param['month'], $uid);
	list( $yd_monlist, $yd_monthnavi ) =  $func->get_monlist ($req_uid,$uid);
	list( $yd_friends, $yd_friendsnavi ) =  $func->get_friends ($mPerm->req_friends);
	$yd_list = $func->get_blist ($req_uid,$uid,10);
	list( $yd_comment, $yd_com_key ) =  $func->get_commentlist ($req_uid,$uid,10,false);
	$yd_counter = $func->get_count_diary($req_uid);
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
		"yd_action" => $action,
		"yd_uid" => $req_uid,
		"yd_uname" => $yd_uname,
		"yd_name" => $yd_name,
		"yd_editperm" => $editperm,
		"yd_layout" => $yd_layout,
		"yd_openarea" => $yd_openarea,
		"yd_param" => $yd_param,
		"yd_year" => $yd_year,
		"yd_month" => $yd_month,
		"yd_day" => $yd_day,
		"yd_com_key"  => $yd_com_key,			
		"catopt"  => $func->get_categories($req_uid,$uid),
		"yd_cid" => $req_cid,
		"yd_cname" => $yd_param['cname'],
		"tag_name" => $b_tag,
		"yd_photolist" => $photolist,
		"yd_pagenavi" => $photonavi,
		"lang_datanum" => constant('_MD_DATANUM1').$num_rows. constant('_MD_DATANUM2').
					$startnum. constant('_MD_DATANUM3').$endnum.
					constant('_MD_DATANUM4'),
		"base_qstr" => $d3dConf->url4_all,
		"urluppr" => $d3dConf->urluppr,
		"qstr4ex_fr" => $d3dConf->url4ex_fr,
		"sort_baseurl" => $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_odr,
	        "url4ex_cat" => $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_cat,
	        "url4ex_tag" => $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_tag,
	        "url4ex_date" => $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_date,
	        "url4ex_fr" => $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_fr,
	        "url4ex_ph" => $d3dConf->urluppr.$d3dConf->urlbase_exph.$d3dConf->url4ex_ph,
		"style_s" => $d3dConf->style_s,
		"mydirname" => $mydirname,
		"xoops_pagetitle" => $xoops_pagetitle,
		"xoops_breadcrumbs" => $breadcrumbs,
		"xoops_module_header" => 
			$xoopsTpl->get_template_vars( 'xoops_module_header' ).$d3diary_header,
		"mod_config" =>  $mod_config
		));

?>