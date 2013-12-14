<?php

//--------------------------------------------------------------------
// Config
//--------------------------------------------------------------------
include_once dirname( dirname(__FILE__) ).'/class/category.class.php';
include_once dirname( dirname(__FILE__) ).'/class/photo.class.php';
include_once dirname( dirname(__FILE__) ).'/class/tag.class.php';
include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';

$category =& D3diaryCategory::getInstance();
$photo =& D3diaryPhoto::getInstance();
$tag =& D3diaryTag::getInstance();
$d3dConf =& D3diaryConf::getInstance($mydirname, 0, "diarylist");
$func =& $d3dConf->func ;
$myts =& $d3dConf->myts;
$mPerm =& $d3dConf->mPerm ;
$gPerm =& $d3dConf->gPerm ;
$mod_config =& $d3dConf->mod_config ;

//--------------------------------------------------------------------
// GET Initial Valuses
//--------------------------------------------------------------------

$myname = "diarylist.php";

// query values
$uid = $d3dConf->uid;
$req_cid = $d3dConf->q_cid;
$b_tag_noquote = $d3dConf->q_tag_noquote;
$b_tag = $d3dConf->q_tag;
$yd_param = array() ;
$yd_param['mode'] = $d3dConf->q_mode;
$yd_param['year'] = $d3dConf->q_year;
$yd_param['month'] = $d3dConf->q_month;
$yd_param['day'] = $d3dConf->q_day;
$yd_param['order'] = $d3dConf->q_odr ;

$xoopsOption['template_main']= $mydirname.'_diarylist.html';
include XOOPS_ROOT_PATH."/header.php";
// this page uses smarty template
// this must be set before including main header.php

// assign module header for css
$d3diary_header = '<link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.
	'/modules/'.$mydirname.'/index.php?page=main_css" />'."\r\n";
$d3diary_header .= '<link rel="alternate" type="application/rss+xml" title="RDF" href="'.XOOPS_URL.
	'/modules/'.$mydirname.'/rdf.php" />'."\r\n";
//$xoopsTpl->assign( 'xoops_module_header' ,$xoopsTpl->get_template_vars( 'xoops_module_header' ).$d3diary_header );

$func->update_other();

// menu
if($mod_config['menu_layout']==1){
	$yd_layout = "left";
}elseif($mod_config['menu_layout']==2){
	$yd_layout = "";
}else{
	$yd_layout = "right";
}

	// get friends' array at first
	//$d3dConf->set_mod_config(0,"diarylist");	// needs $dcfg
	$openarea = array();
	$noavatar_exists = file_exists(XOOPS_ROOT_PATH."/modules/user/images/no_avatar.gif");
	
	// *********** SQL for
	// get personal config openarea and friends' uids
	$sql = "SELECT DISTINCT d.uid, cfg.openarea 
			FROM ".$xoopsDB->prefix($mydirname.'_diary')
			." d LEFT JOIN ".$xoopsDB->prefix($mydirname.'_config')
			." cfg ON d.uid=cfg.uid ";
	
	$result = $xoopsDB->query($sql);
	
	while($dbdat = $xoopsDB->fetchArray($result)){
		$u = (int)$dbdat['uid'];
		$openarea[$u] = (int) $dbdat['openarea'];
	}

	$editperm=0;
	
	if($mPerm->isadmin){
		$editperm=1;
		$whr_openarea = " 1 ";
	} else {
		$_params4op['use_gp'] = $gPerm->use_gp;
		$_params4op['use_pp'] = $gPerm->use_pp;
		$whr_openarea = $mPerm->get_open_query( "dlist1", $_params4op );
	}

	// added requested tag_name
	if (!empty($b_tag) && $mod_config['use_tag']>1) {
		$sql_tag= "LEFT JOIN ".$xoopsDB->prefix($mydirname.'_tag')." t ON d.bid=t.bid ";
	        if (!get_magic_quotes_gpc()) {
			$whr_tag= " AND t.tag_name='".addslashes($b_tag_noquote)."'";
		} else {
			$whr_tag= " AND t.tag_name='".$b_tag_noquote."'";
		}
		$url_tag= "&amp;tag_name=".$b_tag;
	} else {
		$sql_tag= ""; $whr_tag= " "; $url_tag= "";
	}

	// added common category query
	if( 10000 < $req_cid ){
		$category->uid = 0;
		$category->cid = $req_cid;
		$category->getchildren($mydirname);
		$url_tag.="&amp;cid=".$req_cid;
	}
		$yd_param['cname'] = !empty($category->cname) ? $myts->makeTboxData4Show($category->cname) : constant('_MD_NOCNAME') ;
		$yd_param['cid'] = $req_cid;
	
	$whr_time = ""; $whr_cat = ""; $whr_uids = "";

	if(!empty($yd_param['year'])) {
		$yd_param['prev_year'] = $yd_param['year'] -1;
		$yd_param['next_year'] = $yd_param['year'] +1;
	}

	if(strcmp($yd_param['mode'], "category")==0){
		if($category->children){
			$whr_cat.=" AND d.cid IN (".implode(",",$category->children).") ";
		} else {
			$whr_cat.=" AND d.cid='".$yd_param['cid']."' ";
		}
	}elseif(strcmp($yd_param['mode'], "friends")==0){
    		if (!empty($mPerm->req_friends)) {
			$whr_uids=" AND d.uid IN (".implode(',',$mPerm->req_friends).")";
		}
	}
	if(!empty($yd_param['day'])){
		$whr_time.=" AND d.create_time>='".$yd_param['year']."-".$yd_param['month']
			."-".$yd_param['day']." 00:00:00"."' ";
		$whr_time.=" AND d.create_time<='".$yd_param['year']."-".$yd_param['month']
			."-".$yd_param['day']." 23:59:59"."' ";
	}elseif(!empty($yd_param['month'])){
		if($yd_param['month']==12){
			$next_year=$yd_param['year']+1;
			$next_month=1;
		}else{
			$next_year=$yd_param['year'];
			$next_month=$yd_param['month']+1;
		}
		$whr_time.=" AND d. create_time>='".$yd_param['year']."-".$yd_param['month']."-01 00:00:00"."' ";
		$whr_time.=" AND d. create_time<'".$next_year."-".$next_month."-01 00:00:00"."' ";
	}


	$now = date("Y-m-d H:i:s");
	if ($mPerm->isadmin!=true and $mPerm->isauthor!=true) {
		$whr_nofuture = " AND d.create_time<'".$now."' ";
	} else { $whr_nofuture = ""; }

	$yd_param['order'] = $yd_param['order'] ? $yd_param['order'] :'time' ; 

		$dosort = false ;
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

	// arrays for BoxDate
	list( $arr_weeks, $arr_monthes, $arr_dclass, $arr_wclass ) = $func->initBoxArr();

	// init variables
    	$num_rows = 0; $max_entry = 0; $startnum = 0; $endnum = 0; $offset = 0; $yd_pagenavi = array();
    	$first_date = $last_date = $now;
	$entry=array();	$got_bids=array(); 

	// *********** SQL temporary base for get bids
	$sql_tmp_base = "FROM ".$xoopsDB->prefix($mydirname.'_diary')." d 
			INNER JOIN ".$xoopsDB->prefix('users')." u USING(uid) 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_category')." c ON ((c.uid=d.uid or c.uid='0') and d.cid=c.cid) 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_config')." cfg ON d.uid=cfg.uid ".$sql_tag." 
			WHERE ".$whr_openarea.$whr_cat.$whr_time.$whr_uids.$whr_tag.$whr_nofuture." AND 
			(cfg.blogtype='0' OR cfg.blogtype IS NULL) ORDER BY ".$odr ;
	
	// query limit
	$max_entry = intval($mod_config['block_diarynum']);
	$offset = $func->getpost_param('pofst');
	$offset = (isset($offset) && ($offset>0)) ? intval($offset) : 0;
	$whr_offset = " LIMIT ".$offset.",".$max_entry ;

	// *********** SQL for
	// get entries on selected offset
	$sql = "SELECT SQL_CALC_FOUND_ROWS d.bid ".$sql_tmp_base.$whr_offset ;	// for MySQL Only
	//$sql = "SELECT d.bid ".$sql_tmp_base.$whr_offset ;			// for All SQL

	$result = $xoopsDB->query($sql);
	$got_arr_bids = array();
	while ( $dbdat = $xoopsDB->fetchArray($result) ) {
		$got_arr_bids[] = (int)$dbdat['bid'];
	}
	$where_ser_bids = "WHERE d.bid IN (".implode( ',', $got_arr_bids ).")" ;

	//* MySQL Only
	// get count of bids
	$sql = "SELECT FOUND_ROWS();";
	$result = $xoopsDB->query($sql);
	$dbdat = $xoopsDB->fetchArray($result);
	$num_rows = $dbdat['FOUND_ROWS()'];
	//*/	

 	/*// for All SQL
	$sql = "SELECT count(d.bid) as rows ".$sql_tmp_base ;
	$result = $xoopsDB->query($sql);
	$dbdat = $xoopsDB->fetchArray($result);
	$num_rows = $dbdat['rows'];
	*/

    if ( 0 < $num_rows ) {

	// page control
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

	// *********** SQL for actual entry
	// get entries on selected offset

	$sql = "SELECT d.diary, d.create_time, d.cid, d.title, d.bid, d.openarea AS openarea, d.dohtml, 
			d.view, d.vgids AS vgids, d.vpids AS vpids, u.uid, u.uname, u.name, u.user_avatar, 
			c.cid, c.cname, c.openarea AS openarea_cat, c.vgids AS vgids_cat, c.vpids AS vpids_cat 
			FROM ".$xoopsDB->prefix($mydirname.'_diary')." d 
			INNER JOIN ".$xoopsDB->prefix('users')." u USING(uid) 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_category')." c ON ((c.uid=d.uid or c.uid='0') and d.cid=c.cid) 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_config')." cfg ON d.uid=cfg.uid ".$sql_tag
			. $where_ser_bids. " ORDER BY ".$odr  ;
	$result = $xoopsDB->query($sql);

	// flag for using d3comment
	if(!empty($mod_config['comment_dirname']) 
		&& intval($mod_config['comment_forum_id'])>0){
		$yd_param['use_d3comment']=true;
	}else{
		$yd_param['use_d3comment']=false;
	}

	$entry = array(); $got_bids = array(); $mytstamp=array(); $is1st=1;
	while($dbdat = $xoopsDB->fetchArray($result)){
		if($offset>0 and $is1st==1){	// for first record
	    		$last_date = $dbdat['create_time'];
			$is1st=0;
		}
	    	$_entry = array();
		$i = intval($dbdat['bid']);
		$_entry['bid']=$dbdat['bid'];

		$ctime=preg_split('/[-: ]/',$dbdat['create_time']);
		$_entry['tstamp'] = $tstamp = mktime($ctime[3],$ctime[4],$ctime[5],$ctime[1],$ctime[2],$ctime[0]);
		$week = intval($func->myformatTimestamp($tstamp, "w"));

		$_entry['create_time']=$dbdat['create_time'];
		$_entry['year']   = intval($func->myformatTimestamp($tstamp, "Y"));
		$_entry['month']   = intval($func->myformatTimestamp($tstamp, "m"));
		$_entry['day']   = intval($func->myformatTimestamp($tstamp, "d"));
		$_entry['time']   = $func->myformatTimestamp($tstamp, "H:i");
		$_entry['week'] = $arr_weeks [$week];
		$_entry['b_month'] = $arr_monthes [$_entry['month'] -1];
		$_entry['dclass'] = $arr_dclass [$week];
		$_entry['wclass'] = $arr_wclass [$week];

		$_entry['title'] =empty( $dbdat['title'] ) ? constant('_MD_DIARY_NOTITLE') 
				: $myts->makeTboxData4Show($dbdat['title']);
		$_entry['url']=XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=detail&bid='.$dbdat['bid'];
		$_entry['uid']=$dbdat['uid'];
		$_user_avatar = $func->htmlspecialchars($dbdat['user_avatar']);
			if($_user_avatar=="blank.gif" && $noavatar_exists) {
				$_entry['avatarurl'] = XOOPS_URL . "/modules/user/images/no_avatar.gif";
			} else {
				$_entry['avatarurl'] = XOOPS_UPLOAD_URL . "/" . $_user_avatar;
       			}
		
		$_entry['uname']=$func->htmlSpecialChars($dbdat['uname']);
		$_entry['name']= !empty($dbdat['name']) ? 
				$func->htmlSpecialChars($dbdat['name']) : $_entry['uname'];
		$_entry['view'] = $dbdat['view'];
		$_entry['cid'] = isset($dbdat['cid']) ? intval($dbdat['cid']) : 0 ;
		$_entry['cname'] = isset($dbdat['cname']) ? $func->htmlSpecialChars($dbdat['cname']) : constant('_MD_NOCNAME') ;

		// openarea overrides
		$_tmp_op = isset($openarea[$dbdat['uid']]) ? intval($openarea[$dbdat['uid']]) : 0 ;
		$openarea_cat = intval($dbdat['openarea_cat']);

		list( $_got_op , $_slctd_op , $_tmp_gperms, $_tmp_pperms ) 
			= $mPerm->override_openarea( $_tmp_op, intval($dbdat['openarea']), $openarea_cat, 
				$dbdat['vgids'], $dbdat['vpids'], $dbdat['vgids_cat'], $dbdat['vpids_cat'] );
		$_entry['openarea'] = $_got_op;
			// var_dump($_tmp_gperms); var_dump($_tmp_pperms);

		$_entry['can_disp'] = true;
		// timestamp for sort
		$mytstamp[$i] = $tstamp;

		if(!isset($last_date)){ $last_date = $dbdat['create_time']; }
		$first_date =  $dbdat['create_time'];

		$_entry['dohtml'] = intval($dbdat['dohtml']);
		$_entry['diary'] = $func->substrTarea($dbdat['diary'], $_entry['dohtml'], 
			intval($mod_config['preview_charmax']));
		$_entry['other']=0;
		$entry[$i] = $_entry;	unset($_entry);

		$got_bids[] = $i;
	}
    }

	if( $num_rows==0 || ($startnum + $max_entry > $num_rows) ) { 
		$whr_date = " and d.create_time<'".$last_date."' "; 
	} else {
		$whr_date = "and d.create_time>'".$first_date."' and d.create_time<'".$last_date."' ";
	}
	
	// *********** SQL for
	// other enrties

	$where_other = "";

	if ( $dosort == true && (empty($b_tag) || $mod_config['use_tag']==0) ) {
		$sql_tmp = "SELECT  d.uid, d.cid 
			FROM ".$xoopsDB->prefix($mydirname.'_newentry')." d 
			INNER JOIN ".$xoopsDB->prefix('users')." u USING(uid) 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_category')." c 
			ON ((c.uid=d.uid or c.uid='0') and d.cid=c.cid) 
			WHERE d.blogtype>'0' ".$whr_cat.$whr_time.$whr_uids.$whr_date." 
			ORDER BY ".$odr;

		$result = $xoopsDB->query($sql_tmp);
		while ( $dbdat = $xoopsDB->fetchArray($result) ) {
			$where_other .= "(d.uid=".(int)$dbdat['uid']." AND";
			$where_other .= " d.cid=".(int)$dbdat['cid'].") OR ";
		}
	}
		
	if ( 0 < strlen($where_other) ){
			$where_other = "WHERE ". $where_other;
			$where_other = rtrim( $where_other, 'OR ' );

		$sql = "SELECT  d.diary, d.create_time, d.title, d.url, u.uname, u.name, u.uid, u.user_avatar, 
			c.cid, c.cname, c.openarea AS openarea_cat 
			FROM ".$xoopsDB->prefix($mydirname.'_newentry')." d 
			INNER JOIN ".$xoopsDB->prefix('users')." u USING(uid) 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_category')." c 
			ON ((c.uid=d.uid or c.uid='0') and d.cid=c.cid) "
			.$where_other."	ORDER BY ".$odr;

		$result = $xoopsDB->query($sql);

	    $i=-1000;
	    while($dbdat = $xoopsDB->fetchArray($result)){
		$tmp = preg_split("/[-: ]/",$dbdat['create_time']);
		
	    	$_entry = array();
		$_entry['tstamp'] = $tstamp = mktime($tmp[3],$tmp[4],$tmp[5],$tmp[1],$tmp[2],$tmp[0]);
		$week = intval($func->myformatTimestamp($tstamp, "w"));

		$_entry['create_time']=$dbdat['create_time'];
		$_entry['year']   = intval($func->myformatTimestamp($tstamp, "Y"));
		$_entry['month']   = intval($func->myformatTimestamp($tstamp, "m"));
		$_entry['day']   = intval($func->myformatTimestamp($tstamp, "d"));
		$_entry['time']   = $func->myformatTimestamp($tstamp, "H:i");
		$_entry['week'] = $arr_weeks [$week];
		$_entry['b_month'] = $arr_monthes [$_entry['month'] -1];
		$_entry['dclass'] = $arr_dclass [$week];
		$_entry['wclass'] = $arr_wclass [$week];
		
		$_entry['title']=empty( $dbdat['title'] ) ? constant($constpref.'_NOTITLE') 
			: $myts->makeTboxData4Show($dbdat['title']);
		$_entry['url']=$dbdat['url'];
		$_entry['uid']=intval($dbdat['uid']);
		$_user_avatar = $func->htmlspecialchars($dbdat['user_avatar']);
			if($_user_avatar=="blank.gif" && $noavatar_exists) {
				$_entry['avatarurl'] = XOOPS_URL . "/modules/user/images/no_avatar.gif";
			} else {
				$_entry['avatarurl'] = XOOPS_UPLOAD_URL . "/" . $_user_avatar;
       			}
		$_entry['uname']= $func->htmlSpecialChars($dbdat['uname']);
		$_entry['name']= !empty($dbdat['name']) ? 
				$func->htmlSpecialChars($dbdat['name']) : $_entry['uname'];
		$_entry['cid'] = isset($dbdat['cid']) ? intval($dbdat['cid']) : 0 ;
		$_entry['cname'] = isset($dbdat['cname']) ? $dbdat['cname'] : constant('_MD_NOCNAME') ;

//		$_entry['diary'] = $func->substrTarea($dbdat['diary'], 0, 
//					intval($mod_config['preview_charmax']));
		$_entry['diary'] = mb_substr(strip_tags($dbdat['diary']),0,(int)$mod_config['preview_charmax'], _CHARSET)."...";
					
		// openarea overrides
		$_tmp_op = isset($openarea[$dbdat['uid']]) ? intval($openarea[$dbdat['uid']]) : 0 ;
		$_entry['openarea']=$_tmp_op;
		$openarea_cat = intval($dbdat['openarea_cat']);
		if ($openarea_cat>0) { $_entry['openarea'] = $openarea_cat; }

		$_entry['can_disp'] = true;
		
		$_entry['other']=1;
		$_entry['dohtml']=0;

		$entry[$i] = $_entry;	unset($_entry);
		$mytstamp[$i] = $tstamp;
		$i++;
	    }
	} // end if
	// random phptos
	$photo->bids = $got_bids;
	$photo->readrand_mul($mydirname);
	foreach ( $photo->photos as $i => $_photo ) {
		$entry[$i]['photo'] = $_photo['pid'].$_photo['ptype'];
		// hide photos which is inserted diray body text
		if ( strstr( $entry[$i]['diary'], $_photo['pid'] )) {
			$entry[$i]['photo_body_in'] = true ;
		}
	}
	unset($photo->photos);
	
	if ( $mod_config['use_tag']>0 ) {
		// tags
		$tag->bids = $got_bids;
		$tag->readdb_mul($mydirname);
		foreach ( $tag->tags as $i => $_tag ) {
			$entry[$i]['tag'] = $_tag;
			$entry[$i]['tag_num'] = count($_tag);
		}
		unset($tag->tags);
	}

	$d3dConf->set_new_bids ( $got_bids );
	// comment counts, newest comments
	//list($yd_comment,$yd_com_key) = $func->get_commentlist(0,$uid,$got_bids,100,true);
	list($yd_comment,$yd_com_key) = $func->get_commentlist(0,$uid,100,true);
	foreach( $yd_comment as $_com){
		$i = $_com['bid'];
		$entry[$i]['com_num'] = $_com['com_num'];
		$entry[$i]['unique_path'] = $_com['unique_path'];
		$entry[$i]['com_uname'] = $_com['uname'];
		$entry[$i]['com_name'] = $_com['name'];
		$entry[$i]['com_guest_name'] = $_com['guest_name'];
		$entry[$i]['com_title'] = $_com['title'];
		$entry[$i]['com_datetime'] = $_com['datetime'];
		$entry[$i]['newcom'] = $_com['newcom'];
	}

	// sort by timestamp
	if ( $dosort == true && !empty($mytstamp) && 0<count($entry)) {
		array_multisort($mytstamp, SORT_DESC, $entry );
	}

	//TagCloud
	$where = "";
	list( $tagCloud, $dummy_navi ) = $func->getTagCloud($where, 80, 200);

// breadcrumbs
	$bc_para['diary_title'] = $xoopsTpl->get_template_vars('xoops_modulename');
	$bc_para['path'] = "index.php?page=diarylist";
	$bc_para['uname'] = ""; $bc_para['name'] = "";
	$bc_para['mode'] = $yd_param['mode'];
	if($req_cid>=10000){
		$bc_para['mode'] = "category";
		$bc_para['cid'] = $req_cid;
		$bc_para['cname'] = $yd_param['cname'] ? $yd_param['cname'] : constant('_MD_NOCNAME');
	}
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
	}
	
	
	if(!empty($b_tag) && $mod_config['use_tag']>0) {
		$yd_param['tag'] = $b_tag;
		$bc_para['tag'] = $yd_param['tag'];
	}
	$breadcrumbs = $func->get_breadcrumbs( 0, $bc_para['mode'], $bc_para );
	//var_dump($breadcrumbs);

	$xoopsTpl->assign(array(
		//	"yd_editperm" => $editperm,
			"yd_layout" => $yd_layout,
			"yd_data" => $entry,
			"yd_param" => $yd_param,
			"yd_offset" => $offset,
			"yd_com_key"  => $yd_com_key,			
			"yd_pagenavi" => $yd_pagenavi,
			"catopt" => d3diary_assign_common_category ($mydirname),
			"common_cid" => $req_cid,
			"yd_tag" => $b_tag,
			"tagCloud" => $tagCloud,
			"lang_datanum" => constant('_MD_DATANUM1').$num_rows. constant('_MD_DATANUM2').
						$startnum. constant('_MD_DATANUM3').$endnum.
						constant('_MD_DATANUM4'),
			"base_qstr" => $d3dConf->url4_all,
			"sort_baseurl" => $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_odr,
		        "url4ex_cat" => $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_cat,
		        "url4ex_tag" => $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_tag,
		        "url4ex_date" => $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4ex_date,
			"style_s" => $d3dConf->style_s,
			"mydirname" => $mydirname,
		//	"xoops_pagetitle" => $xoops_pagetitle,
			"xoops_breadcrumbs" => $breadcrumbs,
			"xoops_module_header" => 
				$xoopsTpl->get_template_vars( 'xoops_module_header' ).$d3diary_header,
			"mod_config" =>  $mod_config
			));

	$d3dConf->debug_appendtime('diarylist');
	if($mPerm->isadmin==true && $d3dConf->debug_mode==1){$xoopsTpl->assign("debug_time", $d3dConf->debug_gettime());}

function d3diary_assign_common_category ($mydirname) {
	global $xoopsDB, $mPerm, $func ;
	// naao changed for common category (uid=0)
	$sql = "SELECT * FROM ".$xoopsDB->prefix($mydirname.'_category')."
	          WHERE uid='0' ORDER BY corder";

	$result = $xoopsDB->query($sql);
	$catopt = array();
	$catopts = array();
	while ( $dbdat = $xoopsDB->fetchArray($result) ) {
		$op = (int)$dbdat['openarea'];
		if($dbdat['blogtype'] != 100){
			$catopt['cid']   = (int)$dbdat['cid'];
			$catopt['cname']   = $func->htmlspecialchars($dbdat['cname']);
			$catopt['corder']   = (int)$dbdat['corder'];
			$catopt['subcat']   = (int)$dbdat['subcat'];
			$catopt['openarea']   = $op;
			$catopt['blogtype']   = (int)$dbdat['blogtype'];
			$catopt['dohtml']   = (int)$dbdat['dohtml'];
		}
		if($op ==10 || $op==20) {
			$_tmp_gperms = isset($dbdat['vgids']) ? 
					array_map("intval", explode('|', trim($dbdat['vgids'],'|'))) : array();
			if (array_intersect($mPerm->mygids, $_tmp_gperms)) {
				$catopts[] = $catopt;
			}
		} else {
			$catopts[] = $catopt;
		}
	}
	return $catopts;
}

?>
