<?php

require_once XOOPS_TRUST_PATH.'/modules/d3forum/class/D3commentAbstract.class.php' ;

// a class for d3forum comment integration
class d3diaryD3commentContent extends D3commentAbstract {

	var $d3dConf = null;

public function __construct( $d3forum_dirname , $target_dirname , $target_trustdirname = '' )
{
	parent::__construct( $d3forum_dirname , $target_dirname , $target_trustdirname );
	$mydirname = $this->mydirname ;

	require_once dirname(__FILE__).'/d3diaryConf.class.php';
	$this->d3dConf =& D3diaryConf::getInstance($mydirname, 0, "d3comment");
	$this->mPerm =& $this->d3dConf->mPerm;
	$this->func =& $this->d3dConf->func;
	$this->mod_config =& $this->d3dConf->mod_config;

}

function fetchSummary( $external_link_id )
{
	global $xoopsDB;

	$db =& $this->d3dConf->db;
	$myts =& $this->d3dConf->myts;
	
	$entryID = intval( $external_link_id ) ;
	
	$mydirname = $this->mydirname ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$uid = $this->d3dConf->uid;

	$sql = "SELECT d.bid AS bid, d.uid AS uid, d.cid AS cid, d.title AS title, d.vgids AS vgids, d.vpids AS vpids, 
		d.diary AS diary, d.create_time AS create_time, d.openarea AS openarea_entry, d.create_time, 
		d.dohtml AS dohtml, cfg.openarea AS openarea, 
		c.openarea AS openarea_cat, c.vgids AS vgids_cat, c.vpids AS vpids_cat 
		FROM ".$db->prefix($mydirname.'_diary')." d LEFT JOIN "
		.$db->prefix($mydirname.'_config')." cfg ON d.uid=cfg.uid LEFT JOIN "
		.$db->prefix($mydirname.'_category')." c ON (d.cid=c.cid AND (d.uid=c.uid OR c.uid='0')) WHERE d.bid ='".$entryID."'" ;
		
	$content_row = $db->fetchArray( $db->query( $sql ) );
	
		//var_dump($sql); var_dump($content_row);

	$diary_uid = (int)$content_row['uid'];
	$dohtml = (int)$content_row['dohtml'];

	$_tmp_isfriend  = $this->mPerm->check_is_friend($diary_uid);
	$_tmp_isfriend2 = $this->mPerm->check_is_friend2($diary_uid);
	
	$_tmp_op = (int)$content_row['openarea'];

		list( $_got_op , $_slctd_op , $_tmp_gperms, $_tmp_pperms ) 
			= $this->d3dConf->mPerm->override_openarea( $_tmp_op, $content_row['openarea_entry'], $content_row['openarea_cat'], 
				$content_row['vgids'], $content_row['vpids'], $content_row['vgids_cat'], $content_row['vpids_cat'] );

			// var_dump($_tmp_gperms); var_dump($_tmp_pperms);
		$permission = $this->d3dConf->mPerm->can_display($diary_uid, $_got_op, 
				$content_row['create_time'], $_tmp_isfriend, $_tmp_isfriend2, $_tmp_gperms, $_tmp_pperms);

	//var_dump($diary_uid); var_dump($_got_op);   var_dump($_slctd_op); var_dump($content_row['create_time']); var_dump($_tmp_isfriend); var_dump($_tmp_isfriend2); var_dump($_tmp_gperms); var_dump($_tmp_pperms);  var_dump($content_row['openarea_entry']); var_dump($content_row['openarea_cat']); echo"<br />";

	//checking permission : if false, redirect
		//if( $permission != true ) {
		if( $permission != true && ($this->mPerm->exerpt_ok_bymod !== true || $this->mod_config['can_disp_com'] !== 1) ) {
			redirect_header(XOOPS_URL.'/user.php',3,_NOPERM);
			exit();
		}

	$categoryID = $content_row['cid'];
	$uri = XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=detail&bid='.$entryID.'&req_uid='.$diary_uid;

	$summary =  $this->func->substrTarea( $content_row['diary'], $dohtml, 255, true ) ;

	return array(
		'dirname' => $mydirname ,
		'module_name' => $this->d3dConf->module_name ,
		'subject' => $myts->makeTboxData4Show( $content_row['title'] ) ,
		'uri' => $uri,
		'summary' => $summary,
	) ;
}

// callback on newtopic/edit/reply/delete  overrides function added 2009/03/31
function onUpdate( $mode , $link_id , $forum_id , $topic_id , $post_id = 0 )
{
	global $xoopsDB, $xoopsConfig, $xoopsUser;

	if (($mode != "newtopic") && ($mode != "reply")) return true ;
	
	// non-module integration returns false quickly
	if( ! is_object( $this->module ) ) return false ;
	$not_module =& $this->module ;
	$not_modid = $this->module->getVar('mid') ;
	$not_catinfo =& notificationCommentCategoryInfo( $not_modid ) ;
	// module without 'comment' notification
	if( empty( $not_catinfo ) ) return false ;

	$mydirname = $this->mydirname ;

	//get uname
	if(is_object($xoopsUser)) {
		$uname = $xoopsUser->getVar('uname');
		$name = $xoopsUser->getVar('name');
	}
	$notif_name = $this->mod_config['use_name']==1 ? $name : $uname;
	
	$openarea_entry = 0; //default
	
	//get entry's title, bid, uid
	$sql = "SELECT d.uid, d.cid, d.title, d.bid, d.openarea AS openarea, d.vgids AS vgids, d.vpids AS vpids, 
			c.cid, c.openarea AS openarea_cat, c.vgids AS vgids_cat, c.vpids AS vpids_cat 
			FROM ".$xoopsDB->prefix($mydirname.'_diary')." d 
			LEFT JOIN ".$xoopsDB->prefix($mydirname.'_category')." c 
			ON ((c.uid=d.uid or c.uid='0') and d.cid=c.cid) 
			WHERE bid = '".intval($link_id)."'";
			
	//$sql = "SELECT * FROM " . $xoopsDB->prefix($mydirname.'_diary') . " WHERE bid = '".intval($link_id)."'";
	$result = $xoopsDB->query($sql);
	while ( $dbdat = $xoopsDB->fetchArray($result)){
		$title = $dbdat['title'];
		$bid = intval($dbdat['bid']);
		$uid = intval($dbdat['uid']);
		$cid = intval($dbdat['cid']);
		$openarea_entry = intval($dbdat['openarea']);
		$openarea_cat = intval($dbdat['openarea_cat']);
		$vgids = intval($dbdat['vgids']);
		$vpids = intval($dbdat['vpids']);
		$vgids_cat = intval($dbdat['vgids_cat']);
		$vpids_cat = intval($dbdat['vpids_cat']);
	}

	//$openarea_cat = $this->d3dConf->func->get_openarea_cat($uid,$cid);
	
	// Trigger Notification
	$users2notify = $this->mPerm->get_users_can_read_entry( $openarea, $openarea_entry, $openarea_cat, 
				$vgids, $vpids, $vgids_cat, $vpids_cat );
	$not_handler =& D3NotificationHandler::getInstance() ;
	
	$comment_tags = array( 'ENTRY_TITLE' => $title , 'ENTRY_BLOGGER' => $notif_name , 'ENTRY_URI' => XOOPS_URL."/modules/".$mydirname."/index.php?page=detail&bid=".$link_id."&req_uid=".$uid."#comment" ) ;
	$not_handler->triggerEvent( $this->mydirname , 'd3diary' , 'blogger' , $uid , 'new_comment' , $comment_tags , $users2notify ) ;
	
	$not_category = $not_catinfo['name'] ;
	$not_itemid = $link_id ;
	$not_handler->triggerEvent( $this->mydirname , 'd3diary' , $not_category , $not_itemid , 'comment' , $comment_tags , $users2notify ) ;
	
	return true ;
}

// override function
function validate_id( $link_id )
{
	$link_id = intval( $link_id ) ;
	$mydirname = $this->mydirname ;

	$db =& Database::getInstance() ;

	list( $count ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix($mydirname."_diary")." WHERE bid='".$link_id."'" ) ) ;
	if( $count <= 0 ) return false ;

	$sql = "SELECT d.bid AS bid, d.uid AS uid, d.vgids AS vgids, d.vpids AS vpids, d.create_time, 
			d.openarea AS openarea_entry, cfg.openarea AS openarea, 
			c.openarea AS openarea_cat,  c.vgids AS vgids_cat, c.vpids AS vpids_cat 
			FROM ".$db->prefix($mydirname.'_diary')." d LEFT JOIN "
			.$db->prefix($mydirname.'_config')." cfg ON d.uid=cfg.uid LEFT JOIN "
			.$db->prefix($mydirname.'_category')." c ON (d.cid=c.cid AND (d.uid=c.uid OR c.uid='0')) 
			WHERE d.bid ='".$link_id."'" ;

	$content_row = $db->fetchArray( $db->query( $sql ) );
	
	$diary_uid = (int)$content_row['uid'];

	$this->d3dConf->set_mod_config( $diary_uid  , "D3com_validate_id");	// needs $dcfg, $diary_uid

	$_tmp_isfriend  = $this->mPerm->check_is_friend($diary_uid);
	$_tmp_isfriend2 = $this->mPerm->check_is_friend2($diary_uid);
	
	$_tmp_op = (int)$content_row['openarea'];
		list( $_got_op , $_slctd_op , $_tmp_gperms, $_tmp_pperms ) 
			= $this->mPerm->override_openarea( $_tmp_op, $content_row['openarea_entry'], $content_row['openarea_cat'], 
				$content_row['vgids'], $content_row['vpids'], $content_row['vgids_cat'], $content_row['vpids_cat'] );

			// var_dump($_tmp_gperms); var_dump($_tmp_pperms);
		$permission = $this->mPerm->can_display($diary_uid, $_got_op, 
				$content_row['create_time'], $_tmp_isfriend, $_tmp_isfriend2, $_tmp_gperms, $_tmp_pperms);

	//var_dump($diary_uid); var_dump($_got_op);   var_dump($_slctd_op); var_dump($content_row['create_time']); var_dump($_tmp_isfriend); var_dump($_tmp_isfriend2); var_dump($_tmp_gperms); var_dump($_tmp_pperms);  var_dump($content_row['openarea_entry']); var_dump($content_row['openarea_cat']); echo"<br />";

	if( $permission === true ) {
		return $link_id  ;
	} elseif ( $this->mPerm->exerpt_ok_bymod === true && $this->mod_config['can_disp_com'] === 1 ) {
		return $link_id  ;
	}
		return false;
}

// naao added Nov.2012
// array of users id to be notified
// if you want to check authrity validation for parent entry, override it
function validate_users2notify( $link_id, $users2notify=array() )
{
	$link_id = intval( $link_id ) ;
	$mydirname = $this->mydirname ;

	$db =& Database::getInstance() ;

	list( $count ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix($mydirname."_diary")." WHERE bid='".$link_id."'" ) ) ;
	if( $count <= 0 ) return false ;

	$sql = "SELECT d.bid AS bid, d.uid AS uid, d.vgids AS vgids, d.vpids AS vpids, d.create_time, 
			d.openarea AS openarea_entry, cfg.openarea AS openarea, 
			c.openarea AS openarea_cat,  c.vgids AS vgids_cat, c.vpids AS vpids_cat 
			FROM ".$db->prefix($mydirname.'_diary')." d LEFT JOIN "
			.$db->prefix($mydirname.'_config')." cfg ON d.uid=cfg.uid LEFT JOIN "
			.$db->prefix($mydirname.'_category')." c ON (d.cid=c.cid AND (d.uid=c.uid OR c.uid='0')) 
			WHERE d.bid ='".$link_id."'" ;

	$content_row = $db->fetchArray( $db->query( $sql ) );
	
	$diary_uid = (int)$content_row['uid'];

	$this->d3dConf->set_mod_config( $diary_uid  , "D3com_validate_id");	// needs $dcfg, $diary_uid

	$_tmp_isfriend  = $this->mPerm->check_is_friend($diary_uid);
	$_tmp_isfriend2 = $this->mPerm->check_is_friend2($diary_uid);
	
	$_tmp_op = (int)$content_row['openarea'];

		// 1st parameter $openarea is byref
	$d3diary_users2notify = $this->mPerm->get_users_can_read_entry( 
			$_tmp_op, $content_row['openarea_entry'], $content_row['openarea_cat'], 
			$content_row['vgids'], $content_row['vpids'], $content_row['vgids_cat'], $content_row['vpids_cat'] );

	return array_intersect( $users2notify, $d3diary_users2notify );
}

// set forum_dirname from config.comment_dirname
function setD3forumDirname( $d3forum_dirname = '' )
{
	if( ! empty($this->mod_config['comment_dirname'] ) ) {
    		$this->d3forum_dirname = $this->mod_config['comment_dirname'] ;
	} elseif( ! empty( $params['comment_dirname'] ) ) {
		$this->d3forum_dirname = $params['comment_dirname'] ;
	} elseif( $d3forum_dirname ) {
		$this->d3forum_dirname = $d3forum_dirname ;
	} else {
		$this->d3forum_dirname = 'd3forum' ;
	}
}

// d3comment for legacy comment callback overrides function added 2009/03/31
function processCommentNotifications( $mode , $link_id , $forum_id , $topic_id , $post_id )
{
	return true ;
}

}

?>