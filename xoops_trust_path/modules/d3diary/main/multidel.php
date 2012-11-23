<?php

include_once dirname( dirname(__FILE__) ).'/class/diary.class.php';
include_once dirname( dirname(__FILE__) ).'/class/photo.class.php';
include_once dirname( dirname(__FILE__) ).'/class/tag.class.php';
include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';

$diary =& D3diaryDiary::getInstance();
$photoObj =& D3diaryPhoto::getInstance();
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

$d3dConf->set_mod_config(0,"editcat_diaries");	// needs $dcfg
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
$chkarr_bids = $func->getpost_param('chk_bids');		
$selarr_bids = $chkarr_bids ? array_map("intval" ,$chkarr_bids) : Array();

if(!$mPerm->check_exist_user($req_uid)){
	if($uid>0){
		redirect_header(XOOPS_URL.'/modules/'.$mydirname.'/index.php',4,_MD_IVUID_ERR);
	}else{
		header("Location:". XOOPS_URL.'/modules/'.$mydirname.'/diarylist.php');
	}
	exit();
}

if( ! count($selarr_bids) > 0  ){
		header("Location:". XOOPS_URL.'/modules/'.$mydirname.'/index.php?req_uid='.$req_uid.'&mode=category&cid='.$org_cid);
}

$owner=0; $editperm=0;
if($req_uid==$uid){$owner=1;$editperm=1;}
if($mPerm->isadmin){$editperm=1;}	

if($editperm != 1) {
	redirect_header(XOOPS_URL.'/',4,_MD_NOPERM_VIEW);
	exit();
}

// delete multi entries
	$diary->uid = $req_uid;
	$diary->bids = $selarr_bids;
	$deleted_bids = $diary->deletedb_mul($mydirname);
	
	if ( count($deleted_bids) > 0 ){
		list( $uploaddir, $previewdir ) = $d3dConf->get_photodir() ;
		$previewpath = $uploaddir.$previewdir ;

		$photoObj->bids = $deleted_bids;
		$photoObj->readdb_mul($mydirname);
		$photos_bid = $photoObj->photos;

		if ( count($photos_bid) > 0 ) {
			$photoObj->uid=$diary->uid;
			foreach ( $photos_bid  as $bid => $photos ) {
				$pids = array();
				foreach ( $photos as $photo ) {
					/* delfile */
					unlink($uploaddir.$photo['pname']);
					unlink($uploaddir.$photo['thumbnail']);
					$pids[] = "'".$photo['pid']."'";
				}
				// delete photo DB
				$photoObj->bid=$bid;
				$photoObj->pids=$pids;
				$photoObj->deletedb_mul($mydirname);
			}
		}

		// delete tags
		$tag->uid=$diary->uid;
		foreach ( $deleted_bids as $_bid ) {
			$tag->bid = $_bid;
			$tag->delete_by_bid($mydirname);
		}
	}

	redirect_header( XOOPS_URL.'/modules/'.$mydirname.'/index.php?req_uid='.$uid,2,_MD_DIARY_DELETED);

?>
