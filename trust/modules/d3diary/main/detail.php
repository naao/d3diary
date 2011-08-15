<?php

//--------------------------------------------------------------------
// Config
//--------------------------------------------------------------------
include_once dirname( dirname(__FILE__) ).'/class/diary.class.php';
include_once dirname( dirname(__FILE__) ).'/class/category.class.php';
include_once dirname( dirname(__FILE__) ).'/class/photo.class.php';
include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';

$diary =& D3diaryDiary::getInstance();
$category =& D3diaryCategory::getInstance();
$photo =& D3diaryPhoto::getInstance();

//--------------------------------------------------------------------
// GET Initial Valuses
//--------------------------------------------------------------------

$myname = "detail.php";
$yd_list=array(); $yd_com_key=""; $yd_monthnavi="";

$diary->bid = isset($_GET['bid']) ? (int)$_GET['bid'] : 0;
$diary->readdb($mydirname);
if(empty($diary->uid)){
    redirect_header(XOOPS_URL.'/index.php',2,_MD_IVID_ERR);
	exit();
} else {
	$_GET['req_uid'] = (int)$diary->uid ;	// for notification checklist (valid for xcl only)
}

$d3dConf = & D3diaryConf::getInstance ( $mydirname, (int)$diary->uid, "detail" ) ;
$func =& $d3dConf->func ;
$myts =& $d3dConf->myts;
$mPerm =& $d3dConf->mPerm ;
$gPerm =& $d3dConf->gPerm ;
$mod_config =& $d3dConf->mod_config ;

$uid = $d3dConf->uid;
$yd_year = $d3dConf->q_year;
$yd_month = $d3dConf->q_month;
$yd_day = $d3dConf->q_day;
$b_tag_noquote = $d3dConf->q_tag_noquote;
$yd_param['tag'] = $b_tag = $d3dConf->q_tag;

if($d3dConf->dcfg->blogtype!=0){
    header("Location:".  XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=other&req_uid='.$d3dConf->dcfg->uid);
	exit();
}

$mPerm->get_allowed_openarea();

$editperm=0;
$owner=0;
$_tempGperm = $gPerm->getUidsByName( array('allow_edit') );
// check edit permission by group
if(isset($_tempGperm['allow_edit'][$uid])) {
	if($diary->uid==$uid){$owner=1;$editperm=1;}
	if($mPerm->isadmin){$editperm=1;}
}	unset($_tempGperm);

// assign module header for css
$d3diary_header = '<link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=main_css" />'."\r\n";
$d3diary_header .= '<link rel="alternate" type="application/rss+xml" title="RDF" href="'.XOOPS_URL.'/modules/'.$mydirname.'/rdf.php?uid='.$diary->uid.'" />'."\r\n";

$rtn = $func->get_xoopsuname($diary->uid);
$yd_uname = $rtn['uname'];
$yd_name = (!empty($rtn['name'])) ? $rtn['name'] : "" ;

$yd_data['bid'] = $diary->bid;
$yd_data['uid'] = $diary->uid;
$yd_data['cid'] = $diary->cid;
$yd_data['view'] = $diary->view;
//$yd_data['title'] = empty( $diary->title ) ? constant('_MD_DIARY_NOTITLE') : $myts->makeTboxData4Show($diary->title);
$yd_data['title'] = empty( $diary->title ) ? constant('_MD_DIARY_NOTITLE') : $diary->title ;
$yd_data['dohtml'] = $diary->dohtml;
$yd_data['diary'] = $func->stripPb_Tarea($diary->diary, $yd_data['dohtml']);

$xoops_pagetitle = ($mod_config['use_name']==1) ? 
		$yd_data['title'].' - '.$yd_name.constant("_MD_DIARY_PERSON") : 
		$yd_data['title'].' - '.$yd_uname.constant("_MD_DIARY_PERSON") ;

$yd_data['create_time']   = $diary->create_time;
	$ctime = preg_split("/[-: ]/", $diary->create_time);
$yd_data['tstamp'] = $tmp_time = mktime($ctime[3],$ctime[4],$ctime[5],$ctime[1],$ctime[2],$ctime[0]);
	$week = intval($func->myformatTimestamp($tmp_time, "w"));
$yd_data['year']   = $func->myformatTimestamp($tmp_time, "Y");
$yd_data['month']   = intval($func->myformatTimestamp($tmp_time, "m"));
$yd_data['day']   = intval($func->myformatTimestamp($tmp_time, "d"));
$yd_data['time']   = $func->myformatTimestamp($tmp_time, "H:i");

	$yd_param['year'] = $yd_data['year'];
	if(!empty($yd_param['year'])) {
		$yd_param['prev_year'] = $yd_param['year'] -1;
		$yd_param['next_year'] = $yd_param['year'] +1;
	}
	$yd_param['month'] = $yd_data['month'];


	list( $arr_weeks, $arr_monthes, $arr_dclass, $arr_wclass ) = $func->initBoxArr();
	
$yd_data['week'] = $arr_weeks [$week];
$yd_data['b_month'] = $arr_monthes [$yd_data['month'] -1];
$yd_data['dclass'] = $arr_dclass [$week];
$yd_data['wclass'] = $arr_wclass [$week];

$yd_param['mode'] = $func->getpost_param('mode');
// category mode for selected before
if(strcmp($yd_param['mode'], "category")==0){
	$yd_param['cid'] = (int)$func->getpost_param('cid');
	$category->uid = $yd_data['uid'];
	$category->cid = $yd_param['cid'];
	$category->getchildren($mydirname);
	$yd_param['cname'] = $category->cname ? $myts->makeTboxData4Show($category->cname) : constant('_MD_NOCNAME');
	$yd_param['children'] = $category->children;
	if($category->blogtype!=0){
		header("Location:".  XOOPS_URL.'/modules/'.$mydirname.'/index.php?
		page=other&req_uid='.$d3dConf->dcfg->uid.'&cid='.$req_cid);	exit();
	}
}

if($yd_data['cid']>0){
// category for diary itself
	$category->uid = $yd_data['uid'];
	$category->cid = $yd_data['cid'];
	$category->getchildren($mydirname);
	$yd_data['cname'] = $category->cname ? $myts->makeTboxData4Show($category->cname) : constant('_MD_NOCNAME');
	$yd_data['children'] = $category->children;
	if($category->blogtype!=0){
		header("Location:".  XOOPS_URL.'/modules/'.$mydirname.'/index.php?
		page=other&req_uid='.$d3dConf->dcfg->uid.'&cid='.$req_cid);	exit();
	}
} else {
	$yd_data['cname'] = constant('_MD_NOCNAME');
	$yd_data['children'] = array();
}

	$_tmp_isfriend  = $mPerm->check_is_friend($diary->uid);
	$_tmp_isfriend2 = $mPerm->check_is_friend2($diary->uid);
	
	$_tmp_op = intval($d3dConf->dcfg->openarea);
		list( $_got_op , $_slctd_op , $_tmp_gperms, $_tmp_pperms ) 
			= $mPerm->override_openarea( $_tmp_op, intval($diary->openarea), intval($category->openarea), 
				$diary->vgids, $diary->vpids, $category->vgids, $category->vpids );

		$yd_data['openarea'] = $_got_op;

			// var_dump($_tmp_gperms); var_dump($_tmp_pperms);
		$yd_data['can_disp'] = $mPerm->can_display($diary->uid, $_got_op, 
				$diary->create_time, $_tmp_isfriend, $_tmp_isfriend2, $_tmp_gperms, $_tmp_pperms);
		$yd_data['can_disp_com'] = $yd_data['can_disp'] ;

		$photo->uid  = $yd_data['uid'] ;
		$photo->bids = array( $yd_data['bid'] ) ;
		$photo->readdb_mul($mydirname) ;
		$yd_data['photo_num'] = !empty($photo->photos[$yd_data['bid']]) ? count($photo->photos[$yd_data['bid']]) : 0 ;
		$yd_photos   = array() ;
		if ( 0 < $yd_data['photo_num'] ) {
			foreach ( $photo->photos[$yd_data['bid']] as $_photo) {
				if (!empty( $_photo['info'] )) {
					$_photo['info']    = $func->stripPb_Tarea( $_photo['info'] );
				}
				$rtn_photo[] = $_photo;
			}
			$yd_photos   = $rtn_photo ;
		}


// modified 10-06-20
if($yd_data['can_disp'] !== true)
{
	//var_dump($diary->uid); var_dump($_got_op); var_dump($diary->create_time); var_dump($_tmp_isfriend); var_dump($_tmp_isfriend2); var_dump($_tmp_gperms); var_dump($_tmp_pperms); echo"<br />";
	if( $mPerm->exerpt_ok_bymod == true ) {
		$yd_data['diary'] = $func->substrTarea($diary->diary, $yd_data['dohtml'], $mod_config['preview_charmax'] , false, "" );
		$yd_data['can_disp_com'] = $mod_config['can_disp_com'] ? $mod_config['can_disp_com'] : false ;
	} else {
		redirect_header(XOOPS_URL.'/',4,_MD_NOPERM_VIEW);
		exit();
	}
}

// for rightarea global_variables
	
	$openarea=$yd_data['openarea'];

// menu
if($mod_config['menu_layout']==1){
	$yd_layout = "left";
}elseif($mod_config['menu_layout']==2){
	$yd_layout = "";
}else{
	$yd_layout = "right";
}

$yd_avaterurl=$func->get_user_avatar(array($diary->uid));

$rtn = d3diary_get_prev_next($mydirname);
$yd_before = $rtn['yd_before'] ;
$yd_after = $rtn['yd_after'] ;

//var_dump($yd_data);

	// tags
	$pop_tags=array(); $perso_tags=array(); $entry_tags=array();
	$func->get_taglist($diary->uid, $yd_data['bid'], $pop_tags, $person_tags, $entry_tags);
	

$yr_comment_title='RE: '.$myts->makeTboxData4Show($diary->title);

$d3dConf->set_month( $yd_data['year'], $yd_data['month'] );

// define Template
$xoopsOption['template_main']= $mydirname.'_detail.html';

include XOOPS_ROOT_PATH."/header.php";
// this page uses smarty template
// this must be set before including main header.php

// breadcrumbs
	// $xoopsTpl must be after include header.php
	$bc_para['diary_title'] = $xoopsTpl->get_template_vars('xoops_modulename');
	$bc_para['path'] = "index.php";
	$bc_para['uname'] = $yd_uname;
	$bc_para['name'] = (!empty($yd_name)) ? $yd_name : $yd_uname ;
	$bc_para['mode'] = $yd_param['mode'];
	$bc_para['bid'] = $yd_data['bid'];
	$bc_para['title'] = htmlspecialchars($yd_data['title'], ENT_QUOTES);
	
	// category select requested category / or / diary's category itself
	if(strcmp($bc_para['mode'], "category")==0){
		$bc_para['cid'] = $yd_param['cid'];
		$bc_para['cname'] = $yd_param['cname'] ;
	} else {
		$bc_para['cid'] = $yd_data['cid'];
		$bc_para['cname'] = $yd_data['cname'] ;
	}
	$bc_para['cname'] = (!empty($bc_para['cname'])) ? $bc_para['cname'] : constant('_MD_NOCNAME');
	if($yd_day>0){
		$bc_para['year'] = $yd_year;
		$bc_para['month'] = $yd_month;
		$bc_para['day'] =$yd_day;
	} elseif($yd_month>0){ 
		$bc_para['year'] = $yd_year;
		$bc_para['month'] = $yd_month;
	}

	//var_dump($bc_para); echo"<br />";
	$breadcrumbs = $func->get_breadcrumbs( $yd_data['uid'], $bc_para['mode'], $bc_para );
	//var_dump($breadcrumbs);

if($mod_config['menu_layout']<=1){
	list( $yd_calender, $yd_cal_month ) = $func->get_calender ($diary->uid,$yd_data['year'],$yd_data['month'],$uid, 
			$d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_date."&amp;");
	list( $yd_friends, $yd_friendsnavi ) =  $func->get_friends ($mPerm->req_friends);
	$yd_list = $func->get_blist ($diary->uid,$uid,10);
	list( $yd_comment, $yd_com_key ) =  $func->get_commentlist ($diary->uid,$uid,10,false);
	list( $yd_monlist, $yd_monthnavi ) =  $func->get_monlist ($diary->uid,$uid);
	$yd_counter = $func->get_count_diary($diary->uid);
} else {
	$yd_calender=""; $yd_cal_month=""; $yd_friends=""; $yd_friendsnavi="";
	$yd_comment=""; $yd_monlist=""; $yd_monthnav=""; $yd_counter="";
}

	$xoopsTpl->assign(array(
			"yd_uid" => $diary->uid,
			"yd_uname" => $yd_uname,
			"yd_name" => $yd_name,
			"yd_avaterurl" => $yd_avaterurl[$diary->uid],
			"yd_editperm" => $editperm,
			"yd_owner" => $owner,
			"yd_openarea" => $yd_data['openarea'],
			"yd_layout" => $yd_layout,
			"yd_data" => $yd_data,
			"yd_param" => $yd_param,
			"yd_year" => $yd_year,
			"yd_month" => $yd_month,
			"yd_day" => $yd_day,
			"yd_photo" => $yd_photos,
			"bTagArr" => $entry_tags,
			"yd_counter" => $yd_counter,
			"yd_calender" => $yd_calender,
			"yd_cal_month" => $yd_cal_month,
			"yd_monlist" => $yd_monlist,
			"yd_monthnavi" => $yd_monthnavi,
			"yd_friends" => $yd_friends,
			"yd_friendsnavi" => $yd_friendsnavi,
			"yd_before"  => $yd_before,
			"yd_after"  => $yd_after,
			"yd_list" => $yd_list,
			"yd_comment"  => $yd_comment,
			"yd_com_key"  => $yd_com_key,			
			"catopt"  => $func->get_categories($diary->uid,$uid),
			"base_qstr" => $d3dConf->url4_all,
			"sort_baseurl" => $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_odr,
		        "url4ex_cat" => $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_cat,
		        "url4ex_tag" => $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_tag,
		        "url4ex_date" => $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_date,
		        "url4ex_fr" => $d3dConf->urluppr.$d3dConf->urlbase_exfr.$d3dConf->url4ex_fr,
			"mydirname" => $mydirname,
			"xoops_pagetitle" => $xoops_pagetitle,
			"xoops_breadcrumbs" => $breadcrumbs,
			"xoops_module_header" => 
				$xoopsTpl->get_template_vars( 'xoops_module_header' ).$d3diary_header,
			"mod_config" =>  $mod_config
			));

$func->countup_diary($diary->uid, $yd_data['bid']);

// get_prev_next
function d3diary_get_prev_next($mydirname){
	global $diary, $uid, $xoopsUser, $xoopsDB;
	global $yd_param, $yd_data, $editperm, $d3dConf, $gPerm, $mPerm, $yd_year, $yd_month ,$yd_day, $b_tag_noquote ;
	
	$yd_prev = array(); $yd_next = array();
	$openarea = $d3dConf->dcfg->openarea;
	
	// openarea permissions 
	$_params4op['use_gp'] = $gPerm->use_gp;
	$_params4op['use_pp'] = $gPerm->use_pp;
	$whr_openarea = $mPerm->get_open_query( "detail1", $_params4op );
		//var_dump($whr_openarea);

	$whr_time = "";
	if(strcmp($yd_param['mode'], "category")==0){
		if($yd_param['children']){
			$whr_time.=" and d.cid IN (".implode(",",$yd_param['children']).") ";
		} else {
			$whr_time.=" and d.cid='".$yd_param['cid']."' ";
		}
	}
	if($yd_day>0){
		$whr_time.=" and d.create_time>='".$yd_year."-".$yd_month
			."-".$yd_data['day']." 00:00:00"."' ";
		$whr_time.=" and d.create_time<='".$yd_year."-".$yd_month
			."-".$yd_data['day']." 23:59:59"."' ";
	}elseif($yd_month>0){
		if($yd_data['month']==12){
			$next_year=$yd_year+1;
			$next_month=1;
		}else{
			$next_year=$yd_year;
			$next_month=$yd_month+1;
		}
		$whr_time.=" and d. create_time>='".$yd_year."-".$yd_month."-01 00:00:00"."' ";
		$whr_time.=" and d. create_time<'".$next_year."-".$next_month."-01 00:00:00"."' ";
	}

	// added tag_name request
	if (!empty($b_tag_noquote) && $mod_config['use_tag']>0) {
		$sql_tag= " LEFT JOIN ".$xoopsDB->prefix($mydirname.'_tag')." t ON d.bid=t.bid ";
	        if (!get_magic_quotes_gpc()) {
			$whr_tag= " AND t.tag_name='".addslashes($b_tag_noquote)."'";
		} else {
			$whr_tag= " AND t.tag_name='".$b_tag_noquote."'";
		}

	} else {
		$sql_tag= ""; $whr_tag= " ";
	}

	$sql1 = "SELECT d.create_time, d.title, d.bid, d.openarea, c.openarea 
		FROM ".$xoopsDB->prefix($mydirname.'_diary')." d 
		LEFT JOIN ".$xoopsDB->prefix($mydirname.'_category')." c 
		ON ((c.uid=d.uid or c.uid='0') and d.cid=c.cid) 
		".$sql_tag." 
		WHERE (d.uid='".$diary->uid."') AND ".$whr_openarea.$whr_time.$whr_tag." AND ";
		
	// prev
	$whr_create_time = "(d.create_time<'".$diary->create_time."' 
		OR (d.create_time='".$diary->create_time."' AND d.bid<'".$yd_data['bid']."'))";
	
	$sql2 = " ORDER BY d.create_time DESC, d.bid DESC LIMIT 0,1";
	$sql = $sql1.$whr_create_time.$sql2;
	//var_dump($sql);
	
	$result = $xoopsDB->query($sql);
		while($dbdat = $xoopsDB->fetchArray($result)){
			$yd_prev['bid']   = $dbdat['bid'];
			$yd_prev['title']   = empty( $dbdat['title'] ) ? constant('_MD_DIARY_NOTITLE') : $dbdat['title'] ;
			$yd_prev['create_time']   = $dbdat['create_time'];
		}
		
	// next
	$nowdate=date("Y-m-d H:i:s");
	if ($editperm==1) {
		$whr_create_time = "(d.create_time>'".$diary->create_time."' 
			OR (d.create_time='".$diary->create_time."' AND d.bid>'".$yd_data['bid']."'))";
	} else {
		$whr_create_time = "((d.create_time>'".$diary->create_time."' 
			OR (d.create_time='".$diary->create_time."' AND d.bid>'".$yd_data['bid']."')) 
			AND d.create_time<'".$nowdate."')";
	}

	$sql2 = " ORDER BY d.create_time ASC, d.bid ASC LIMIT 0,1";
	$sql = $sql1.$whr_create_time.$sql2;

	$result = $xoopsDB->query($sql);
		while($dbdat = $xoopsDB->fetchArray($result)){
			$yd_next['bid']   = $dbdat['bid'];
			$yd_next['title']   = empty( $dbdat['title'] ) ? constant('_MD_DIARY_NOTITLE') : $dbdat['title'] ;
			$yd_next['create_time']   = $dbdat['create_time'];
		}

	return array("yd_before" => $yd_prev, "yd_after" => $yd_next);
}

if($mod_config['use_simplecomment']==1){
	include dirname( dirname(__FILE__) ).'/include/comment_view.php';
}else{
	include XOOPS_ROOT_PATH.'/include/comment_view.php';
}

	$d3dConf->debug_appendtime('detail');

	if($mPerm->isadmin==true && $d3dConf->debug_mode==1){$xoopsTpl->assign("debug_time", $d3dConf->debug_gettime());}
	
include_once XOOPS_ROOT_PATH.'/footer.php';

?>
