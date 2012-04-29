<?php

//--------------------------------------------------------------------
// Config
//--------------------------------------------------------------------


include_once dirname( dirname(__FILE__) ).'/class/tag.class.php';
include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';

// define constants

define ('_D3DIARY_EDITTAG_DELETE', '1' ) ;
define ('_D3DIARY_EDITTAG_REV', '2' ) ;
define ('_D3DIARY_EDITTAG_ADD', '3' ) ;

$tag =& D3diaryTag::getInstance();
$d3dConf = & D3diaryConf::getInstance($mydirname);
$func =& $d3dConf->func ;
$mPerm =& $d3dConf->mPerm ;
$gPerm =& $d3dConf->gPerm ;

$uid = $d3dConf->uid;
if( $uid<=0 ){
    redirect_header(XOOPS_URL.'/user.php',2,_MD_IVUID_ERR);
	exit();
}

$d3dConf->set_mod_config(0,"edit_tags");	// needs $dcfg
$uname = $d3dConf->uname;

$_tempGperm = $gPerm->getUidsByName( array('allow_edit') );
// check edit permission by group
if(!empty($_tempGperm['allow_edit'])){
	if(!isset($_tempGperm['allow_edit'][$uid])) {
		redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
		exit();
	}
} else {
		redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
		exit();
}	unset($_tempGperm);

$req_uid = intval($func->getpost_param('req_uid'));
$tagaction = intval($func->getpost_param('tagaction'));
$org_tag = rawurldecode($func->getpost_param('old_tag'));
$rev_tag = rawurldecode($func->getpost_param('rev_tag'));
$post_tags = $func->getpost_param('tags');
$q_string = htmlspecialchars( $func->getpost_param('q_string'), ENT_QUOTES );

$chk_bids= $func->getpost_param('chk_bids');
$sel_bids = $chk_bids ? array_map("intval" ,$chk_bids) : Array();

if(!$mPerm->check_exist_user($req_uid)){
	if($uid>0){
		redirect_header(XOOPS_URL.'/modules/'.$mydirname.'/index.php',4,_MD_IVUID_ERR);
	}else{
		header("Location:". XOOPS_URL.'/modules/'.$mydirname.'/diarylist.php');
	}
	exit();
}

if( !$sel_bids || $tagaction<1 ){
		header("Location:".XOOPS_URL.'/modules/'.$mydirname.'/index.php?'.$q_string);
}

require XOOPS_ROOT_PATH.'/header.php';

$owner=0; $editperm=0;
if($req_uid==$uid){$owner=1;$editperm=1;}
if($mPerm->isadmin){$editperm=1;}	

if($editperm != 1) {
    redirect_header(XOOPS_URL.'/',4,_MD_NOPERM_VIEW);
	exit();
}


switch ($tagaction) {
case _D3DIARY_EDITTAG_DELETE:
	// delete tags at once
	$tag->uid = $req_uid;
	$tag->bids = $sel_bids;
	$tag->tag_name = $org_tag;
	$tag->deletedb_byname_mul($mydirname);

	redirect_header( XOOPS_URL.'/modules/'.$mydirname.'/index.php?'.$q_string,2,_MD_TAG_DELETED);
	break;

case _D3DIARY_EDITTAG_REV:
	// update edited tags
	$tag->uid = $req_uid;
	$tag->bids = $sel_bids;
	$tag->tag_name = strip_tags($org_tag);
	$tag->updatedb_byname_mul($mydirname, $rev_tag);

	redirect_header( XOOPS_URL.'/modules/'.$mydirname.'/index.php?'.$q_string,2,_MD_TAG_UPDATED);
	break;

case _D3DIARY_EDITTAG_ADD:
	// update edited tags
	$tag->uid = $req_uid;
	$tag->bids = $sel_bids;
	
	if(!empty($post_tags)) {
		if (function_exists('mb_convert_kana')){
			preg_match_all("/\[(.+)\]/U", mb_convert_kana($post_tags, 'asKV'), $tags);
		} else {
			preg_match_all("/\[(.+)\]/U", $post_tags, $tags);
		}
		$arr_tags = array_unique($tags[1]);
		
		foreach ( $arr_tags as $q_tag ) {
			$tag->tag_name = strip_tags($q_tag);
			$tag->read_bid_byname($mydirname);

			// exclude existed bid which has the $q_tag
			$tag->bids = array_diff($sel_bids, $tag->bids);
			$tag->insertdb_byname_mul($mydirname);
		}
	}

	redirect_header( XOOPS_URL.'/modules/'.$mydirname.'/index.php?'.$q_string,2,_MD_TAG_UPDATED);
	break;

default:
}

?>
