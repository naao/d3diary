<?php
include_once dirname( dirname(__FILE__) ).'/class/diary.class.php';
include_once dirname( dirname(__FILE__) ).'/class/photo.class.php';
include_once dirname( dirname(__FILE__) ).'/class/tag.class.php';
include_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';

$myname = "mailpost.php";

$diary =& Diary::getInstance();
$photo =& Photo::getInstance();
$tag =& Tag::getInstance();
$d3dConf =& D3diaryConf::getInstance($mydirname, 0, "mailpost");

$uid = $d3dConf->uid;
// overrides $d3dConf->req_uid
$d3dConf->req_uid = $req_uid = isset($_GET['req_uid']) ? (int)$_GET['req_uid'] : $uid;
$mPerm =& $d3dConf->mPerm;
$mPerm->ini_set();

// overrides dcfg->uid
$dcfg =& $d3dConf->dcfg;
$dcfg->uid = $req_uid;
$dcfg->readdb($mydirname);

// overrides mPost->req_uid
$mPost = & $d3dConf->mPost;
$mPost->ini_set();

$myts =& $d3dConf->myts;

// post step
$mpost_step = $d3dConf->func->getpost_param('mpost_step');
$mpost_step = !empty($mpost_step) ? (int)$mpost_step : 0;

// checked mail-ids for 2nd-step
$chk_mails= $d3dConf->func->getpost_param('chk_mails');
$chk_time= $d3dConf->func->getpost_param('chk_time');

	// get permission unames for each groupPermission
	// check edit permission by group
	$_tempGperm = $d3dConf->gPerm->getUidsByName( array_keys($d3dConf->gPerm->gperm_config) );
	if(!empty($_tempGperm['allow_edit']) && $d3dConf->mod_config['use_mailpost']==1 && !empty($_tempGperm['allow_mailpost'])){
		if(!in_array($uid, $_tempGperm['allow_edit'])) {
			redirect_header(XOOPS_URL.'/index.php',2,_MD_NOPERM_EDIT);
			exit();
		}
		// check mailpost permission by group
		if(!in_array($uid, $_tempGperm['allow_mailpost'])) {
			redirect_header(XOOPS_URL.'/index.php',2,_MD_NOPERM_MAILPOST);
			exit();
		}
	} else {
		redirect_header(XOOPS_URL.'/index.php',2,_MD_NOPERM_MAILPOST);
		exit();
	}

	$reg_time = '';
	$allow_regdate = !empty($_tempGperm['allow_regdate']) ? in_array($uid, $_tempGperm['allow_regdate']) : false;
	//$mPost->_err_msg .= $d3dConf->func->getpost_param('reg_time');
	if ($allow_regdate == true && $d3dConf->func->getpost_param('reg_time')) {
		$reg_time = d3diary_reg_time($d3dConf->func->getpost_param('published'));
	}

// check setting
if ( $mpost_step >= 1 ) {

	// check mailpost manual use setting
	if ( $dcfg->mailpost<1 || $dcfg->mailpost>2 ) {
		redirect_header(XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=usr_config',2,_MD_NOSET_MAILMANUAL);
		exit();
	}

	if ( $mPost->check_settings() == true ) {
		// connect to server
		$connected = $mPost->connect();
	}
}

// define Template
$xoopsOption['template_main']= $mydirname.'_mailpost.html';

require_once XOOPS_ROOT_PATH.'/header.php';

	if ( $mpost_step > 0 && !$connected ) {
			$mPost->_err_msg .= _MD_NOSET_MAILPOST.$mPost->pop3->ERROR." <br />\n";
	} else {
		if ($mpost_step==1) {
			$ret = $mPost->get_list();
			if ( $ret != true ) {
				$mPost->_err_msg .= _MD_NO_NEWMAIL." <br />\n";
			}
			$ret = $mPost->quit();
		} elseif ($mpost_step==2) {
		
			$ret = $mPost->get_list();
			if ( $ret != true ) {
				$mPost->_err_msg .= _MD_NO_NEWMAIL." <br />\n";
			}
		    if ( empty($chk_mails) ) {
				$mPost->_err_msg .= _MD_NOCHECKED_MAIL." <br />\n";
		    } else {
		    	$mail_keep = (int)$d3dConf->func->getpost_param('keep');
				// set parameters
				$params['chk_mails'] = $chk_mails;	// checked for registering mails
				$params['chk_time'] = $chk_time;	// for validate
				$params['f_query'] = false;
		    	if ( $mail_keep < 2 ) {
				$params['reg_time'] = $reg_time;
				$params['cid'] = (int)$d3dConf->func->getpost_param('cid');
				$params['openarea'] = (int)$d3dConf->func->getpost_param('openarea');
					$chk_vgids= $d3dConf->func->getpost_param('vgids');		
				$params['chk_vgids'] = $chk_vgids ? "|".implode("|", array_map("intval" ,$chk_vgids))."|" : "";
					$chk_vpids= $d3dConf->func->getpost_param('vpids');		
				$params['chk_vpids'] = $chk_vpids ? "|".implode("|", array_map("intval" ,explode("," , $chk_vpids)))."|" : "";
				$params['post_tags'] = $d3dConf->func->getpost_param('tags');

				$ret = $mPost->regist_list( &$diary, &$photo, &$tag, $params );
			} else { $ret == true ;}

			if ( $ret == true ) {
				$params['keep'] = $mail_keep;
				$ret = $mPost->del_list( $params );
			}
		    }
		}

		// close the connection
		if ($connected) {
			$ret = $mPost->quit();
		}

		// update diaryconfig "updated" time for auto register
		$dcfg->updated = time();
		$dcfg->updatedb($mydirname, true);

	}

		$yd_data['group_list'] = array();
		$_oe = (int)$d3dConf->mod_config['use_open_entry'];
		if( $_oe == 10 || $_oe == 20 ) {
			foreach ( $d3dConf->gPerm->group_list as $_gid => $_name) {
		    	    if($_gid >= 4 && (in_array($_gid, $d3dConf->mPerm->mygids) || $d3dConf->mPerm->isadmin)){
				$group_list[$_gid]['gname'] = $_name;
			    }
			}
			$yd_data['group_list'] = $group_list;
		}

	$xoopsTpl->assign(array(
		"mpost_step" => $mpost_step+1,
		"mails" => $mPost->mails,
		"got_mails" => $mPost->got_mails,
		"yd_data" => $yd_data,
		"yd_cfg" => $d3dConf->dcfg,
		"catopt" => $d3dConf->func->get_categories($uid, $uid),
		"yd_use_open_entry" => intval($d3dConf->mod_config['use_open_entry']),
		"mydirname" => $mydirname,
		"mod_config" => $d3dConf->mod_config,
		"scc_msg" => $mPost->_scc_msg,
		"err_msg" => $mPost->_err_msg,
		"allow_edit" => !empty($_tempGperm['allow_edit']) ? in_array($uid, $_tempGperm['allow_edit']) : array(),
		"allow_html" => !empty($_tempGperm['allow_html']) ? in_array($uid, $_tempGperm['allow_html']) : array(),
		"allow_regdate" => $allow_regdate
		));
			
	if(!empty($_tempGperm['allow_gpermission']) && ( $_oe == 10 || $_oe == 20 ))
		{ $xoopsTpl->assign( 'allow_gpermission' , in_array($uid,$_tempGperm['allow_gpermission'])); }
	if(!empty($_tempGperm['allow_ppermission']) && ( $_oe == 20 ))
		{ $xoopsTpl->assign( 'allow_ppermission' , in_array($uid,$_tempGperm['allow_ppermission'])); }

include_once XOOPS_ROOT_PATH.'/footer.php';

function d3diary_reg_time($published){
		$pub = array_map('intval', $published);
		$tmp_time = mktime($pub['Time_Hour'],$pub['Time_Minute'],0,$pub['Date_Month'],$pub['Date_Day'],$pub['Date_Year']);
		return date("Y-m-d H:i:s", $tmp_time);
}

?>
