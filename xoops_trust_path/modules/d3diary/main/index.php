<?php

//--------------------------------------------------------------------
// Config
//--------------------------------------------------------------------
include_once dirname( dirname(__FILE__) ).'/class/diary.class.php';
include_once dirname( dirname(__FILE__) ).'/class/category.class.php';
include_once dirname( dirname(__FILE__) ).'/class/photo.class.php';
include_once dirname( dirname(__FILE__) ).'/class/tag.class.php';
include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';

$diary =& D3diaryDiary::getInstance();
$category =& D3diaryCategory::getInstance();
$photo =& D3diaryPhoto::getInstance();
$tag =& D3diaryTag::getInstance();

//--------------------------------------------------------------------
// GET Initial Valuses
//--------------------------------------------------------------------

$myname = "index.php";
$yd_list=array(); $yd_com_key=""; $yd_monthnavi="";

$req_uid = isset($_GET['req_uid']) ? (int)$_GET['req_uid'] : 0;

$d3dConf =& D3diaryConf::getInstance($mydirname, $req_uid, "index");
$dcfg =& $d3dConf->dcfg;
$func =& $d3dConf->func ;
$myts =& $d3dConf->myts;
$mPerm =& $d3dConf->mPerm ;
$gPerm =& $d3dConf->gPerm ;
$mod_config =& $d3dConf->mod_config ;

// query values
$uid = $d3dConf->uid;
$req_cid = $d3dConf->q_cid;
$b_tag_noquote = $d3dConf->q_tag_noquote;
$b_tag = $d3dConf->q_tag;
$yd_param = array() ;
$yd_param['mode'] = $d3dConf->q_mode;
$yd_param['friend'] = strcmp($yd_param['mode'], "friends")==0 ? 1 : $d3dConf->q_fr ;
$yd_param['year'] = $yd_year = $d3dConf->q_year;
$yd_param['month'] = $yd_month = $d3dConf->q_month;
$yd_param['day'] = $yd_day = $d3dConf->q_day;
$yd_param['order'] = $d3dConf->q_odr ;
$yd_param['fr_mode'] = $d3dConf->q_fr ;

if ( $yd_param['friend']==1 && $req_uid==0 ) {
   		header("Location:".  XOOPS_URL.'/modules/'.$mydirname.'/index.php?req_uid='.$uid.'&fr=1');
} elseif ( $uid>0 && $req_uid==0 ) {
	$d3dConf->override_uid2_requid();
	$req_uid = $d3dConf->req_uid;
	$dcfg->uid = $req_uid;
	$dcfg->readdb($mydirname);
	if($dcfg->keep==0){
   		header("Location:".  XOOPS_URL.'/modules/'.$mydirname.'/index.php?req_uid='.$uid);
	}else{
		header("Location:". XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=diarylist');
	}
	exit();
} elseif ( $uid==0 && $req_uid==0 ) {
	header("Location:". XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=diarylist');
	exit();
}

if($dcfg->blogtype!=0){
    header("Location:".  XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=other&req_uid='.$req_uid);
	exit();
}

if(!$mPerm->check_exist_user($req_uid)){
	redirect_header(XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=diarylist',4,_MD_IVUID_ERR);
	exit();
}

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

// access check
if(!$mPerm->check_permission($req_uid, $dcfg->openarea)){
    redirect_header(XOOPS_URL.'/',4,_MD_NOPERM_VIEW);
	exit();
}

	// call auto mailpost function (ver0.16~)
	if( $mod_config['use_mailpost']==1 && !empty($_tempGperm['allow_mailpost'])){
		if(isset($_tempGperm['allow_mailpost'][$req_uid])) {
			// overrides dcfg->uid
			$dcfg->uid = $req_uid;
			$dcfg->readdb($mydirname);
			
			// excluded $editperm=1
			if ( $dcfg->mailpost>=2 && ( time() > ($dcfg->updated + $dcfg->uptime) ) ) {
				$mPost = & $d3dConf->mPost;
				//var_dump($mPost->req_uid);
				$mPost->ini_set();

				d3diary_auto_mailpost();
				// queryF for 2nd argument "true"
				$dcfg->updated = time();
				$dcfg->updatedb($mydirname, true);
			}
		}
	}
	unset($_tempGperm);
	//$d3dConf->debug_appendtime('index_pre_header');

// define Template
$xoopsOption['template_main']= $mydirname.'_index.html';

require XOOPS_ROOT_PATH.'/header.php';
// this page uses smarty template
// this must be set before including main header.php

	//$d3dConf->debug_appendtime('index_post_header');

// assign module header for css
$d3diary_header = '<link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=main_css" />'."\r\n";
$d3diary_header .= '<script type="text/javascript" src="'.XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=loader&src=tag.js"></script>'."\r\n";
$d3diary_header .= '<link rel="alternate" type="application/rss+xml" title="RDF" href="'.XOOPS_URL.'/modules/'.$mydirname.'/rdf.php?uid='.$req_uid.'&cid='.$req_cid.'" />'."\r\n";
//$xoopsTpl->assign( 'xoops_module_header' ,$xoopsTpl->get_template_vars( 'xoops_module_header' ).$d3diary_header );

// menu
if($mod_config['menu_layout']==1){
	$yd_layout = "left";
}elseif($mod_config['menu_layout']==2){
	$yd_layout = "";
}else{
	$yd_layout = "right";
}

//$yd_uname=d3diary_get_xoopsuname($req_uid);
$rtn = $func->get_xoopsuname($req_uid);
$yd_uname = $rtn['uname'];
$yd_name = (!empty($rtn['name'])) ? $rtn['name'] : "" ;

$yd_avaterurl = $func->get_user_avatar(array($req_uid));

$yd_param['openarea']=intval($dcfg->openarea);
$yd_param['query_string'] = !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : "" ;

	$url_tag = "";
	if ( $yd_param['friend']==1){$url_tag.="&amp;fr=1";}
	if (!empty($yd_param['mode'])){$url_tag.="&amp;mode=".$yd_param['mode'];}
	if (!empty($req_uid)){$url_tag.="&amp;req_uid=".$req_uid;}
	if (!empty($b_tag_noquote)) {
		if (!get_magic_quotes_gpc()) {
			$url_tag.="&amp;tag_name=".addslashes($b_tag_noquote) ;
		} else {
			$url_tag.="&amp;tag_name=".$b_tag_noquote ;
		}
	}

if(strcmp($yd_param['mode'], "category")==0){
	$yd_param['cid'] = $req_cid;
	$category->uid=$req_uid;
	$category->cid=$yd_param['cid'];
	$category->getchildren($mydirname);
	if (!empty($yd_param['cid']) && $category->children){
		$url_tag.="&amp;cid=".$yd_param['cid'];
	}
	if($category->blogtype!=0){
		header("Location:".  XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=other&req_uid='.$dcfg->uid.'&cid='.$req_cid);
		exit();
	}
	$yd_param['cname'] = !empty($category->cname) ? $myts->makeTboxData4Show($category->cname) : constant('_MD_NOCNAME') ;

}

if(!empty($yd_param['day'])){
	if (!empty($yd_param['year'])){$url_tag.="&amp;year=".$yd_param['year'];}
	if (!empty($yd_param['month'])){$url_tag.="&amp;month=".$yd_param['month'];}
	$url_tag.="&amp;day=".$yd_param['day'];
}elseif(!empty($yd_param['month'])){
	if (!empty($yd_param['year'])){$url_tag.="&amp;year=".$yd_param['year'];}
	$url_tag.="&amp;month=".$yd_param['month'];
}elseif(strcmp($yd_param['mode'], "all")==0){
	$yd_param['mode']="all";
}

$temp_openarea = !empty($category->openarea) ? intval($category->openarea) : $yd_param['openarea'] ;

$xoops_pagetitle = ($mod_config['use_name']==1) ? $yd_name.constant("_MD_DIARY_PERSON") : 
				$yd_uname.constant("_MD_DIARY_PERSON") ;

// flag for using d3comment
if(!empty($mod_config['comment_dirname']) && intval($mod_config['comment_forum_id'])>0){
	$yd_param['use_d3comment']=true;
}else{
	$yd_param['use_d3comment']=false;
}

	//TagCloud
	$where = "";
	if($req_uid){
		$where= 'uid='. intval($req_uid);
	}
	list( $tagCloud, $dummy_navi ) = $func->getTagCloud($where, 80, 200);

list( $arr_weeks, $arr_monthes, $arr_dclass, $arr_wclass ) = $func->initBoxArr();

// ** start mainblist
// **		**
	$noavatar_exists = file_exists(XOOPS_ROOT_PATH."/modules/user/images/no_avatar.gif");
	$whr_openarea = " (d.openarea <>100 OR d.uid = $uid)";

	$mid = $d3dConf->mid;
	
	if ($editperm==1) {
		$whr_openarea = " 1 ";
	} else {
		// openarea permissions 
		$_params4op['use_gp'] = $gPerm->use_gp;
		$_params4op['use_pp'] = $gPerm->use_pp;
		$whr_openarea = $mPerm->get_open_query( "index1", $_params4op );
		//var_dump($whr_openarea);
	}

	// added tag_name request
	if (!empty($b_tag_noquote) && $mod_config['use_tag']>0) {
		$sql_tag= " LEFT JOIN ".$xoopsDB->prefix($mydirname.'_tag')." t ON d.bid=t.bid ";
	        if (!get_magic_quotes_gpc()) {
			$whr_tag= " AND t.tag_name='".addslashes($b_tag_noquote)."'";
		} else {
			$whr_tag= " AND t.tag_name='".$b_tag_noquote."'";
		}
		//$yd_param['req_tag'] = htmlSpecialChars( $b_tag, ENT_QUOTES );
		$yd_param['req_tag'] = $b_tag ;
		$yd_param['tag_mode'] = 1;

	} else {
		$sql_tag= ""; $whr_tag= " ";

		$yd_param['tag_mode'] = $func->getpost_param('tag_mode') ;
		$yd_param['tag_mode'] = !empty($yd_param['tag_mode']) ? 1 : 0 ;
	}
	$charmax = intval($mod_config['preview_charmax']);
	$diarynum = intval($mod_config['block_diarynum']);

	$openarea = $yd_param['openarea'];

	$whr_time = " ";
	$whr_uids="d.uid='".intval($req_uid)."'";
 	if( $yd_param['friend'] ==1 ){
    		if (!empty($mPerm->req_friends)) {
			$whr_uids="d.uid IN (".implode(',',$mPerm->req_friends).")";
		}
	}
	if(strcmp($yd_param['mode'], "category")==0){
		if($category->children){
			$whr_time.=" and d.cid IN (".implode(",",$category->children).") ";
		} else {
			$whr_time.=" and d.cid='".$yd_param['cid']."' ";
		}
 	}
	if(!empty($yd_param['day'])){
		$whr_time.=" and d.create_time>='".$yd_param['year']."-".$yd_param['month']
			."-".$yd_param['day']." 00:00:00"."' ";
		$whr_time.=" and d.create_time<='".$yd_param['year']."-".$yd_param['month']
			."-".$yd_param['day']." 23:59:59"."' ";
	}elseif(!empty($yd_param['month'])){
		if($yd_param['month']==12){
			$next_year=$yd_param['year']+1;
			$next_month=1;
		}else{
			$next_year=$yd_param['year'];
			$next_month=$yd_param['month']+1;
		}
		$whr_time.=" and d. create_time>='".$yd_param['year']."-".$yd_param['month']."-01 00:00:00"."' ";
		$whr_time.=" and d. create_time<'".$next_year."-".$next_month."-01 00:00:00"."' ";
	}

	$now = date("Y-m-d H:i:s");
	if ($mPerm->isadmin!=true and $mPerm->isauthor!=true) {
		$whr_nofuture = " AND d.create_time<'".$now."' ";
	} else {
		$whr_nofuture = "";
	}

	$yd_param['order'] = $yd_param['order'] ? $yd_param['order'] :'time' ; 

		$dosort = false ; $resort_odr = "" ;
		switch ($yd_param['order']) {
			case 'random' :
				$odr = "rand()" ;
				break;
			case 'title_asc' :
				$odr = "cast(d.title as char) ASC" ;
				break;
			case 'title_dsc' :
				$odr = "cast(d.title as char) DESC" ;
				break;
			case 'hit_asc' :
				$odr = "d.view ASC" ;
				break;
			case 'hit_dsc' :
				$odr = "d.view DESC" ;
				break;
			case 'time_asc' :
				$odr = "d.create_time ASC" ;
				break;
			case 'time_dsc' :
			case 'time' :
			default :
				$odr = "d.create_time DESC" ;
				$dosort = true ;
		}

	//$d3dConf->debug_appendtime('index_tag');

	// *********** SQL base
	$sql_base = "FROM ".$xoopsDB->prefix($mydirname.'_diary')." d 
			INNER JOIN ".$xoopsDB->prefix('users')." u ON u.uid=d.uid AND ".$whr_uids." 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_category')." c 
			ON ((c.uid=d.uid or c.uid='0') and d.cid=c.cid) ".$sql_tag." 
			WHERE ".$whr_openarea.$whr_time.$whr_tag.$whr_nofuture." 
			 ORDER BY ".$odr ;

	// *********** SQL for
	// get count of total entries
	$sql = "SELECT count(d.bid) as count ".$sql_base ;

	$result = $xoopsDB->query($sql);
	list ($num_rows) = $xoopsDB->fetchRow($result);

	// page control
	$max_entry = intval($mod_config['block_diarynum']);
	$offset = $func->getpost_param('pofst');
	$offset = (isset($offset) && ($offset>0)) ? intval($offset) : 0;
	if ( $offset <= 0 ) {
		$offset2 = 0 ;
		$max_entry2 = $max_entry ;
		$startnum = $offset2 + 1 ;
		$endnum = $max_entry ;
	} else {
		$offset2 = $offset-1 ;
		$max_entry2 = $max_entry + 1 ;
		$startnum = $offset + 1 ;
		$endnum = $offset + $max_entry ;
	}

	if(empty($num_rows)){$startnum = 0;	$endnum = 0;}
	if ($endnum > $num_rows) { $endnum = $num_rows;	}
	
	// query limit
	$whr_offset = " LIMIT ".$offset2.",".$max_entry2 ;

	// using d3diaryPageNav
	if($num_rows>$max_entry){
            if( !empty($_SERVER['QUERY_STRING'])) {
                if( preg_match("/^pofst=[0-9]+/", $_SERVER['QUERY_STRING']) ) {
                    $url = "";
                } else {
                    $url = preg_replace("/^(.*)\&pofst=[0-9]+/", "$1", $_SERVER['QUERY_STRING']);
                }
            } else {
                $url = "";
            }
	    include_once dirname( dirname(__FILE__) ).'/class/d3diaryPagenavi.class.php';
            $nav = new d3diaryPageNav($num_rows, $max_entry, $offset, "pofst", $url);
            $yd_pagenavi = $nav->getNav();
        } else {
            $yd_pagenavi = "";
        }

	//$d3dConf->debug_appendtime('index_pre_query');
	
	// *********** SQL for
	// get entries on selected offset
	$whr_uids="u".ltrim($whr_uids,"d");
	$sql = "SELECT d.diary, d.create_time, d.cid, d.title, d.bid, d.openarea AS openarea, d.dohtml, 
			d.view, u.uname, u.name, u.uid, u.user_avatar, c.cname, c.openarea AS openarea_cat "
		.$sql_base.$whr_offset ;

	$result = $xoopsDB->query($sql);
	
	// flag for using d3comment
	if(!empty($mod_config['comment_dirname']) 
		&& intval($mod_config['comment_forum_id'])>0){
		$yd_param['use_d3comment']=true;
	}else{
		$yd_param['use_d3comment']=false;
	}

	$entry=array();	$got_bids=array(); $is1st=1;
	while ( $dbdat = $xoopsDB->fetchArray($result) ) {
	    if($offset>0 and $is1st==1){	// for first record, fields are to galvage
	    	$last_date = $dbdat['create_time'];
	    } else {
		$i = intval($dbdat['bid']);
		$entry[$i]['bid']=$dbdat['bid'];

		$ctime=preg_split('/[-: ]/',$dbdat['create_time']);
		$entry[$i]['tstamp'] = $tstamp = mktime($ctime[3],$ctime[4],$ctime[5],$ctime[1],$ctime[2],$ctime[0]);
		$week = intval($func->myformatTimestamp($tstamp, "w"));

		$entry[$i]['create_time']=$dbdat['create_time'];
		$entry[$i]['year']   = intval($func->myformatTimestamp($tstamp, "Y"));
		$entry[$i]['month']   = intval($func->myformatTimestamp($tstamp, "m"));
		$entry[$i]['day']   = intval($func->myformatTimestamp($tstamp, "d"));
		$entry[$i]['time']   = $func->myformatTimestamp($tstamp, "H:i");
		$entry[$i]['week'] = $arr_weeks [$week];
		$entry[$i]['b_month'] = $arr_monthes [$entry[$i]['month'] -1];
		$entry[$i]['dclass'] = $arr_dclass [$week];
		$entry[$i]['wclass'] = $arr_wclass [$week];

		$entry[$i]['title'] =empty( $dbdat['title'] ) ? constant('_MD_DIARY_NOTITLE') 
				: $myts->makeTboxData4Show($dbdat['title']);
		$entry[$i]['url']=XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=detail&bid='.$dbdat['bid'];
		$entry[$i]['uid']=$dbdat['uid'];
		$_user_avatar = htmlSpecialChars($dbdat['user_avatar'], ENT_QUOTES);
			if($_user_avatar=="blank.gif" && $noavatar_exists) {
				$entry[$i]['avatarurl'] = XOOPS_URL . "/modules/user/images/no_avatar.gif";
			} else {
				$entry[$i]['avatarurl'] = XOOPS_UPLOAD_URL . "/" . $_user_avatar;
       			}
		
		$entry[$i]['uname']= htmlSpecialChars($dbdat['uname'], ENT_QUOTES);
		$entry[$i]['name']= !empty($dbdat['name']) ? 
				htmlSpecialChars($dbdat['name'], ENT_QUOTES) : $entry[$i]['uname'];
		$entry[$i]['view'] = $dbdat['view'];
		$entry[$i]['cid'] = isset($dbdat['cid']) ? intval($dbdat['cid']) : 0 ;
		$entry[$i]['cname'] = isset($dbdat['cname']) ? htmlSpecialChars($dbdat['cname'], ENT_QUOTES) : constant('_MD_NOCNAME') ;

		// openarea overrides
		$_tmp_openarea=intval($openarea);
		$entry[$i]['openarea']=$_tmp_openarea;
		$openarea_cat = intval($dbdat['openarea_cat']);
		if ($openarea_cat>0) { $entry[$i]['openarea'] = $openarea_cat; }
		if (intval($dbdat['openarea'])>0) { $entry[$i]['openarea'] = intval($dbdat['openarea']); }

		$entry[$i]['can_disp'] = true;

		// timestamp for sort
		$mytstamp["$i"] = $tstamp;
		$got_bids[] = $i;
		if(!isset($last_date)){ $last_date = $dbdat['create_time']; }
		$first_date =  $dbdat['create_time'];

		$entry[$i]['dohtml'] = intval($dbdat['dohtml']);
		$entry[$i]['diary'] = $func->substrTarea($dbdat['diary'], $entry[$i]['dohtml'], $charmax);
		$entry[$i]['other']=0;
	    } //end (is1st)
	    $is1st=0;
	}

	if($num_rows < $max_entry or $startnum <= 1) { $last_date = $now; }
	if($startnum + $max_entry > $num_rows) { 
		$whr_date = " AND d.create_time<'".$last_date."' "; 
	} else {
		$whr_date = " AND d.create_time>'".$first_date."' and d.create_time<'".$last_date."' ";
	}

	// newentries (other)

	if ($editperm==1) {
		$whr_openarea = "";
	} else {
		// openarea permissions 
		$whr_openarea = $mPerm->get_open_query( "index1_other", $_params4op );
		$whr_openarea = " AND ".$whr_openarea;
		//var_dump($whr_openarea);
	}

	//$d3dConf->debug_appendtime('index_pre_other');

	// *********** SQL for
	// other enrties
	if ( $dosort == true && (empty($b_tag) || $mod_config['use_tag']==0) ) {
		$sql = "SELECT  d.diary, d.create_time, d.title, d.url, u.uname, u.name, u.uid, u.user_avatar, 
			c.cid, c.cname, c.openarea AS openarea_cat 
			FROM ".$xoopsDB->prefix($mydirname.'_newentry')." d 
			INNER JOIN ".$xoopsDB->prefix('users')." u ON u.uid=d.uid AND ".$whr_uids." 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_category')." c 
			ON ((c.uid=d.uid or c.uid='0') and d.cid=c.cid) 
			WHERE d.blogtype>'0' ".$whr_openarea.$whr_date.$whr_time." 
			ORDER BY ".$odr;

		//var_dump($sql);
		$result = $xoopsDB->query($sql);

	    $i=-1000;
	    while($dbdat = $xoopsDB->fetchArray($result)){
		$tmp = preg_split("/[-: ]/",$dbdat['create_time']);
		
		$entry[$i]['tstamp'] = $tstamp = mktime($tmp[3],$tmp[4],$tmp[5],$tmp[1],$tmp[2],$tmp[0]);
		$week = intval($func->myformatTimestamp($tstamp, "w"));

		$entry[$i]['create_time']=$dbdat['create_time'];
		$entry[$i]['year']   = intval($func->myformatTimestamp($tstamp, "Y"));
		$entry[$i]['month']   = intval($func->myformatTimestamp($tstamp, "m"));
		$entry[$i]['day']   = intval($func->myformatTimestamp($tstamp, "d"));
		$entry[$i]['time']   = $func->myformatTimestamp($tstamp, "H:i");
		$entry[$i]['week'] = $arr_weeks [$week];
		$entry[$i]['b_month'] = $arr_monthes [$entry[$i]['month'] -1];
		$entry[$i]['dclass'] = $arr_dclass [$week];
		$entry[$i]['wclass'] = $arr_wclass [$week];

		$entry[$i]['title']=empty( $dbdat['title'] ) ? constant($constpref.'_NOTITLE') 
			: $myts->makeTboxData4Show($dbdat['title']);
		$entry[$i]['url']=$dbdat['url'];
		$entry[$i]['uid']=intval($dbdat['uid']);
		$_user_avatar = htmlSpecialChars($dbdat['user_avatar'], ENT_QUOTES);
			if($_user_avatar=="blank.gif" && $noavatar_exists) {
				$entry[$i]['avatarurl'] = XOOPS_URL . "/modules/user/images/no_avatar.gif";
			} else {
				$entry[$i]['avatarurl'] = XOOPS_UPLOAD_URL . "/" . $_user_avatar;
       			}
		$entry[$i]['uname']= htmlSpecialChars( $dbdat['uname'], ENT_QUOTES );
		$entry[$i]['name']= !empty($dbdat['name'] ) ? htmlSpecialChars( $dbdat['name'], ENT_QUOTES ) : "";
		$entry[$i]['cid'] = isset($dbdat['cid']) ? intval($dbdat['cid']) : 0 ;
		$entry[$i]['cname'] = isset($dbdat['cname']) ? $dbdat['cname'] : constant('_MD_NOCNAME') ;

		$entry[$i]['diary'] = mb_substr(strip_tags($dbdat['diary']),0,(int)$mod_config['preview_charmax'], _CHARSET)."...";

		// openarea overrides
		$_tmp_openarea = isset($openarea[$dbdat['uid']]) ? intval($openarea[$dbdat['uid']]) : 0 ;
		$entry[$i]['openarea']=$_tmp_openarea;
		$openarea_cat = intval($dbdat['openarea_cat']);
		if ($openarea_cat>0) { $entry[$i]['openarea'] = $openarea_cat; }

		$entry[$i]['can_disp'] = true;
		$entry[$i]['other']=1;
		$entry[$i]['dohtml']=0;
		$entry[$i]['blogtype']=100;		
		$mytstamp[$i] = $tstamp;
		$i++;
	    }
	} //end if
	//$d3dConf->debug_appendtime('index_pre_photo');

	// photos
	$photo->bids = $got_bids;
	$photo->readdb_mul($mydirname);
	foreach ( $photo->photos as $i => $_photo ) {
		// hide photos which is inserted diray body text
		$temp_entry = $entry[$i]['diary'];
		$j=0;
		$_photo_r = array();
		foreach ( $_photo as $each_photo ) {
			$_photo_r[$j] = $each_photo ;
			if ( strstr( $temp_entry, $each_photo['pid'] )) {
				$_photo_r[$j]['body_in'] = true ;
			}
			$j++;
		}
		$entry[$i]['photo'] = $_photo_r;
		$entry[$i]['photo_num'] = count($_photo_r);
	}
	unset($photo->photos);

	$pop_tags=array(); $person_tags=array(); $_entry_tags=array();
	if ( $mod_config['use_tag']>0 ) {
		// tags
		$tag->bids = $got_bids;
		$tag->readdb_mul($mydirname);
		foreach ( $tag->tags as $i => $_tag ) {
			$entry[$i]['tag'] = $_tag;
			$entry[$i]['tag_num'] = count($_tag);
		}
		unset($tag->tags);

		// tags list
		$pop_tags=array(); $person_tags=array(); $_entry_tags=array();
		$func->get_taglist($req_uid, 0, $pop_tags, $person_tags, $_entry_tags);
	}

	//$d3dConf->debug_appendtime('index_pre_comment');

	// comment counts, newest comments
	if(!empty($got_bids)){
		$d3dConf->set_new_bids ( $got_bids );
		list($yd_comment,$yd_com_key) = $func->get_commentlist( 0, $uid, 100, true );
		foreach( $yd_comment as $_com){
			$i = $_com['bid'];
			$entry[$i]['com_num'] = $_com['com_num'];
			$entry[$i]['unique_path'] = $_com['unique_path'];
			$entry[$i]['com_uname'] = htmlSpecialChars($_com['uname'], ENT_QUOTES);
			$entry[$i]['com_name'] = htmlSpecialChars($_com['name'], ENT_QUOTES);
			$entry[$i]['com_guest_name'] = htmlSpecialChars($_com['guest_name'], ENT_QUOTES);
			$entry[$i]['com_title'] = $myts->makeTboxData4Show($_com['title']);
			$entry[$i]['com_datetime'] = $_com['datetime'];
			$entry[$i]['newcom'] = $_com['newcom'];
		}
	}

	// sort by timestamp
	if ( $dosort == true && !empty($mytstamp) && !empty($entry)) {
		array_multisort($mytstamp, SORT_DESC, $entry );
	}

$yd_param['year'] = !empty($yd_year) ? $yd_year : (int)date("Y") ;
$yd_param['month'] = !empty($yd_month) ? $yd_month : (int)date("n") ;
$yd_param['day'] = !empty($yd_day) ? $yd_day : 0 ;

// ** end mainblist
// **		**

// breadcrumbs
	$bc_para['diary_title'] = $xoopsTpl->get_template_vars('xoops_modulename');
	$bc_para['path'] = "index.php";
	$bc_para['uname'] = $yd_uname;
	$bc_para['name'] = (!empty($yd_name)) ? $yd_name : $yd_uname ;
	$bc_para['mode'] = $yd_param['mode'];
	if((strcmp($bc_para['mode'], "category")==0)){
		$bc_para['cid'] = $yd_param['cid'];
		$bc_para['cname'] = $yd_param['cname'] ? $yd_param['cname'] : constant('_MD_NOCNAME');
	//} elseif(strcmp($bc_para['mode'], "friends")==0){
 	}elseif( $yd_param['friend'] > 0 ){
		$bc_para['bc_name'] = constant('_MD_DIARY_FRIENDSVIEW');;
	}

	if($yd_param['month'] > 0){
		$bc_para['year'] = $yd_param['year'];
		$bc_para['month'] = $yd_param['month'];
	} elseif($yd_param['day'] > 0){
		$bc_para['year'] = $yd_param['year'];
		$bc_para['month'] = $yd_param['month'];
		$bc_para['day'] = $yd_param['day'];
	}
	
	if(!empty($b_tag) && $mod_config['use_tag']>0) {
		$yd_param['tag'] = $b_tag;
		$bc_para['tag'] = $yd_param['tag'];
	}
	
	if(!empty($yd_param['year'])) {
		$yd_param['prev_year'] = $yd_param['year'] -1;
		$yd_param['next_year'] = $yd_param['year'] +1;
	}

	//var_dump($bc_para); echo"<br />";
	$breadcrumbs = $func->get_breadcrumbs( $req_uid, $bc_para['mode'], $bc_para );
	//var_dump($breadcrumbs);

	//$d3dConf->debug_appendtime('index_post_getbc');

if($mod_config['menu_layout']<=1){
	list( $yd_calender, $yd_cal_month ) = 
		$func->get_calender ($req_uid,$yd_param['year'],$yd_param['month'],$uid, 
			$d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_date."&amp;");
	//$d3dConf->debug_appendtime('index_calender');
	list( $yd_friends, $yd_friendsnavi ) =  $func->get_friends ($mPerm->req_friends);
	//$d3dConf->debug_appendtime('index_friends');
	$yd_list = $func->get_blist ($req_uid,$uid,10);
	//$d3dConf->debug_appendtime('index_blist');
	list( $yd_comment, $yd_com_key ) =  $func->get_commentlist ($req_uid,$uid,10,false);
	//$d3dConf->debug_appendtime('index_comment');
	list( $yd_monlist, $yd_monthnavi ) =  $func->get_monlist ($req_uid,$uid);
	//$d3dConf->debug_appendtime('index_month');
	$yd_counter = $func->get_count_diary($req_uid);
} else {
	$yd_calender=""; $yd_cal_month=""; $yd_friends=""; $yd_friendsnavi="";
	$yd_comment=""; $yd_monlist=""; $yd_monthnav=""; $yd_counter="";
}

	$xoopsTpl->assign(array(
			"yd_uid" => $req_uid,
			"yd_uname" => $yd_uname,
			"yd_name" => $yd_name,
			"yd_avaterurl" => $yd_avaterurl[$req_uid],
			"yd_cid" => $req_cid,
			"yd_editperm" => $editperm,
			"yd_owner" => $owner,
			"yd_openarea" => $temp_openarea,
			"yd_layout" => $yd_layout,
			"yd_data" => $entry,
			"yd_param" => $yd_param,
			"yd_year" => $yd_year,
			"yd_month" => $yd_month,
			"yd_day" => $yd_day,
			"popTagArr" => $pop_tags,
			"myTagArr" => $person_tags,
			"yd_counter" =>  $yd_counter,
			"yd_calender" => $yd_calender,
			"yd_cal_month" => $yd_cal_month,
			"yd_monlist" => $yd_monlist,
			"yd_monthnavi" => $yd_monthnavi,
			"yd_friends" => $yd_friends,
			"yd_friendsnavi" => $yd_friendsnavi,
			"yd_offset" => $offset,
			"yd_list" => $yd_list,
			"yd_comment"  => $yd_comment,
			"yd_com_key" => $yd_com_key,
			"yd_pagenavi" => $yd_pagenavi,
			"catopt" => $func->get_categories($req_uid,$uid),
			"tagCloud" => $tagCloud,
			"lang_datanum" => constant('_MD_DATANUM1').$num_rows. constant('_MD_DATANUM2').
						$startnum. constant('_MD_DATANUM3').$endnum.
						constant('_MD_DATANUM4'),
			"base_qstr" => $d3dConf->url4_all,
			"sort_baseurl" => $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_odr,
		        "url4ex_cat" => $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_cat,
		        "url4ex_tag" => $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_tag,
		        "url4ex_date" => $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_date,
		        "url4ex_fr" => $d3dConf->urluppr.$d3dConf->urlbase_exfr.$d3dConf->url4ex_fr,
			"style_s" => $d3dConf->style_s,
			"mydirname" => $mydirname,
			"xoops_pagetitle" => $xoops_pagetitle,
			"xoops_breadcrumbs" => $breadcrumbs,
			"xoops_module_header" => 
				$xoopsTpl->get_template_vars( 'xoops_module_header' ).$d3diary_header,
			"mod_config" =>  $mod_config
			));
	
	if ( !empty($mPost) ) {
		$xoopsTpl->assign(array(
			"scc_msg" => $mPost->_scc_msg,
			"err_msg" => $mPost->_err_msg
			));
	}
		
$func->countup_diary($req_uid);

$func->update_other_cat($req_uid);
	$d3dConf->debug_appendtime('index');
	if($mPerm->isadmin==true && $d3dConf->debug_mode==1){$xoopsTpl->assign("debug_time", $d3dConf->debug_gettime());}

// ***
// *** mailpost function (ver0.16~) ***
// ***
function d3diary_auto_mailpost () {
	global $d3dConf, $diary, $photo, $tag, $mPost;

	$reg_time = '';

	// check setting
	if ( $mPost->check_settings() != true ) {
		$mPost->_err_msg .= _MD_NOSET_MAILPOST." <br />\n";
	}

	$connected = $mPost->connect();
	if ( !$connected ) {
			$mPost->_err_msg .= _MD_NOSET_MAILPOST.$mPost->pop3->ERROR." <br />\n";
	} else {
		$ret = $mPost->get_list();

		if ( $ret != true ) {
			$mPost->_err_msg .= _MD_NO_NEWMAIL." <br />\n";
		} elseif ( count($mPost->mails) > 0 ) {
		
			foreach ( $mPost->mails as $mail ) {
				// auto check all mails
				$i = $mail['id'];
				$params['chk_mails'][] = $i;
				$params['chk_time'][$i] = $mail['create_time'];
			}
				$params['f_query'] = true;	// force sql insert for GET method
			$ret = $mPost->regist_list( $diary, $photo, strip_tags($tag), $params );
			if ( $ret == true ) {
				$params['keep'] = 0;
				$ret = $mPost->del_list( $params );
			}
		}
		// close the connection
		$ret = $mPost->quit();
	}
}

?>
