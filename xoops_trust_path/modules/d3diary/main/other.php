<?php

//--------------------------------------------------------------------
// Config
//--------------------------------------------------------------------

include_once dirname( dirname(__FILE__) ).'/class/category.class.php';
include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';
require_once(dirname( dirname(__FILE__) ).'/include/magpierss/rss_fetch.inc');

//--------------------------------------------------------------------
// GET Initial Valuses
//--------------------------------------------------------------------

$myname = "other.php";
$yd_list=array(); $yd_com_key=""; $yd_monthnavi="";

$req_uid = isset($_GET['req_uid']) ? (int)$_GET['req_uid'] : 0;

$d3dConf =& D3diaryConf::getInstance($mydirname, $req_uid, "edit");
$func =& $d3dConf->func ;
$myts =& $d3dConf->myts;
$mPerm =& $d3dConf->mPerm ;
$gPerm =& $d3dConf->gPerm ;
$mod_config =& $d3dConf->mod_config ;

if ( $mPerm->isadmin && 0 < $req_uid ) {
	$query_req_uid = "&amp;req_uid=".$req_uid;
}

$func->update_other();

$uid = $d3dConf->uid;

if($d3dConf->dcfg->uid==$uid){$owner=1;}else{$owner=0;}

// define Template
$xoopsOption['template_main']= $mydirname.'_other.html';

include XOOPS_ROOT_PATH."/header.php";
// this page uses smarty template
// this must be set before including main header.php

// assign module header for css
$d3diary_header = '<link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=main_css" />'."\r\n";
$xoopsTpl->assign( 'xoops_module_header' ,$xoopsTpl->get_template_vars( 'xoops_module_header' ).$d3diary_header );

// access check
if(!$mPerm->check_permission( $d3dConf->dcfg->uid, $d3dConf->dcfg->openarea )){
    redirect_header(XOOPS_URL.'/',4,_MD_NOPERM_VIEW);
	exit();
}

$_tempGperm = $gPerm->getUidsByName( array('allow_mailpost') );
// check mailpost permission for access user's group
if( $mod_config['use_mailpost']==1 && !empty($_tempGperm['allow_mailpost'])){
	$allow_mailpost = 0;
	if(isset($_tempGperm['allow_mailpost'][$req_uid])) {
		$allow_mailpost = 1;
	}
}	unset($_tempGperm);

// menu
if($mod_config['menu_layout']==1){
	$yd_layout = "left";
}elseif($mod_config['menu_layout']==2){
	$yd_layout = "";
}else{
	$yd_layout = "right";
}

$yd_avaterurl = $func->get_user_avatar(array($d3dConf->dcfg->uid));

	//$yd_uname=d3diary_get_xoopsuname($d3dConf->dcfg->uid);
	$rtn = $func->get_xoopsuname($d3dConf->dcfg->uid);
	$yd_uname = $rtn['uname'];
	$yd_name = (!empty($rtn['name'])) ? $rtn['name'] : "" ;
	
	list( $arr_weeks, $arr_monthes, $arr_dclass, $arr_wclass ) = $func->initBoxArr();

// specified category
$req_cid=intval($func->getpost_param('cid'));

if($req_cid >0 ){
	$category =& D3diaryCategory::getInstance();
	$category->uid=$d3dConf->dcfg->uid;
	$category->cid=$req_cid;
	$category->readdb($mydirname);
	$yd_data['cname'] = $category->cname;

	$xoopsTpl->assign("yd_cid", $req_cid);
	$xoopsTpl->assign("yd_cname", $category->cname);
} else {
	$xoopsTpl->assign("yd_cid", "");
	$xoopsTpl->assign("yd_cname", "");

	$rss = d3d_mgp_fetch_rss($d3dConf->dcfg->rss);

	$yd_rss['title']=$rss->channel['title'];
	if(mb_internal_encoding()!="UTF-8"){
		$yd_rss['title']=mb_convert_encoding($yd_rss['title'], mb_internal_encoding(), "UTF-8");
	}
	$yd_rss['rss']=$d3dConf->dcfg->rss;
	$yd_rss['blogurl']=$d3dConf->dcfg->blogurl;
	$xoopsTpl->assign("yd_rss", $yd_rss);

	$i=0;
  foreach ($rss->items as $item ) {
	$yd_data['title']=$item['title'];
	if(mb_internal_encoding()!="UTF-8"){
		$yd_data['title']=mb_convert_encoding($yd_data['title'], mb_internal_encoding(), "UTF-8");
	}
	$yd_data['link'] = $item['link'];
	
	# 普通はelse文の部分だけでいいはずなんだが・・・
	if(!empty($item['dc']['date'])){
		$tstamp=yr_strtotime($item['dc']['date']);
	}elseif(!empty($item['pubdate'])){
		$tstamp=yr_strtotime($item['pubdate']);
	}elseif(!empty($item['published'])){
		$tstamp=yr_strtotime($item['published']);
	}elseif(!empty($item['issued'])){
		$tstamp=yr_strtotime($item['issued']);
	}elseif(!empty($item['modified'])){
		$tstamp=yr_strtotime($item['modified']);
	}else{
		$tstamp=$item['date_timestamp'];
	}

	if(!empty($item['summary'])){
		$yd_data['diary'] = $item['summary'];
	}elseif(!empty($item['description'])){
		$yd_data['diary'] = $item['description'];
	}elseif(!empty($item['content'])){
		$yd_data['diary'] = $item['content'];
	}else{
		$yd_data['diary']="";
	}

	if(mb_internal_encoding()!="UTF-8"){
		$yd_data['diary']=mb_convert_encoding($yd_data['diary'], mb_internal_encoding(), "UTF-8");
	}

//	print_r($item);print $tstamp;exit;
	$yd_data['tstamp'] = $tstamp;
	$yd_data['year'] = intval(date("Y", $tstamp));
	$yd_data['month'] = intval(date("m", $tstamp));
	$yd_data['day'] = intval(date("d", $tstamp));
	$yd_data['time'] = date("H:i:s", $tstamp);
		$week = intval($func->myformatTimestamp($tstamp, "w"));
	$yd_data['week'] = $arr_weeks [$week];
	$yd_data['b_month'] = $arr_monthes [$yd_data['month'] -1];
	$yd_data['dclass'] = $arr_dclass [$week];
	$yd_data['wclass'] = $arr_wclass [$week];
	$yd_data['cname'] = "";
	$yd_other[] = $yd_data;
    	$mytstamp[]  = $tstamp;
	$i++;
	if($i>=7){
		break;
	}
  }
}

// get categories of this user
if( $req_cid >0 ){
	$whr_cat = " AND cid=".intval($req_cid);
} else {
	$whr_cat = "";
}

	$sql = "SELECT *
			  FROM ".$xoopsDB->prefix($mydirname.'_category')."
	          WHERE uid='".intval($d3dConf->dcfg->uid)."'".$whr_cat." AND blogtype>0 ORDER BY corder";

	$result = $xoopsDB->query($sql);

	$other_cids = array();
	$i=0;
	while ( $dbdat = $xoopsDB->fetchArray($result) ) {
		// ここに各カテゴリの外部設定を渡して取り込む//
		$rss= d3d_mgp_fetch_rss($dbdat['rss']);

		$j=0;
		$yd_data = array();
		foreach ($rss->items as $item ) {
	    		$yd_data['title'] = $item['title'];
		  	if(mb_internal_encoding()!="UTF-8"){
				$yd_data['title']=mb_convert_encoding($yd_data['title'], mb_internal_encoding(), "UTF-8");
			}
    			$yd_data['link'] = $item['link'];
	
			# 普通はelse文の部分だけでいいはずなんだが・・・
			if(!empty($item['dc']['date'])){
				$tstamp=yr_strtotime($item['dc']['date']);
			}elseif(!empty($item['pubdate'])){
				$tstamp=yr_strtotime($item['pubdate']);
			}elseif(!empty($item['published'])){
				$tstamp=yr_strtotime($item['published']);
			}elseif(!empty($item['issued'])){
				$tstamp=yr_strtotime($item['issued']);
			}elseif(!empty($item['modified'])){
				$tstamp=yr_strtotime($item['modified']);
			}else{
				$tstamp=$item['date_timestamp'];
			}

			if(!empty($item['summary'])){
				$yd_data['diary'] = $item['summary'];
			}elseif(!empty($item['description'])){
				$yd_data['diary'] = $item['description'];
			}elseif(!empty($item['content'])){
				$yd_data['diary'] = $item['content'];
			}else{
				$yd_data['diary']="";
			}

			if(mb_internal_encoding()!="UTF-8"){
				$yd_data['diary']=mb_convert_encoding($yd_data['diary'], mb_internal_encoding(), "UTF-8");
			}

//			print_r($item);print $tstamp;exit;
			$yd_data['tstamp'] = $tstamp;
			$yd_data['year'] = intval(date("Y", $tstamp));
			$yd_data['month'] = intval(date("m", $tstamp));
			$yd_data['day'] = intval(date("d", $tstamp));
			$yd_data['time'] = date("H:i:s", $tstamp);
				$week = intval($func->myformatTimestamp($tstamp, "w"));
			$yd_data['week'] = $arr_weeks [$week];
			$yd_data['b_month'] = $arr_monthes [$yd_data['month'] -1];
			$yd_data['dclass'] = $arr_dclass [$week];
			$yd_data['wclass'] = $arr_wclass [$week];
			
			$yd_data['cname'] = $dbdat['cname'];
			$yd_other[] = $yd_data;
    			$mytstamp[]  = $tstamp;
		//	var_dump($yd_data); echo"<br />";
			$j++;
			if($j>=7){
				break;
			}
    		}
    		$i++;
    		
    		unset($rss);
		
	}
	//var_dump($other_cids);

	// sort all entries by timestamp
	if (!empty($yd_other)){
		array_multisort($mytstamp, SORT_DESC, $yd_other);
		$xoopsTpl->assign("yd_data", $yd_other);
	}

	$yd_param['year']=intval(date("Y"));
	$yd_param['month']=intval(date("n"));
	if(!empty($yd_param['year'])) {
		$yd_param['prev_year'] = $yd_param['year'] -1;
		$yd_param['next_year'] = $yd_param['year'] +1;
	}

// breadcrumbs
	$bc_para['diary_title'] = $xoopsTpl->get_template_vars('xoops_modulename');
	$bc_para['path'] = "index.php";
	$bc_para['uname'] = $yd_uname;
	$bc_para['name'] = (!empty($yd_name)) ? $yd_name : $yd_uname ;
	$bc_para['mode'] = "";
	$bc_para['title'] = $yd_data['title'];
	
	if($req_cid>=0){
		$bc_para['mode'] = "category";
		$bc_para['cid'] = $req_cid;
		$bc_para['cname'] = $yd_data['cname'] ? $yd_data['cname'] : constant('_MD_NOCNAME');
	}

	$breadcrumbs = $func->get_breadcrumbs( $d3dConf->dcfg->uid, $bc_para['mode'], $bc_para );
	//var_dump($breadcrumbs);

if ($d3dConf->dcfg->uid>0){
    if ($mod_config['menu_layout']<=1){
	$yd_list = $func->get_blist ($d3dConf->dcfg->uid,$uid,10);
	list( $yd_comment, $yd_com_key ) =  $func->get_commentlist ($d3dConf->dcfg->uid,$uid,10,false);
	list( $yd_calender, $yd_cal_month ) =  $func->get_calender ($d3dConf->dcfg->uid,date("Y"),date("m"), $uid);
	list( $yd_friends, $yd_friendsnavi ) =  $func->get_friends ($mPerm->req_friends);
	list( $yd_monlist, $yd_monthnavi ) =  $func->get_monlist ($d3dConf->dcfg->uid,$uid);
    } else {
	$yd_calender=""; $yd_cal_month=""; $yd_friends=""; $yd_friendsnavi="";
	$yd_comment=""; $yd_monlist=""; $yd_monthnav="";
    }

	$xoopsTpl->assign(array(
			"yd_avaterurl" => $yd_avaterurl[$d3dConf->dcfg->uid],
			"yd_calender" => $yd_calender,
			"yd_cal_month" => $yd_cal_month,
			"yd_monlist" => $yd_monlist,
			"yd_monthnavi" => $yd_monthnavi,
			"yd_friends" => $yd_friends,
			"yd_friendsnavi" => $yd_friendsnavi,
			"yd_list" => $yd_list,
			"yd_comment"  => $yd_comment,
			"yd_com_key" => $yd_com_key,
			"yd_mailpost"	=> $allow_mailpost
			));
}

	$xoopsTpl->assign(array(
			"req_uid" => $req_uid,
			"query_req_uid" => $query_req_uid,
			"yd_uid" => $d3dConf->dcfg->uid,
			"yd_uname" => $yd_uname,
			"yd_name" => $yd_name,
			"yd_owner" => $owner,
			"yd_param" => $yd_param,
			"yd_openarea" => intval($d3dConf->dcfg->openarea),
			"yd_layout" => $yd_layout,
			"yd_offset" => $offset,
			"catopt"  => $func->get_categories($d3dConf->dcfg->uid,$uid),
			"mydirname" => $mydirname,
			"xoops_breadcrumbs" => $breadcrumbs,
			"mod_config" => $mod_config
			));

$func->countup_diary($d3dConf->dcfg->uid);

function yr_strtotime($tstamp){
		return strtotime($tstamp);
}

?>
