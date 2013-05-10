<?php
//--------------------------------------------------------------------
// Config
//--------------------------------------------------------------------

include_once dirname( dirname(__FILE__) ).'/class/category.class.php';
include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';

$category =& D3diaryCategory::getInstance();
//--------------------------------------------------------------------
// GET Initial Valuses
//--------------------------------------------------------------------

$myname = "viewcomment.php";
$yd_list=array(); $yd_com_key=""; $yd_monthnavi="";

$req_uid = isset($_GET['req_uid']) ? (int)$_GET['req_uid'] : 0;

$d3dConf =& D3diaryConf::getInstance($mydirname, $req_uid, "viewcomment");
$func =& $d3dConf->func ;
$myts =& $d3dConf->myts;
$mPerm =& $d3dConf->mPerm ;
$gPerm =& $d3dConf->gPerm ;
$mod_config =& $d3dConf->mod_config ;

$uid = $d3dConf->uid;

if($d3dConf->dcfg->blogtype!=0){
    header("Location:".  XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=other&req_uid='.$d3dConf->dcfg->uid);
	exit();
}

$editperm=0;
$owner=0;
$_tempGperm = $gPerm->getUidsByName( array_keys($gPerm->gperm_config) );
// check edit permission by group
if(isset($_tempGperm['allow_edit'])){
	if(isset($_tempGperm['allow_edit'][$uid])) {
		if($req_uid==$uid){$owner=1;$editperm=1;}
		if($mPerm->isadmin){$editperm=1;}
	}
}

// check mailpost permission for access user's group
$allow_mailpost = 0;
if( $mod_config['use_mailpost']==1 && !empty($_tempGperm['allow_mailpost'])){
	if(isset($_tempGperm['allow_mailpost'][$uid]) && $owner=1) {
		$allow_mailpost = 1;
	}
}	unset($_tempGperm);

if(!$mPerm->check_exist_user($req_uid)){
	//if($uid>0){
	//	$req_uid=$uid;
	//	$openarea=$d3dConf->dcfg->openarea;
	//}else{
		$req_uid=0;
		$openarea=0;
	//}
}

// define Template
$xoopsOption['template_main']= $mydirname.'_viewcomment.html';

include XOOPS_ROOT_PATH."/header.php";
// this page uses smarty template
// this must be set before including main header.php

$mid = $d3dConf->mid;

// assign module header for css
$d3diary_header = '<link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=main_css" />'."\r\n";
//$xoopsTpl->assign( 'xoops_module_header' ,$xoopsTpl->get_template_vars( 'xoops_module_header' ).$d3diary_header );

$yd_param['mode']=$func->getpost_param('mode');

if(strcmp($yd_param['mode'], "category")==0){
	$yd_param['cid']=$func->getpost_param('cid');
	$category->uid=$req_uid;
	$category->cid=$yd_param['cid'];
	$category->getchildren($mydirname);
	if($category->blogtype!=0){
		header("Location:".  XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=other&req_uid='.$d3dConf->dcfg->uid.'&cid='.$req_cid);
		exit();
	}
	$yd_param['cname'] = $myts->makeTboxData4Show($category->cname);

}elseif(strcmp($yd_param['mode'], "date")==0){
	$yd_param['year']=intval($func->getpost_param('year'));
	$yd_param['month']=intval($func->getpost_param('month'));
	$yd_param['day']=intval($func->getpost_param('day'));
	if (!empty($yd_param['year'])){$url_tag.="&amp;year=".$yd_param['year'];}
	if (!empty($yd_param['month'])){$url_tag.="&amp;month=".$yd_param['month'];}
	if (!empty($yd_param['day'])){$url_tag.="&amp;day=".$yd_param['day'];}
}elseif(strcmp($yd_param['mode'], "month")==0){
	$yd_param['year']=intval($func->getpost_param('year'));
	$yd_param['month']=intval($func->getpost_param('month'));
	if (!empty($yd_param['year'])){$url_tag.="&amp;year=".$yd_param['year'];}
	if (!empty($yd_param['month'])){$url_tag.="&amp;month=".$yd_param['month'];}
}elseif(strcmp($yd_param['mode'], "all")==0){
	$yd_param['mode']="all";
}

	if(empty($yd_param['year'])) {
		$yd_param['year']=intval(date("Y"));
		$yd_param['month']=intval(date("n"));
		$yd_param['prev_year'] = $yd_param['year'] -1;
		$yd_param['next_year'] = $yd_param['year'] +1;
	}

	$com_dirname = $mod_config['comment_dirname'];
	$com_forum_id = intval($mod_config['comment_forum_id']);
	$com_anchor_type = intval($mod_config['comment_anchor_type']);

	$req_cid=intval($func->getpost_param('cid'));
	$req_uid2=intval($func->getpost_param('req_uid'));

	if(intval($req_uid2)>0) {
		$whr_uids="AND d.uid=".intval($req_uid2);
	} else {$whr_uids="";}

 		$whr_time = "";

		if(strcmp($yd_param['mode'], "category")==0){
			if($category->children){
				$whr_time.=" and d.cid IN (".implode(",",$category->children).") ";
			} else {
				$whr_time.=" and d.cid='".$yd_param['cid']."' ";
			}
		}elseif(strcmp($yd_param['mode'], "friends")==0){
    			if (!empty($mPerm->req_friends)) {
				$whr_uids="d.uid IN (".implode(',',$mPerm->req_friends).")";
			}
		}elseif(strcmp($yd_param['mode'], "date")==0){
			$whr_time.=" and d.create_time>='".$yd_param['year']."-".$yd_param['month']
				."-".$yd_param['day']." 00:00:00"."' ";
			$whr_time.=" and d.create_time<='".$yd_param['year']."-".$yd_param['month']
				."-".$yd_param['day']." 23:59:59"."' ";
		}elseif(strcmp($yd_param['mode'], "month")==0){
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

	if($mPerm->isadmin){
		$whr_openarea = "";
	} else {
		$_params4op['use_gp'] = $gPerm->use_gp;
		$_params4op['use_pp'] = $gPerm->use_pp;
		$whr_openarea = " AND ".$mPerm->get_open_query( "viewcomment1", $_params4op );
	}
		$now = date("Y-m-d H:i:s");
		if ($mPerm->isadmin!=true and $mPerm->isauthor!=true) {
			$whr_nofuture = " AND d.create_time<'".$now."' ";
		} else {
			$whr_nofuture = "";
		}

   	if($com_dirname && ($com_forum_id > 0)){
    	// d3comment integration
		$whr_forum = "f.forum_id='".$com_forum_id."'" ;
		// forums can be read by current viewer (check by forum_access)
		$got_forums_can_read = $func->get_d3comforums_can_read( $com_dirname ,$uid );
		if ( !in_array( $com_forum_id, $got_forums_can_read ) ) { exit ; }

		// *********** SQL for
		// get count of total comments
		$sql = "SELECT count(p.post_id) as count
			FROM ".$xoopsDB->prefix($com_dirname."_posts")." p 
			INNER JOIN ".$xoopsDB->prefix($com_dirname."_topics")." t USING(topic_id) 
			INNER JOIN ".$xoopsDB->prefix($com_dirname."_forums")." f 
				ON (f.forum_id=t.forum_id AND ".$whr_forum.") 
			INNER JOIN ".$xoopsDB->prefix($mydirname.'_diary')." d 
				ON t.topic_external_link_id=d.bid ".$whr_uids." 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_category')." c 
				ON ((d.uid=c.uid  OR c.uid='0') AND d.cid=c.cid) 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_config')." cfg ON d.uid=cfg.uid 
			WHERE ! t.topic_invisible ".$whr_openarea.$whr_time;

		$result = $xoopsDB->query($sql);
		while($dbdat = $xoopsDB->fetchArray($result)){ $num_rows=$dbdat['count']; }
		//var_dump($num_rows); echo"<br />";
		//var_dump($sql); echo"<br />";
		
		// end d3comment
	} else {
		// xoops comment

		// *********** SQL for
		// get count of total comments
		$sql = "SELECT count(com.com_id) as count
			FROM ".$xoopsDB->prefix('xoopscomments')." com 
			INNER JOIN ".$xoopsDB->prefix($mydirname.'_diary')." d 
				ON com.com_itemid=d.bid ".$whr_uids." 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_category')." c 
				ON ((d.uid=c.uid  OR c.uid='0') AND d.cid=c.cid) 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_config')." cfg ON d.uid=cfg.uid 
			WHERE com.com_modid='".$d3dConf->mid."' "
			.$whr_openarea.$whr_time;


		$result = $xoopsDB->query($sql);
		while($dbdat = $xoopsDB->fetchArray($result)){ $num_rows=$dbdat['count']; }
		//var_dump($num_rows); echo"<br />";
		//var_dump($sql); echo"<br />";
		
	} // end xoops comment count
	
	// page control
	 $max_entry = 50; // for test
	//$max_entry = intval($mod_config['block_diarynum']);
	$offset = $func->getpost_param('pofst');
	$offset = isset($offset) ? intval($offset) : 0;
	$offset2 = $offset-1;	// getting 1 entry before to enable other entry between pages
	if($offset <=0){ $offset=0; }
	if($offset2 <=0){ $offset2=0; }
	$whr_offset = " LIMIT ".$offset2.",".$max_entry ;
	
	if(!empty($num_rows)){
		$startnum = $offset+1;
		$endnum = $startnum + $max_entry;
		if ($endnum > $num_rows) { $endnum = $num_rows;	}
	} else {$startnum = 0;	$endnum = 0;}
	$xoopsTpl->assign(array("lang_datanum" => constant('_MD_DATANUM1').$num_rows. 
		constant('_MD_DATANUM2') .$startnum. constant('_MD_DATANUM3').$endnum. 
		constant('_MD_DATANUM4')));

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
	$xoopsTpl->assign("yd_pagenavi", $yd_pagenavi);

	$com_list = array();
    	
    	if($com_dirname && ($com_forum_id > 0)){
    	// d3comment
		    $sql = "SELECT p.post_id, p.subject, p.post_time, 
			p.uid, p.guest_name, p.unique_path, t.topic_external_link_id, u.uname, u.name, 
			d.bid, d.cid, c.cname, c.openarea as openareacat 
			FROM ".$xoopsDB->prefix($com_dirname."_posts")." p 
			INNER JOIN ".$xoopsDB->prefix($com_dirname."_topics")." t 
				ON (t.topic_id=p.topic_id AND ! t.topic_invisible ) 
			INNER JOIN ".$xoopsDB->prefix($com_dirname."_forums")." f 
				ON (f.forum_id=t.forum_id AND ".$whr_forum.") 
			INNER JOIN ".$xoopsDB->prefix($mydirname.'_diary')." d 
				ON t.topic_external_link_id=d.bid ".$whr_uids." 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_category')." c 
				ON (d.uid=c.uid  OR c.uid=0) AND d.cid=c.cid 
			LEFT JOIN ".$xoopsDB->prefix('users')." u ON p.uid=u.uid 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_config')." cfg ON d.uid=cfg.uid 
			WHERE ! t.topic_invisible ".$whr_openarea.$whr_time." 
			ORDER BY p.post_time DESC".$whr_offset;

		//var_dump($sql); echo"<br />";

		$result = $xoopsDB->query($sql);
		while ( $dbdat = $xoopsDB->fetchArray($result) ) {
			$yd_comment['com_id']  =  $dbdat['post_id'];
			if($com_anchor_type==1) { $yd_comment['unique_path'] = $dbdat['post_id']; }
			else { $yd_comment['unique_path'] = ltrim($dbdat['unique_path'], "."); }
			$yd_comment['title'] = $myts->makeTboxData4Show(xoops_substr($dbdat['subject'],0,40));
			$yd_comment['datetime']  = intval($dbdat['post_time']);
			$yd_comment['year']  = date("Y", $dbdat['post_time']);
			$yd_comment['month'] = date("m", $dbdat['post_time']);
			$yd_comment['day']   = date("d", $dbdat['post_time']);
			$yd_comment['time']  = date("H:i", $dbdat['post_time']);
			if((mktime()-60*60*24*7)<$dbdat['post_time']){
				$yd_comment['newcom'] = 1;
			}else{	$yd_comment['newcom'] = 0; }
			
			$yd_comment['bid']  =  $dbdat['topic_external_link_id'];
			if ($dbdat['uid']) {
				$yd_comment['uname'] = $dbdat['uname'];
				$yd_comment['name']  = (!empty($dbdat['name'])) ? $dbdat['name'] : "";
			} else { $yd_comment['name'] = $yd_comment['uname'] = $dbdat['guest_name']; }
			$com_list[] = $yd_comment;
		}
			if($com_anchor_type==1) { $yd_com_key = "#post_id"; } 
			else { $yd_com_key = "#post_path"; }
			
	//end d3comment
   	} else {
    	//xoops comment
		// entries
		//$q_order = 'p.post_time DESC';
		
		    $sql = "SELECT com.com_id, com.com_title, com.com_created, u.uname, u.name, 
			d.bid, d.openarea, d.cid, c.cname, c.openarea as openareacat 
			FROM ".$xoopsDB->prefix('xoopscomments')." com 
			INNER JOIN ".$xoopsDB->prefix($mydirname.'_diary')." d 
				ON com.com_itemid=d.bid ".$whr_uids."
			LEFT JOIN ".$xoopsDB->prefix('users')." u ON com.com_uid=u.uid 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_category')." c 
				ON ((d.uid=c.uid  OR c.uid='0') AND d.cid=c.cid) 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_config')." cfg ON d.uid=cfg.uid 
			WHERE com.com_modid='".$d3dConf->mid."' "
			.$whr_openarea.$whr_time." 
			ORDER BY com_created DESC".$whr_offset;

		//var_dump($sql); echo"<br />";

		$result = $xoopsDB->query($sql);
		while ( $dbdat = $xoopsDB->fetchArray($result) ) {
			$yd_comment['com_id']  =  $dbdat['com_id'];
			//$yd_comment['com_num']  =  $_com_count[$dbdat['bid']];
			$yd_comment['unique_path']  =  $dbdat['com_id'];
			$yd_comment['title'] = $myts->makeTboxData4Show(xoops_substr($dbdat['com_title'],0,40));
			$yd_comment['datetime']  = intval($dbdat['com_created']);
			$yd_comment['year']  = date("Y", $dbdat['com_created']);
			$yd_comment['month'] = date("m", $dbdat['com_created']);
			$yd_comment['day']   = date("d", $dbdat['com_created']);
			$yd_comment['time']  = date("H:i", $dbdat['com_created']);
			$yd_comment['bid']  = $dbdat['bid'];
			$yd_comment['uname']  = $dbdat['uname'];
			$yd_comment['name']  = (!empty($dbdat['name'])) ? $dbdat['name'] : "";
			if((mktime()-60*60*24*7)<$dbdat['com_created']){
				$yd_comment['newcom'] = 1;
			}else{ $yd_comment['newcom'] = 0; }

			$com_list[] = $yd_comment;
		}
			$yd_com_key = "#comment";
    	}

    	//var_dump($com_list);

// menu
if($mod_config['menu_layout']==1){
	$yd_layout = "left";
}elseif($mod_config['menu_layout']==2){
	$yd_layout = "";
}else{
	$yd_layout = "right";
}

	list( $arr_weeks, $arr_monthes, $arr_dclass, $arr_wclass ) = $func->initBoxArr();

//$yd_uname=d3diary_get_xoopsuname($req_uid);
$rtn = $func->get_xoopsuname($req_uid);
$yd_uname = (!empty($rtn['uname'])) ? $rtn['uname'] : "" ;
$yd_name = (!empty($rtn['name'])) ? $rtn['name'] : "" ;

$yd_avaterurl = $func->get_user_avatar(array($req_uid));

$xoops_pagetitle = ($mod_config['use_name']==1) ? $yd_name.constant("_MD_DIARY_PERSON") : 
			$yd_uname.constant("_MD_DIARY_PERSON") ;

// breadcrumbs
	$bc_para['diary_title'] = $xoopsTpl->get_template_vars('xoops_modulename');
	$bc_para['path'] = "index.php?page=viewcomment";
	$bc_para['uname'] = $yd_uname;
	$bc_para['name'] = (!empty($yd_name)) ? $yd_name : $yd_uname ;
	$bc_para['mode'] = "comment";
	$bc_para['bc_name'] = constant('_MD_COMMENT');
	
	$breadcrumbs = $func->get_breadcrumbs( $d3dConf->dcfg->uid, $bc_para['mode'], $bc_para );
	//var_dump($breadcrumbs);

if ($req_uid>0){
    if($mod_config['menu_layout']<=1){
	list( $yd_calender, $yd_cal_month ) =  $func->get_calender ($req_uid,date("Y"),date("m"), $uid);
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
			));
}

	$xoopsTpl->assign(array(
			"yd_uid" => $req_uid,
			"yd_uname" => $yd_uname,
			"yd_name" => $yd_name,
			"yd_editperm" => $editperm,
			"yd_owner" => $owner,
			"yd_openarea" => $openarea,
			"yd_layout" => $yd_layout,
			"yd_param" => $yd_param,
			"yd_counter" => $yd_counter,
			"com_list"  => $com_list,
			"yd_com_key"  => $yd_com_key,			
			"yd_mailpost"	=> $allow_mailpost,
			"catopt"  => $func->get_categories($req_uid,$uid),
			"mydirname" => $mydirname,
			"xoops_pagetitle" => $xoops_pagetitle,
			"xoops_breadcrumbs" => $breadcrumbs,
			"xoops_module_header" => 
				$xoopsTpl->get_template_vars( 'xoops_module_header' ).$d3diary_header,
			"mod_config" =>  $mod_config
			));

?>
