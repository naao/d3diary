<?php

//--------------------------------------------------------------------
// Config
//--------------------------------------------------------------------
require_once(dirname( dirname(__FILE__) ).'/lib/PEAR/XML/RPC.php');

include_once dirname( dirname(__FILE__) ).'/class/diary.class.php';
include_once dirname( dirname(__FILE__) ).'/class/category.class.php';
include_once dirname( dirname(__FILE__) ).'/class/photo.class.php';
include_once dirname( dirname(__FILE__) ).'/class/tag.class.php';
include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';

$diary =& D3diaryDiary::getInstance();
$category =& D3diaryCategory::getInstance();
$photoObj =& D3diaryPhoto::getInstance();
$tag =& D3diaryTag::getInstance();

require_once XOOPS_TRUST_PATH.'/libs/altsys/class/D3NotificationHandler.class.php' ;

// define constants

define ('_D3DIARY_PROCMODE_FORM', '1' ) ;
define ('_D3DIARY_PROCMODE_PREVIEW', '2' ) ;
define ('_D3DIARY_PROCMODE_CREATE', '3' ) ;
define ('_D3DIARY_PROCMODE_EDIT', '4' ) ;
define ('_D3DIARY_PROCMODE_DELETE', '8' ) ;
define ('_D3DIARY_PROCMODE_PHOTODEL', '9' ) ;
define ('_D3DIARY_PROCMODE_PHOTOROTATE', '10' ) ;

//--------------------------------------------------------------------
// GET Initial Values
//--------------------------------------------------------------------

$myname = "edit.php";

$d3dConf =& D3diaryConf::getInstance($mydirname, 0, "edit");
$func =& $d3dConf->func ;
$myts =& $d3dConf->myts;
$mPerm =& $d3dConf->mPerm ;
$gPerm =& $d3dConf->gPerm ;
$mod_config =& $d3dConf->mod_config ;

$uid = $d3dConf->uid;
$d3dConf->req_uid = $req_uid = isset($_GET['req_uid']) ? (int)$_GET['req_uid'] : $uid;
$mPerm->ini_set();

if( $uid<=0 ){
    redirect_header(XOOPS_URL.'/user.php',2,_MD_IVUID_ERR);
	exit();
}

if($d3dConf->dcfg->blogtype!=0){
    header("Location:".  XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=usr_config');
	exit();
}

$openarea = intval($d3dConf->dcfg->openarea)!=0 ? intval($d3dConf->dcfg->openarea) : 0;
$now = date("Y-m-d H:i:s");

$diary->bid = (int)$func->getpost_param('bid');
$diary->uid = $req_uid;

// edit parameters
$eparam = array() ;
$eparam['is_prev'] = 0 ;
// photo parameters

list( $eparam['uploaddir'] , $eparam['previewdir'] ) = $d3dConf->get_photodir() ;	// photo upload dir, preview dir
$eparam['th_size']= empty( $mod_config['photo_thumbsize'] ) ? 160 : intval($mod_config['photo_thumbsize']);
										// photo thumbnail size
$eparam['postmax']= intval($mod_config['photo_maxpics']);		// max photo numbers

//
// set mode for processing the branch
//

//$post_val['photodel'] = $func->getpost_param('photodel') ;
//$post_val['photorotate'] = $func->getpost_param('photorotate') ;
$post_val['delsub'] = $func->getpost_param('delsub') ;
$post_val['submit1'] = $func->getpost_param('submit1') ;
$post_val['preview'] = $func->getpost_param('preview') ;
$post_val['phandle'] = $func->getpost_param('phandle') ;
$action = (int)$func->getpost_param('action') ;

if( !empty($post_val['phandle']) && $action == 5 && $diary->bid>0 ) {
	$eparam['mode'] = _D3DIARY_PROCMODE_PHOTODEL ;		// delete photo
} elseif( !empty($post_val['phandle']) && ($action == 2 || $action == 3) && $diary->bid>0 ) {
	$eparam['mode'] = _D3DIARY_PROCMODE_PHOTOROTATE ;	// rotate photo
} elseif( !empty($post_val['delsub'] ) && $diary->bid>0 ) {
	$eparam['mode'] = _D3DIARY_PROCMODE_DELETE ;		// delete
} elseif( !empty($post_val['preview']) ) {
	$eparam['mode'] =_D3DIARY_PROCMODE_PREVIEW ;		// preview now
} elseif( !empty($post_val['submit1']) ) {
	if( $diary->bid>0 ) {
		$eparam['mode'] = _D3DIARY_PROCMODE_EDIT ;	// edit update
	} else {
		$eparam['mode'] = _D3DIARY_PROCMODE_CREATE ;	// create newly
	}
} else {
	$eparam['mode'] = _D3DIARY_PROCMODE_FORM ;		// show form or preview
}

//
// for access check
//

	if ($mPerm->isadmin && 0 < intval($d3dConf->req_uid)) {
		$req_uid = intval($d3dConf->req_uid);
		$rtn = $func->get_xoopsuname($req_uid);
		$uname = $rtn['uname'];
		$name = (!empty($rtn['name'])) ? $rtn['name'] : "" ;
		$rtn = $func->get_xoopsuname($uid);
		$myuname = $rtn['uname'];
		$myname = (!empty($rtn['name'])) ? $rtn['name'] : "" ;
	} else {
		$req_uid = $uid;
		$rtn = $func->get_xoopsuname($uid);
		$uname = $rtn['uname'];
		$name = $rtn['name'];
	}
	
	$notif_name = $mod_config['use_name']==1 ? $name : $uname;

// STEP0 check edit permission

	if(($diary->bid>0 and !$mPerm->check_editperm($diary->bid, $uid)) or ($uid != $req_uid && $diary->bid <= 0)){
		redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
		exit();
	}

	// get permission unames for each groupPermission
	// check edit permission by group
	$_tempGperm = $gPerm->getUidsByName( array_keys($gPerm->gperm_config) );
	if(!empty($_tempGperm['allow_edit'])){
		if(!isset($_tempGperm['allow_edit'][$uid])) {
			redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
			exit();
		}
	} else {
		redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
		exit();
	}

// STEP 1: get photos name

// set "is preview" for preview or delete photo
	if(!empty($post_val['preview']) || $eparam['mode'] == _D3DIARY_PROCMODE_PHOTODEL
			 || $eparam['mode'] == _D3DIARY_PROCMODE_PHOTOROTATE){
		$eparam['is_prev']  = 1 ;
	}

	// set "was preview" for last access was preview
	$eparam['was_prev'] = (int)$func->getpost_param('was_prev') ;	

	// pname in preview
	$prev_pname = $func->getpost_param('pname');
	$prev_pname = !empty($prev_pname) ? $prev_pname : array();
	$prev_pid = $func->getpost_param('pid');
	$prev_pid = !empty($prev_pid) ? $prev_pid : array();
	$prev_info = $func->getpost_param('pvinfo');
	$prev_info = !empty($prev_info) ? $prev_info : array();

	$form_photos = array() ;
	$count_pr_pid = count($prev_pid) ;
	if ( $count_pr_pid > 0 ) {
		for ($i=0; $i<$count_pr_pid; $i++) {
			$form_photo['pid'] = $prev_pid[$i] ;
			$form_photo['pname'] = $prev_pname[$i] ;
			$form_photo['info'] = $prev_info[$i] ;
			$form_photos[] = $form_photo ;
		}
	}

	// photo name by submit with photos
	$pinfo = $func->getpost_param('pinfo');
	$pinfo = !empty($pinfo) ? $pinfo : array();

	// photo delete check by submit with photos
	$temp_psels = $func->getpost_param('psel');
	$psels = array(); $psel_names = array();
	if (!empty($temp_psels)) {
		foreach ( $temp_psels as $temp_psel ) {
			list( $psels[], $psel_names[] ) = explode( "::", $temp_psel );
		}
	}
	//var_dump($psels); echo "<br><br>"; var_dump($psel_names); echo "<br><br>";

// STEP 2: get registered diary and photos if existed
	if($diary->bid>0){
		$diary->readdb($mydirname);
		if ( $eparam['is_prev'] == 1 ) { $diary->diary = $func->getpost_param('diary'); }
		// get registered photos
		list( $yd_data['photo_num'] , $yd_photo )= d3diary_readdb_photo($mydirname);
		if ( 0 < count($form_photos) ) { d3diary_swap_photoinfo (); }
	} else {
		$yd_data['photo_num'] = 0 ;
		$yd_photo = array() ;
	}

// STEP 3: branch to each processing mode

switch ( $eparam['mode'] ) {

    	// delete photo
    case _D3DIARY_PROCMODE_PHOTODEL :

	if($uid != $req_uid && $diary->bid <= 0){
		redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
		exit();
	}

	$rtnurl = $d3dConf->urluppr.$d3dConf->urlbase.$d3dConf->url4_all ;
	// delete registered photos ( $action == 5 )
	$result = $func->manage_photos( $photoObj, $diaryObj, $psels, $psel_names, $action, $eparam ) ;
	$_msg="";

		// readdb again after deleted
		list( $num_photos , $yd_photo )= d3diary_readdb_photo($mydirname);
		$yd_data['photo_num'] = $num_photos ;

		// read and add previewed photos
		$msg=d3diary_checkphoto( $mydirname );
		if(empty($msg)){
			list( $photo_num, $prev_photo ) = d3diary_prevphoto($mydirname);
			$i=0;
			$yd_photo = array_merge($yd_photo, $prev_photo);
			$yd_data['photo_num'] = $yd_data['photo_num'] + $photo_num;
		}

	// input form
   	d3diary_showform( $mydirname ) ;
	break ;

     	// rotate photo
    case _D3DIARY_PROCMODE_PHOTOROTATE :

	if($uid != $req_uid && $diary->bid <= 0){
		redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
		exit();
	}

	// rotate registered photos ( $action == 2 || $action == 3 )
	$result = $func->manage_photos( $photoObj, $diaryObj, $psels, $psel_names, $action, $eparam ) ;

	//$eparam['mode'] = _D3DIARY_PROCMODE_PREVIEW ;
	$rtnurl = XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=detail&bid='.$diary->bid ;
	redirect_header( $rtnurl, 2, _MD_DIARY_UPDATED);

	// input form
	//d3diary_showform( $mydirname ) ;
	break ;

    	// show form for edit or new input
    case _D3DIARY_PROCMODE_FORM :

	d3diary_showform( $mydirname ) ;

	break ;

    	// create
    case _D3DIARY_PROCMODE_CREATE ;
	if($uid != $req_uid){
		redirect_header(XOOPS_URL.'/user.php',2,_MD_NOPERM_EDIT);
		exit();
	}

	$diary->title=$func->getpost_param('title');
	$diary->dohtml=intval($func->getpost_param('dohtml'));
	if ($diary->dohtml == 1) {
		$diary->diary = $func->htmlPurifier($func->getpost_param('diary'));
	} else {
		$diary->diary = $func->getpost_param('diary');
	}
	$diary->cid=$func->getpost_param('cid');
	$diary->openarea=$func->getpost_param('openarea');
	$chk_vgids= $func->getpost_param('vgids');		
	$diary->vgids = $chk_vgids ? "|".implode("|", array_map("intval" ,$chk_vgids))."|" : "";
	$chk_vpids= $func->getpost_param('vpids');		
	$diary->vpids = $chk_vpids ? "|".implode("|", array_map("intval" ,explode("," , $chk_vpids)))."|" : "";
	$openarea = intval($diary->openarea)!=0 ? intval($diary->openarea) : $openarea;

	$yd_data['dohtml'] = $func->getpost_param('dohtml');
	
	if ($func->getpost_param('reg_time')) {
		d3diary_reg_time();
	}
	$cname=$func->getpost_param('cname');
	$msg=d3diary_checkphoto($mydirname);
	if(empty($msg)){
		// new category
		if($diary->cid==-1){
			$category->cname=$cname;
			if(!empty($category->cname)){
				$category->uid=$req_uid;
				$diary->cid=$category->insertdb($mydirname);
			}else{
				$diary->cid=0; // not categorized/other
			}
		}
		$yd_data['uid'] = $req_uid;
		$yd_data['cid'] = $diary->cid;
		$yd_data['title'] = htmlSpecialChars($diary->title, ENT_QUOTES);
		$yd_data['diary'] = htmlSpecialChars($diary->diary, ENT_QUOTES);
		$diary->bid=$diary->insertdb($mydirname);
		$yd_data['bid'] = $diary->bid;
		$yd_data['openarea']=$diary->openarea;	// naao added
		$yd_data['dohtml'] = $diary->dohtml;

		// needs bid
		if ($eparam['was_prev']==1){
			d3diary_regphoto($mydirname, "was_prev");
		} else {
			d3diary_regphoto($mydirname);
		}

		d3diary_regtags($mydirname);
		
		$category->cid = $diary->cid;
		$category->uid=$req_uid;
		$category->readdb($mydirname);

		// Trigger Notification using "Altsys D3NotificationHandler"
		$openarea_entry = intval($func->getpost_param('openarea'))!=0 ? 
					intval($func->getpost_param('openarea')) : 0;
		$category->cid = (int)$diary->cid;
		$category->uid = $uid;
		$category->readdb($mydirname);
		$openarea_cat = intval($category->openarea)!=0 ? intval($category->openarea) : 0;
		$vgids_cat = !empty($category->vgids) ? $category->vgids : "";
		$vpids_cat = !empty($category->vpids) ? $category->vpids : "";

		// 1st parameter $openarea is byref
		$users2notify = $mPerm->get_users_can_read_entry( $openarea, $yd_data['openarea'], $openarea_cat, 								$diary->vgids, $diary->vpids, $vgids_cat, $vpids_cat );

		$not_handler =& D3NotificationHandler::getInstance() ;
		
		$comment_tags = array( 'ENTRY_TITLE' => $yd_data['title'] , 'ENTRY_URI' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=detail&req_uid='.$req_uid.'&bid='.$diary->bid ) ;
		$not_handler->triggerEvent( $mydirname , 'd3diary' , 'global' , 0 , 'new_entry' , $comment_tags , $users2notify ) ;

		$comment_tags = array( 'ENTRY_TITLE' => $yd_data['title'] , 'ENTRY_BLOGGER' => $notif_name , 'ENTRY_URI' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=detail&req_uid='.$req_uid.'&bid='.$diary->bid ) ;
		$not_handler->triggerEvent( $mydirname , 'd3diary' , 'blogger' , $req_uid , 'new_entry' , $comment_tags , $users2notify ) ;
		

	    if (intval($func->getpost_param('update_ping'))==1 && $openarea==0 && ($diary->create_time <= $now)){
	    		$blogtitle=mb_convert_encoding($notif_name._MD_DIARY_TITLENAME, "UTF-8");
			$blogtopurl=mb_convert_encoding(XOOPS_URL.'/modules/'.$mydirname.'/index.php?req_uid='.$req_uid, "UTF-8");
			d3diary_ping_send($blogtitle, $blogtopurl);
	    }

		// increment post	added 2009/06/30 ver0.02
		if( is_object( @$xoopsUser ) ) {
			$xoopsUser->incrementPost() ;
		}

		redirect_header( XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=detail&bid='.$diary->bid,2,_MD_DIARY_CREATED);

	}else{
		$yd_data['error'] = $msg;
		// input form
		$yd_data['cname'] = htmlSpecialChars($cname, ENT_QUOTES);
		$yd_data['bid'] = 0;
		$yd_data['uid'] = $req_uid;
		$yd_data['cid'] = $diary->cid;
		$yd_data['title'] = htmlSpecialChars($diary->title, ENT_QUOTES);
		$yd_data['diary'] = htmlSpecialChars($diary->diary, ENT_QUOTES);
		$yd_data['photo_num'] = $num_photos;
		$yd_data['openarea']=$diary->openarea;	// naao added
		$yd_data['dohtml'] = $diary->dohtml;
		// set filename
		for($i=1;$i<=(3-$num_photos);$i++){
			$yd_data['filename'][$i] = $_FILES['filename']['name'][$i];
		}
		d3diary_showform( $mydirname ) ;
		break ;
	}


    	// edit update
    case _D3DIARY_PROCMODE_EDIT :
	$diary->readdb($mydirname);
	if(empty($diary->create_time)){
	    redirect_header( XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=edit',2,_MD_NODIARY_ERR);
		exit();
	}
	$diary->title=$func->getpost_param('title');
	$diary->dohtml=intval($func->getpost_param('dohtml'));
	if ($diary->dohtml == 1) {
		$diary->diary = $func->htmlPurifier($func->getpost_param('diary'));
	} else {
		$diary->diary = $func->getpost_param('diary');
	}
	$diary->cid=$func->getpost_param('cid');
	$chk_vgids= $func->getpost_param('vgids');		
	$diary->vgids = $chk_vgids ? "|".implode("|", array_map("intval" ,$chk_vgids))."|" : "";
	$chk_vpids= $func->getpost_param('vpids');
	$diary->vpids = $chk_vpids ? "|".implode("|", array_map("intval" ,explode("," , $chk_vpids)))."|" : "";

	if ( ($diary->openarea == 100) && (intval($func->getpost_param('openarea') != 100))) {
		if (intval($func->getpost_param('update_time'))==1) {
			$update_create_time = true;
			
			// Trigger Notification using "Altsys D3NotificationHandler"
			$openarea_entry = intval($func->getpost_param('openarea'))!=0 ? 
					intval($func->getpost_param('openarea')) : 0;
			$category->cid = (int)$diary->cid;
			$category->uid = $uid;
			$category->readdb($mydirname);
			$openarea_cat = intval($category->openarea)!=0 ? intval($category->openarea) : 0;
			$vgids_cat = !empty($category->vgids) ? $category->vgids : "";
			$vpids_cat = !empty($category->vpids) ? $category->vpids : "";

			// 1st parameter $openarea is byref
			$users2notify = $mPerm->get_users_can_read_entry( $openarea, $openarea_entry, $openarea_cat, 
						$diary->vgids, $diary->vpids, $vgids_cat, $vpids_cat );

			$not_handler =& D3NotificationHandler::getInstance() ;
			
			$comment_tags = array( 'ENTRY_TITLE' => $diary->title , 'ENTRY_URI' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=detail&req_uid='.$req_uid.'&bid='.$diary->bid ) ;
			$not_handler->triggerEvent( $mydirname , 'd3diary' , 'global' , 0 , 'new_entry' , $comment_tags , $users2notify ) ;

			$comment_tags = array( 'ENTRY_TITLE' => $diary->title , 'ENTRY_BLOGGER' => $notif_name , 'ENTRY_URI' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=detail&req_uid='.$req_uid.'&bid='.$diary->bid ) ;
			$not_handler->triggerEvent( $mydirname , 'd3diary' , 'blogger' , $req_uid , 'new_entry' , $comment_tags , $users2notify ) ;
		
		} else {
			$update_create_time = false;
		}
	} else {
		$update_create_time = false;
	}
	
	$diary->openarea=intval($func->getpost_param('openarea'));
	$openarea = intval($diary->openarea)!=0 ? intval($diary->openarea) : $openarea;
	
	if ($func->getpost_param('reg_time')) {
		d3diary_reg_time();
	}
	$cname=$func->getpost_param('cname');
	$msg=d3diary_checkphoto($mydirname);
	// subscribe
	if(empty($msg)){
		// new category
		if($diary->cid==-1){
			$category->cname= $cname;
			if(!empty($category->cname)){
				$category->uid=$req_uid;
				$diary->cid=$category->insertdb($mydirname);
			}else{
				$diary->cid=0; // not categorized/other
			}
		}
		$yd_data['bid'] = $diary->bid;
		$yd_data['uid'] = $req_uid;
		$yd_data['cid'] = $diary->cid;
		$yd_data['title'] = htmlSpecialChars($diary->title, ENT_QUOTES);
		$yd_data['dohtml'] = $diary->dohtml;
		$yd_data['diary'] = $yd_data['diary4edit'] = htmlSpecialChars($diary->diary, ENT_QUOTES);
		$yd_data['openarea']=$diary->openarea;	// naao added
		$yd_data['groups'] = $diary->vgids;
		
		$diary->updatedb($mydirname, $update_create_time);
		// needs bid
		if ($eparam['was_prev']==1){
			d3diary_regphoto($mydirname, 'was_prev');
		} else {
			d3diary_regphoto($mydirname);
		}
		if ( $yd_data['photo_num'] > 0 ) {
			d3diary_update_photoinfo ( $mydirname ) ;	//update photo info
		}
		d3diary_regtags($mydirname);

		$category->cid = $diary->cid;
		$category->uid=$req_uid;
		$category->readdb($mydirname);
		$openarea = intval($category->openarea)!=0 ? intval($category->openarea) : $openarea;
	    if (intval($func->getpost_param('update_ping'))==1 && $openarea==0 && ($diary->create_time <= $now)){
	    		$blogtitle=mb_convert_encoding($uname._MD_DIARY_TITLENAME, "UTF-8");
			$blogtopurl=mb_convert_encoding(XOOPS_URL.'/modules/'.$mydirname.'/index.php?req_uid='.$req_uid, "UTF-8");
			d3diary_ping_send($blogtitle, $blogtopurl);
	    }
		redirect_header( XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=detail&bid='.$diary->bid,2,_MD_DIARY_UPDATED );

	}else{
		//redirect_header( XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=edit&bid='.$diary->bid,2,$msg );
		$yd_data['error'] = $msg;
		d3diary_showform( $mydirname ) ;
		break ;
	}

   	// preview
    case _D3DIARY_PROCMODE_PREVIEW :

	$msg=d3diary_checkphoto( $mydirname );
	if(empty($msg)){
		list( $photo_num, $prev_photo ) = d3diary_prevphoto($mydirname);
		$yd_photo = array_merge($yd_photo, $prev_photo);
		$yd_data['photo_num'] = count($yd_photo);
	} else {
		$yd_data['error'] = $msg;
	}

	d3diary_showform( $mydirname ) ;
	break ;

    	// delete entry
    case _D3DIARY_PROCMODE_DELETE :

	$sql = "SELECT * FROM ".$xoopsDB->prefix($mydirname.'_photo')." 
	          WHERE uid='".intval($diary->uid)."' and bid='".intval($diary->bid)."'";
	$result = $xoopsDB->query($sql);
	
	$photoObj->uid=$diary->uid;
	$photoObj->bid=$diary->bid;
	while ( $dbdat = $xoopsDB->fetchArray($result) ) {
		$photoObj->pid=$dbdat['pid'];
		$photoObj->readdb($mydirname);
		/* delfile */
		unlink(XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/upimg/'.$photoObj->pid.$photoObj->ptype);
		unlink(XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/upimg/t_'.$photoObj->pid.$photoObj->ptype);
		$photoObj->deletedb($mydirname);
	}

	$diary->deletedb($mydirname);

	// delete tags
	$tag->uid=$diary->uid;
	$tag->bid=$diary->bid;
	$tag->delete_by_bid($mydirname);

	redirect_header( XOOPS_URL.'/modules/'.$mydirname.'/index.php?req_uid='.$uid,2,_MD_DIARY_DELETED);

	break;

}	// end switch $eparam['mode'] 


// STEP 4: process tags

	$pop_tags=array(); $perso_tags=array(); $_entry_tags=array(); $entry_tags=array();
	$func->get_taglist($req_uid, $diary->bid, $pop_tags, $person_tags, $_entry_tags);
	
	// read tags
	// use post_tags for preview
	$post_tags = $func->getpost_param('tags');
	if(!empty($post_tags)) {
		if (function_exists('mb_convert_kana')){
			preg_match_all("/\[(.+)\]/U", mb_convert_kana($post_tags, 'asKV'), $tags);
		} else {
			preg_match_all("/\[(.+)\]/U", $post_tags, $tags);
		}
		$arr_tags = array_unique($tags[1]);
		$i=0;
		foreach ($arr_tags as $_tag){
			$entry_tags[$i]['tag'] = htmlspecialchars($d3dConf->myts->stripslashesGPC($_tag), ENT_QUOTES) ;
			//$entry_tags[$i]['tag_urlenc'] = rawurlencode($_tag);
			$i++;
		}
	} else {
		$entry_tags = $_entry_tags;
	}
	
	// assign module header for tags
	$d3diary_header = '<link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=main_css" />'."\r\n";
	
	$d3diary_header .= '<script type="text/javascript" src="'.XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=loader&src=tag.js"></script>'."\r\n";
	if(!empty($_tempGperm['allow_ppermission'])){
		if(isset($_tempGperm['allow_ppermission'][$uid])){
			$d3diary_header .= '<script type="text/javascript" src="'.XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=loader&src=prototype,suggest,log.js"></script>'."\r\n";
		}
	}
	
	if($xoopsModuleConfig['body_htmleditor']=="common_fckeditor"){
		$d3diary_header .= '<script type="text/javascript" src="'.XOOPS_URL.'/common/fckeditor/fckeditor.js"></script>'."\r\n";
	}


$smilylist = d3diary_get_smilylist();

// menu
if($mod_config['menu_layout']==1){
	$yd_layout = "left";
}elseif($mod_config['menu_layout']==2){
	$yd_layout = "";
}else{
	$yd_layout = "right";
}

	$d3dConf->set_month ( $yd_data['year'], $yd_data['month'] );

// breadcrumbs
	$bc_para['diary_title'] = $d3dConf->module_name;
	$bc_para['path'] = "index.php";
	$bc_para['uname'] = $uname;
	$bc_para['name'] = (!empty($name)) ? $name : $uname ;
	$bc_para['mode'] = "edit";
	$bc_para['bid'] = $yd_data['bid'];
	$bc_para['title'] = $yd_data['title'];
	$bc_para['bc_name'] = constant('_MD_DIARY_EDIT');
	
	$breadcrumbs = $func->get_breadcrumbs( $req_uid, $bc_para['mode'], $bc_para );

// header ~ assign
$xoopsOption['template_main']= $mydirname.'_edit.html';
include XOOPS_ROOT_PATH."/header.php";
// this page uses smarty template
// this must be set before including main header.php
	$_temp_preview = $func->getpost_param('preview');
	if(!empty($_temp_preview) || $eparam['mode'] == _D3DIARY_PROCMODE_PHOTODEL){
		$xoopsTpl->assign("preview", "1");
	}

	$xoopsTpl->assign("yd_data", $yd_data);
	
	$xoopsTpl->assign(array(
			"yd_uid" => $req_uid,
			"yd_uname" => $uname,
			"yd_name" => $name,
			"yd_openarea" => intval($d3dConf->dcfg->openarea),
			"yd_layout" => $yd_layout,
			"yd_use_friend" => intval($mod_config['use_friend']),
			"yd_photo_maxsize" => intval($mod_config['photo_maxsize']),
			"yd_photo_maxpics" => intval($mod_config['photo_maxpics']),
			"yd_useresize" => intval($mod_config['photo_useresize']),
			"yd_data" => $yd_data,
			"yd_photo" => $yd_photo,
			"popTagArr" => $pop_tags,
			"myTagArr" => $person_tags,
			"bTagArr" => $entry_tags,
			"yd_counter" =>  $func->get_count_diary($diary->uid),
			"yd_use_open_entry" => intval($mod_config['use_open_entry']),
			"catopt" => d3diary_get_category_foredit($mydirname, $req_uid),
			"mydirname" => $mydirname,
			"xoops_breadcrumbs" => $breadcrumbs,
			"xoops_module_header" => 
				$xoopsTpl->get_template_vars( 'xoops_module_header' ).$d3diary_header,
			"mod_config" => $mod_config,
			"charset" => _CHARSET,
			"smilylist" => $smilylist,
			"allow_edit" => !empty($_tempGperm['allow_edit']) ? isset($_tempGperm['allow_edit'][$uid]) : false,
			"allow_html" => !empty($_tempGperm['allow_html']) ? isset($_tempGperm['allow_html'][$uid]) : false,
			"allow_regdate" => !empty($_tempGperm['allow_regdate']) ? isset($_tempGperm['allow_regdate'][$uid]) : false
			));
			
	if(!empty($_tempGperm['allow_gpermission']) && ( $_oe == 10 || $_oe == 20 ))
		{ $xoopsTpl->assign( 'allow_gpermission' , isset($_tempGperm['allow_gpermission'][$uid])); }
	if(!empty($_tempGperm['allow_ppermission']) && ( $_oe == 20 ))
		{ $xoopsTpl->assign( 'allow_ppermission' , isset($_tempGperm['allow_ppermission'][$uid])); }

include_once XOOPS_ROOT_PATH.'/footer.php';

// 
// private functions
//
function d3diary_get_category_foredit($mydirname, $uid){
	global $xoopsDB, $myts, $mod_config;
	
	// changed for common category (uid=0)
	$sql = "SELECT *
			  FROM ".$xoopsDB->prefix($mydirname.'_category')."
	          WHERE uid='".intval($uid)."' OR uid='0' ORDER BY corder";

	$result = $xoopsDB->query($sql);

	$cat_options = array();
	
	while ( $dbdat = $xoopsDB->fetchArray($result) ) {
		if($dbdat['blogtype'] != 100){
			$catopt['cid']   = $dbdat['cid'];
			$catopt['cname']   = htmlspecialchars($dbdat['cname'], ENT_QUOTES);
			if (intval($mod_config['use_open_cat'])>=1){
				if ($dbdat['subcat']==1) $catopt['cname'] = "&nbsp;--&nbsp;".$catopt['cname'] ;
				switch ((int)$dbdat['openarea']) {
					case 0: $catopt['cname'] .= " &nbsp;[".constant('_MD_CONF2_FOLLOW')."]"; break;
					case 1: $catopt['cname'] .= "  &nbsp;[".constant('_MD_CONF2_1OLOSE')."]"; break;
					case 2: $catopt['cname'] .= "  &nbsp;[".constant('_MD_CONF2_2FRIEND')."]"; break;
					case 3: $catopt['cname'] .= "  &nbsp;[".constant('_MD_CONF2_3FRIEND2')."]"; break;
					case 10: $catopt['cname'] .= "  &nbsp;[".constant('_MD_CONF2_10GROUP')."]"; break;
					case 20: $catopt['cname'] .= "  &nbsp;[".constant('_MD_CONF2_20PERSON')."]"; break;
					case 100: $catopt['cname'] .= " &nbsp; [".constant('_MD_CONF2_100HIDE')."]"; break;
					default:
				}
			}
			$catopt['corder']   = $dbdat['corder'];
			$catopt['openarea']   = $dbdat['openarea'];
			$catopt['blogtype']   = $dbdat['blogtype'];
			$catopt['dohtml']   = $dbdat['dohtml'];
			$cat_options[] = $catopt;
		}
	}
	return $cat_options;
}

function d3diary_readdb_photo($mydirname){
	global $diary, $photoObj, $xoopsDB, $myts, $func;
	global $form_photos, $del_pid;

	$photoObj->bids = array( intval($diary->bid) ) ;
	$photoObj->readdb_mul($mydirname) ;

	$rtn_photo = array() ;
	$i = 0 ;
	$_photos = !empty($photoObj->photos[$diary->bid]) ? $photoObj->photos[$diary->bid] : array() ;
	$num_photos = count($_photos) ;
	if ( 0 < $num_photos ) {
		foreach ( $_photos as $_photo) {
			if (!empty( $_photo['info'] )) {
				$_photo['pinfo']   = $myts->makeTboxData4Show( $_photo['info'] );
				$_photo['info']    = $func->stripPb_Tarea( $_photo['info'] );
			} elseif ( !empty($form_photos[$i]['info']) and ($form_photos[$i]['pid'] != $del_pid) ) {
				$_photo['pinfo']   = $myts->makeTboxData4Show( $form_photos[$i]['info'] );
				$_photo['info']    = $func->stripPb_Tarea( $form_photos[$i]['info'] );
			}
			if ( strstr( $diary->diary, $_photo['pname'] )) {
				$_photo['body_in'] = true;
			}
			$rtn_photo[] = $_photo;
			$i++;
		}
	}
	return array( $num_photos , $rtn_photo );
}

function d3diary_checkphoto($mydirname){
	global $diary, $xoopsOption, $mod_config, $eparam;
	
	$totalsize=0;

	for($i=0;$i<$eparam['postmax'];$i++){
		/* check filesize */
		$totalsize+=intval($_FILES['filename']['size'][$i]);
		if(!empty($_FILES['filename']['name'][$i])){
			if($_FILES['filename']['size'][$i] <=0){
			   return _MD_NOFILE.":photo(".$i.")";
			}
			/* check filetype */
			$ptype = strrchr($_FILES['filename']['name'][$i], ".");
			if(strcasecmp($ptype, ".png")!=0 and strcasecmp($ptype, ".jpg")!=0 and
			   strcasecmp($ptype, ".jpeg")!=0 and strcasecmp($ptype, ".gif")!=0){
			   return _MD_IVTYPE.":photo(".$i.")";
			}
			list($width, $height, $type, $attr) = getimagesize($_FILES['filename']['tmp_name'][$i]);
			if($type <= 0 or $type >=4){
				   return _MD_IVTYPE.":photo(".$i.")";
			}
		}
	}
	if($totalsize > ($mod_config['photo_maxsize']*1024)){
	   return _MD_SIZEOVER;
	}

}

function d3diary_prevphoto($mydirname){
	global $photoObj, $diary, $xoopsOption, $func, $myts, $eparam;
	global $yd_data, $form_photos, $del_pid, $psels, $pinfo ;

	$prevdir = $eparam['previewdir'] ;
	$updir = $eparam['uploaddir'].$prevdir;

	$photoObj->uid=$diary->uid;
	$photoObj->bid=$diary->bid;

	/* create dir */
//	$updir = XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/upimg/'.$diary->uid;
//	if(!is_dir($updir)){
//		if(!mkdir($updir)){
//		    redirect_header(XOOPS_URL.'/index.php',3,_MD_MKDIRERR);
//		}
//	}
	$prevphotos = array();
	$arr_prevphotos = array();

	// for re-preview photos uploaded by last preview
	if (!empty($form_photos) ){
		$i=0;
		while ($i < count($form_photos)){
			$trim_pname = str_replace($prevdir, "", $form_photos[$i]['pname']);
			$trim_pid = str_replace($prevdir, "", $form_photos[$i]['pid']);
			if ( ($trim_pname != $form_photos[$i]['pname'] ) and (strlen($trim_pid) !=  strlen($form_photos[$i]['pid']))
			    and  !in_array( $form_photos[$i]['pid'], $psels ) ){
				$prevphotos['prev']  = 1;
				$prevphotos['pid']   = $form_photos[$i]['pid'];
				$prevphotos['pname']   = $form_photos[$i]['pname'];
				$prevphotos['thumbnail']   = $prevdir."t_".$trim_pname;
				$prevphotos['info']  = $func->stripPb_Tarea( $myts->stripslashesGPC($form_photos[$i]['info']) );
				$prevphotos['pinfo']  = $myts->makeTboxData4Show( $myts->stripslashesGPC($form_photos[$i]['info']) );
				$arr_prevphotos[] = $prevphotos;
				$yd_data['photo_num']++;
			}
			$i++;
		}
	}

	// uploaded photos at this time
	list( $photo_num , $prev_photo ) = d3diary_process_uploaded_photo( $mydirname , $updir) ;

	foreach ( $prev_photo as $p_photo) {
		$p_photo['prev']   = 1 ;
		$p_photo['pid']   = $prevdir.$p_photo['pid'];
		$p_photo['pname']   = $prevdir.$p_photo['pname'];
		$p_photo['thumbnail']   = $prevdir.$p_photo['thumbnail'] ;
		$_tmp_pinfo = $p_photo['info'] ;
		$p_photo['info']  = $func->stripPb_Tarea( $_tmp_pinfo );
		$p_photo['pinfo']  = $myts->makeTboxData4Show( $_tmp_pinfo );
		$arr_prevphotos[] = $p_photo;
	}

	return array( $yd_data['photo_num'] + $photo_num , $arr_prevphotos ) ;
}

function d3diary_regphoto($mydirname, $prev=""){
	global $photoObj, $diary, $xoopsOption, $d3dConf, $eparam;
	global $yd_data, $form_photos, $pinfo, $psels ;

	$updir = $eparam['uploaddir'] ;
	$prevdir = $eparam['previewdir'] ;

	$photoObj->uid=$diary->uid;
	$photoObj->bid=$diary->bid;

	/* create dir */
//	$updir = XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/upimg/'.$diary->uid;
//	if(!is_dir($updir)){
//		if(!mkdir($updir)){
//		    redirect_header(XOOPS_URL.'/index.php',3,_MD_MKDIRERR);
//		}
//	}
	$prevphotos = array();
	$arr_prevphotos = array();
	if ($prev=="is_prev"){
		$updir = $updir.$prevdir;
	}

	// for re-preview photos uploaded by preview
	if (!empty($form_photos) && $eparam['was_prev']==1 ){
		$i=0;
		while ($i < count($form_photos)){

			$trim_pname = str_replace($prevdir, "", $form_photos[$i]['pname']);
			$trim_pid = str_replace($prevdir, "", $form_photos[$i]['pid']);
			if (( $trim_pname != $form_photos[$i]['pname'] ) and (strlen($trim_pid) !=  strlen($form_photos[$i]['pid']) )){
				$photoObj->pid   = $trim_pid;
				$photoObj->ptype = strrchr($trim_pname, ".");
				
				$f_from = $updir.$prevdir.$photoObj->pid.$photoObj->ptype;
				$f_to = $updir.$photoObj->pid.$photoObj->ptype;
				if (copy($f_from, $f_to)==true){
					unlink($f_from);
				} else {
					unlink($f_from);
					break;
				}
				
				$f_from = $updir.$prevdir.'t_'.$photoObj->pid.$photoObj->ptype;
				$f_to = $updir.'t_'.$photoObj->pid.$photoObj->ptype;
				if (copy($f_from, $f_to)==true){
					unlink($f_from);
				} else {
					unlink($f_from);
					break;
				}
				
				$photoObj->info  = $form_photos[$i]['info'];
				$photoObj->insertdb($mydirname);
			}
			$i++;
		}
	}

	list( $photo_num , $reg_photos ) = d3diary_process_uploaded_photo( $mydirname , $updir) ;

	foreach ( $reg_photos as $reg_photo ) {
		$photoObj->pid = $reg_photo['pid'] ;
		$photoObj->ptype = $reg_photo['ptype'] ;
		$photoObj->info =  $reg_photo['info'];
		$photoObj->insertdb($mydirname);
	}

	return array ( $photo_num , $reg_photos ) ;

}

function d3diary_swap_photoinfo (){
	global $yd_photo, $form_photos, $myts, $func ;

	$i=0;
	while ($i < count($form_photos)){
		$yd_photo[$i]['pinfo']	= $myts->makeTboxData4Show( $myts->stripslashesGPC($form_photos[$i]['info']) );
		$yd_photo[$i]['info'] 	= $func->stripPb_Tarea( $myts->stripslashesGPC($form_photos[$i]['info']) );
		$i++;
	}
}

function d3diary_update_photoinfo ( $mydirname ){
	global $photoObj, $form_photos ;

	$i=0;
	while ($i < count($form_photos)){
		$photoObj->pid   = $form_photos[$i]['pid'];
		$photoObj->readdb($mydirname);
		if ( $photoObj->info != $form_photos[$i]['info'] ){
			$photoObj->info  = $form_photos[$i]['info'];
			$photoObj->updatedb($mydirname);
		}
		$i++;
	}
}

// to process submit or preview uploaded photos
function d3diary_process_uploaded_photo( $mydirname , $updir){
	global $photoObj, $diary, $xoopsOption, $mod_config, $eparam;
	global $yd_data, $pinfo ;

	$photo_num = 0 ; $arr_prevphotos = array() ;

	for($i=0;$i<=$eparam['postmax'];$i++){
		if(!empty($_FILES['filename']['name'][$i])){
			/* set timestamp to pid */
			//$photoObj->pid   = 	md5(uniqid(rand(),1));
			$photoObj->pid   = substr("0".(string)$i, -2). md5(uniqid(rand(),1));
			$photoObj->ptype = strrchr($_FILES['filename']['name'][$i], ".");
			$photoObj->info = $pinfo[$i];
			if(strcasecmp($photoObj->ptype, ".png")!=0 and strcasecmp($photoObj->ptype, ".jpg")!=0 and
			   strcasecmp($photoObj->ptype, ".jpeg")!=0 and strcasecmp($photoObj->ptype, ".gif")!=0){
			   continue;
			}

			list($width, $height, $type, $attr) = getimagesize($_FILES['filename']['tmp_name'][$i]);

			/* move */
			$uploadfile = $updir.$photoObj->pid.$photoObj->ptype;
			if (!move_uploaded_file($_FILES['filename']['tmp_name'][$i], $uploadfile)) {
		//	    redirect_header( XOOPS_URL.'/modules/'.$mydirname.'/index.php',3,_MD_IVFILE);
			}

			/* create thumbnail*/
			$th_file = $updir.'t_'.$photoObj->pid.$photoObj->ptype;
			if($type == 1){
				$src_id = imagecreatefromgif($uploadfile);
			} elseif($type == 2){
				$src_id = imagecreatefromjpeg($uploadfile);
			} else {
				$src_id = imagecreatefrompng($uploadfile);
			}

			if($mod_config['photo_useresize']==1){
				// shrink large size data(640px)
				if($width>640 or $height>640){
					if($width >= $height){
						$th_width  = 640;
						$th_height = $height * $th_width / $width;
					} else {
						$th_height = 640;
						$th_width  = $width * $th_height / $height;
					}
					$dst_id = imagecreatetruecolor($th_width, $th_height);
					imagecopyresampled($dst_id, $src_id, 0, 0, 0, 0, $th_width, $th_height, $width, $height);
					if($type == 1){
						imagegif($dst_id, $uploadfile);
					} elseif($type == 2){
						imagejpeg($dst_id, $uploadfile);
					} else {
						imagepng($dst_id, $uploadfile);
					}
					imagedestroy($dst_id);
				}
			}

			// make sumbnail and save
			if($width>$eparam['th_size'] or $height>$eparam['th_size']){
				if($width >= $height){
					$th_width  = $eparam['th_size'];
					$th_height = $height * $th_width / $width;
				} else {
					$th_height = $eparam['th_size'];
					$th_width  = $width * $th_height / $height;
				}
			}else{
				$th_width  = $width;
				$th_height = $height;
			}
			
			$dst_id = imagecreatetruecolor($th_width, $th_height);
			imagecopyresampled($dst_id, $src_id, 0, 0, 0, 0, $th_width, $th_height, $width, $height);
			if($type == 1){
				imagegif($dst_id, $th_file);
			} elseif($type == 2){
				imagejpeg($dst_id, $th_file);
			} else {
				imagepng($dst_id, $th_file);
			}

			imagedestroy($src_id);
			imagedestroy($dst_id);
			
			$prevphotos['pid']   = $photoObj->pid;
			$prevphotos['ptype']   = $photoObj->ptype;
			$prevphotos['pname']   = $photoObj->pid.$photoObj->ptype;
			$prevphotos['thumbnail']   = "t_".$photoObj->pid.$photoObj->ptype;
			$prevphotos['info']  = $photoObj->info;
			$arr_prevphotos[] = $prevphotos;
			$photo_num++;
			
		}
	}
		return array( $photo_num, $arr_prevphotos) ;
}

function d3diary_showform($mydirname){
	global $photoObj, $diary, $xoopsOption, $func, $myts, $mPerm, $gPerm, $eparam, $d3diary_header ;
	global $yd_data, $del_pname, $form_photos, $_oe, $mod_config;

		$yd_data['bid'] = $diary->bid;
		$yd_data['uid'] = $diary->uid;

	// preview or photodel
	if( $eparam['is_prev']==1 ){
		$yd_data['cid']=$func->getpost_param('cid');
		if($yd_data['cid']>0){
			$category =& D3diaryCategory::getInstance();
			$category->uid = $diary->uid;
			$category->cid = intval($yd_data['cid']);
			$category->readdb($mydirname);
			$yd_data['cname'] = htmlSpecialChars($category->cname, ENT_QUOTES);
		}else{
			$yd_data['cname'] = htmlSpecialChars($myts->stripslashesGPC($func->getpost_param('cname')), ENT_QUOTES);
		}
		$yd_data['title'] = htmlSpecialChars($myts->stripslashesGPC($func->getpost_param('title')), ENT_QUOTES);
		$yd_data['openarea'] = $func->getpost_param('openarea');
		$yd_data['dohtml'] = $func->getpost_param('dohtml');
		$yd_data['update_ping'] = intval($func->getpost_param('update_ping'));

		$yd_data['diary'] = $func->stripPb_Tarea($myts->stripslashesGPC($func->getpost_param('diary')), $yd_data['dohtml']);
		$yd_data['diary4edit']= htmlSpecialChars($myts->stripslashesGPC($func->getpost_param('diary')), ENT_QUOTES);

	} else {
		$yd_data['cid'] = $diary->cid;
		$yd_data['title'] = htmlSpecialChars($diary->title, ENT_QUOTES);
		$yd_data['diary'] = $yd_data['diary4edit'] =htmlSpecialChars($diary->diary, ENT_QUOTES);
		$yd_data['openarea']=$diary->openarea;	// naao added
		$yd_data['dohtml'] = $diary->dohtml;
	}

	if ($func->getpost_param('reg_time')) {	//post by specified time
		$yd_data['reg_time']   = 1;
		d3diary_reg_time();
		$tmp_time = $yd_data['create_time_unformat'];
	} elseif(!empty($diary->create_time)){		//existing posted time
		$yd_data['create_time']   = $diary->create_time;
		$ctime = preg_split("/[-: ]/", $diary->create_time);
		$tmp_time = mktime($ctime[3],$ctime[4],$ctime[5],$ctime[1],$ctime[2],$ctime[0]);
		$yd_data['create_time_unformat'] = $tmp_time;
	} else {
		$tmp_time = time();
	}
		$week = intval($func->myformatTimestamp($tmp_time, "w"));
		$yd_data['year']   = $func->myformatTimestamp($tmp_time, "Y");
		$yd_data['month']   = intval($func->myformatTimestamp($tmp_time, "m"));
		$yd_data['day']   = intval($func->myformatTimestamp($tmp_time, "d"));
		$yd_data['time']   = $func->myformatTimestamp($tmp_time, "H:i");

		list( $arr_weeks, $arr_monthes, $arr_dclass, $arr_wclass ) = $func->initBoxArr();
		$yd_data['week'] = $arr_weeks [$week];
		$yd_data['b_month'] = $arr_monthes [$yd_data['month'] -1];
		$yd_data['dclass'] = $arr_dclass [$week];
		$yd_data['wclass'] = $arr_wclass [$week];

		$yd_data['group_list'] = array();
		$_oe = (int)$mod_config['use_open_entry'];
		if( $_oe == 10 || $_oe == 20 ) {
			$g_selcted = explode( "|", trim( $diary->vgids ,"|" ) );
			$group_list = array();
			foreach ( $gPerm->group_list as $_gid => $_name) {
		    	    if($_gid >= 4 && (in_array($_gid, $mPerm->mygids) || $mPerm->isadmin)){
				$group_list[$_gid]['gname'] = $_name;
				$group_list[$_gid]['gsel'] = (in_array( $_gid, $g_selcted )) ? 1 : 0;
			    }
			}
			$yd_data['group_list'] = $group_list;
		}

		if( $_oe == 20 ) {
			$p_selcted = array_map("intval", explode( "|", trim( $diary->vpids ,"|" )) );
			$pperm_list = implode( "," , $p_selcted ) ;
			$yd_data['pperm_list'] = $pperm_list;
			$unames = array(); $names = array();

			foreach ($p_selcted as $vpid) {
				if( $vpid >1 ) {
					$rtn = $func->get_xoopsuname($vpid);
					$uname = $rtn['uname'];
					$name = (!empty($rtn['name'])) ? $rtn['name'] : "" ;
					$unames[] = htmlspecialchars( $uname.'['.$vpid.'] ', ENT_QUOTES );
					$names[] = htmlspecialchars( $name.'['.$vpid.'] ', ENT_QUOTES );
				}
			}
			if( $mod_config['use_name'] == 1 ) {
				$yd_data['pperm_names'] = $names;
			} else {
				$yd_data['pperm_names'] = $unames;
			}
		}
}

function d3diary_get_smilylist(){
	global $xoopsDB;
	
	$sql = "SELECT *
		  FROM ".$xoopsDB->prefix('smiles')."
	          WHERE display='1'";
	$result = $xoopsDB->query($sql);
	$smilylist=_MD_SMILY_MENU;
	$smilylist.="&nbsp;[<a href='#moresmiley' onclick='javascript:openWithSelfMain(\"".XOOPS_URL."/misc.php?action=showpopups&amp;type=smilies&amp;target=diary\",\"smilies\",300,475);'>"._MD_MORE."</a>]<br />";
	$i=0;
	while ( $dbdat = $xoopsDB->fetchArray($result) ) {
		$smilylist.="<img onclick='xoopsCodeSmilie(\"diary\", \" ".$dbdat['code']." \");' onmouseover='style.cursor=\"hand\"' src='".XOOPS_URL."/uploads/".$dbdat['smile_url']."' alt='".$dbdat['emotion']."' />";
		$i++;
		if($i>=20){
			$i=0;
			$smilylist.="<br />";
		}
	}
	return $smilylist;
}

function d3diary_regtags($mydirname){
	global $xoopsDB, $tag, $diary, $func ;

	$post_tags = $func->getpost_param('tags');

	if(!empty($post_tags)) {
		if (function_exists('mb_convert_kana')){
			preg_match_all("/\[(.+)\]/U", mb_convert_kana($post_tags, 'asKV'), $tags);
		} else {
			preg_match_all("/\[(.+)\]/U", $post_tags, $tags);
		}
		$arr_tags = array_unique($tags[1]);
	} else {
		$arr_tags = array();
	}

		$sql = "SELECT *
			  FROM ".$xoopsDB->prefix($mydirname.'_tag')."
		          WHERE bid='".intval($diary->bid)."'";

		$result = $xoopsDB->query($sql);

		$db_tags = array();
		while ( $dbdat = $xoopsDB->fetchArray($result) ) {
			$db_tags[] = $dbdat['tag_name'];
			if(!in_array($dbdat['tag_name'],$arr_tags)){
				// delete the tag
				$tag->tag_id = $dbdat['tag_id'];
				$tag->deletedb($mydirname);
			}
		}

		foreach ($arr_tags as $post_tag) {
			if(!in_array($post_tag, $db_tags)){
				//insert db
				$tag->bid = intval($diary->bid);
				$tag->uid = intval($diary->uid);
				$tag->tag_name = $post_tag;
				$tag->insertdb($mydirname);
			}
		}
}

function d3diary_ping_send($blogtitle, $blogtopurl) {
	global $mod_config;
	
	if (!empty($mod_config['updateping_url'])) {
		$arr_ping_servers = explode("\n", $mod_config['updateping_url']);
		$arr_ping_servers = array_map("trim" ,$arr_ping_servers);
		$arr_ping_servers2 = array_map("parse_url" ,$arr_ping_servers);
		$ping = array();
		
		foreach ($arr_ping_servers2 as $ping_server){
			$ping[$ping_server['host']] = $ping_server['path'] ;
		}
	
		$param = array(
			new XML_RPC_Value($blogtitle, 'string'),
			new XML_RPC_Value($blogtopurl, 'string')
		);
	
		$msg=new XML_RPC_Message('weblogUpdates.ping', $param);
	
		foreach($ping as $pingServer => $pingPath) {
			$client = new XML_RPC_Client($pingPath, $pingServer, 80);
			$response = $client->send($msg);
			if(!$response) {
				echo "timeout : ".$pingServer."<br />\n";
			}elseif($response->faultCode()) {
				echo "failed : ".$pingServer."<br />\n";
			}else {
				echo "success!! : ".$pingServer."<br />\n";
			}
		}
	}
}

function d3diary_reg_time(){
	global $diary, $yd_data, $func;
	if ($func->getpost_param('reg_time')) {
		$pub = $func->getpost_param('published');
		$pub = array_map('intval', $pub);
		$tmp_time = mktime($pub['Time_Hour'],$pub['Time_Minute'],0,$pub['Date_Month'],$pub['Date_Day'],$pub['Date_Year']);
		$yd_data['create_time_unformat'] = $tmp_time;
		$yd_data['create_time'] = date("Y-m-d H:i:s", $tmp_time);
		$diary->create_time = $yd_data['create_time'];
	}
}

?>
