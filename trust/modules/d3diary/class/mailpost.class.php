<?php
   /* wp-shot.php
    *
    * Copyright (c) 2003-2004 The Wordpress Team
    *
    * Copyright (c) 2004 - John B. Hewitt - jb@stcpl.com.au
    * Copyright (c) 2004 - Dan Cech - dcech@lansmash.com
    * Copyright (c) 2006 - Santaro Otsukare - staybymyside [at] gmail.com
    *
    * Licensed under the GNU GPL. For full terms see the file COPYING.
    *
    * modified by naao 2011-04
    */

if( ! class_exists( 'D3diaryMailPost' ) ) {

class D3diaryMailPost {

	var $d3dConf ;
	var $pop3 ;
	var $myts ;
	var $mydirname ;
	var $mid ;
	var $mod_config ;
	var $dcfg ;
	var $uid ;
	var $req_uid ;
	var $email ;	// user profile's email address
	var $structure;
	var $mails ;
	var $got_mails ;
	var $body ;
	var $dohtml ;
	var $photos ;
	var $photo_parts ;
	var $server_url ;
	var $server_port ;
	var $apop ;
	var $account ;
	var $password ;
	var $_scc_msg = '' ;
	var $_err_msg = '' ;

function D3diaryMailPost( & $d3dConf ){

	$this->d3dConf = & $d3dConf;
}

function ini_set()
{	//must be set $this->mydirname, $req_uid before call it

	// copying parent's parameters
	$this->mydirname = & $this->d3dConf->mydirname;
	$this->mid = & $this->d3dConf->mid;

	$this->uid = & $this->d3dConf->uid;
	$this->req_uid = & $this->d3dConf->req_uid;
	$this->mod_config = & $this->d3dConf->mod_config;
	$this->dcfg = & $this->d3dConf->dcfg;
	
	$this->myts =& $this->d3dConf->myts;

	// read class
	require_once dirname( dirname(__FILE__) ).'/include/post_pop3.php';
	require_once dirname( dirname(__FILE__) ).'/include/mimeDecode.php';

	//retrieve mail
	$this->pop3 = & new D3diary_POP3();

	// get pop3 prefferences
	$this->server_url  = $this->mod_config['pop3_server'];
	$this->server_port =  (int)$this->mod_config['pop3_port'];
	$this->apop =  $this->mod_config['pop3_apop'];
	$this->account =  $this->mod_config['post_email_address'];
	$this->password =  $this->mod_config['post_email_password'];

	/*
	$this->uptime = $this->dcfg->uptime;
	$this->updated = $this->dcfg->updated;
	*/

}

///******** FUNCTIONS **********/
//

function check_settings( ) {
	if ( 1 <= (int)$this->dcfg->mailpost ) {
		if ( !empty($this->server_url) && 0 < $this->server_port && !empty($this->account) && !empty($this->password)) {
			return true;
		}
	}
	return false;
}

function connect() {
	return $this->pop3->connect($this->server_url, $this->server_port);
}

function quit() {
	return $this->pop3->quit();
}

function login() {

	if ( (int)$this->apop ==1 ) {
		$count =  $this->pop3->apop($this->account, $this->password);
	} else {
		$count =  $this->pop3->login($this->account, $this->password);
	}
	
	$this->_err_msg .= $this->pop3->ERROR." <br />\n";

	return $count;
}

function get_list( ) {

	$count =  $this->login();
	
	$max = 100;
	$start = ($count > $max) ? $count-$max : 0 ; 
	//var_dump($count);

if (0 == $count) {
//	print "There doesn't seem to be any new mail.";
	return false;
} else {
	for ($i=$count; $i > $start; $i--) {
		//variables
		$content_type = '';
		$boundary = '';
		$bodysignal = 0;

		// added override "mb_detect_order"
		$chr_order = !empty($this->mod_config['post_detect_order']) ? $this->mod_config['post_detect_order'] : 'auto';
		$mb_rtn = mb_detect_order($chr_order);
		if ( $mb_rtn != true ) {
			$this->_err_msg.= "Error: INVALID DETECT ORDER '".$chr_order."'<br />\n";
			mb_detect_order('auto');
		}

		$input = implode ('',$this->pop3->get($i));

		// decode the mime
		$params['include_bodies'] = true;
		$params['decode_bodies'] = true;
		$params['decode_headers'] = true;
		$params['input'] = $input;
		$structure = D3diary_Mail_mimeDecode::decode($params);
		
		// read and check "from" address
		$from = $this->yn_read_address($structure);
		if (! $from) {
			$this->_err_msg.= "Error: No sender address found at message #$i.<br />\n";
			continue;	// jump through
		}
		
		// validate "from" address
			// user profile's email address
			// must get email of $this->req_uid not for $xoopsUser
			$ret = $this->d3dConf->func->get_xoopsuname($this->req_uid) ;
			$this->email = $ret['email'];

		if ( strcmp($from,$this->dcfg->address)!=0 &&  strcmp($from,$this->email)!=0 ) {
			continue;	// jump through
		}

		// pickup subject
		$subject = trim($structure->headers['subject']);
		//$subject = mb_convert_encoding($subject, _CHARSET, "auto");
		$subject = mb_convert_encoding($subject, _CHARSET,  mb_detect_encoding($subject));
		$title = $this->myts -> makeTboxData4Show($subject);
		if (empty($title)) {
			$title= _MD_DIARY_NO_TITLE ;
		}

		//date reformating
		$post_time_gmt = strtotime(trim($structure->headers['date']));
		if (! $post_time_gmt) {
			$this->_err_msg.= "Error: There is no Date: field at message #$i.<br />\n";
			continue;
		}

		$post_date = gmdate('Y-m-d H:i:s', $post_time_gmt + ( $this->d3dConf->server_TZ * 3600));
		$create_time = $this->myts -> makeTboxData4Show($post_date);
		$post_date_gmt = gmdate('Y-m-d H:i:s', $post_time_gmt);
		$post_date_gmt = $this->myts -> makeTboxData4Show($post_date_gmt);
		
		$this->mails[$i]['id'] = $i;
		$this->mails[$i]['title'] = $title;
		$this->mails[$i]['create_time'] = $create_time;
		$this->mails[$i]['post_date'] = $post_date;
		$this->mails[$i]['post_date_gmt'] = $post_date_gmt;
		
		$this->structure[$i] = $structure;
		
	} // end looping over messages
	return true;
}

}
function regist_list( &$diary, &$_photo, &$_tag, $params ) {
	//$params['chk_mails'], $params['chk_time'], $params['cid'], $params['openarea'];

	global $xoopsDB;

	// get mail title, createdtime as $this->mails
	//$ret = $this->get_list( );
	//if ($ret == false) { return false; }

	$count = count($this->mails);

    if (0 < $count) {

	for ($j=0; $j<count($params['chk_mails']); $j++) {
		$i = $params['chk_mails'][$j];
		$title = $this->mails[$i]['title'];
		$update_time = $create_time = $this->mails[$i]['create_time'];
		$post_date = $this->mails[$i]['post_date'];
		$post_date_gmt = $this->mails[$i]['post_date_gmt'];

		if ( $create_time == $params['chk_time'][$i] ) {
			$this->photo_parts = array();
		
			$this->got_mails[$i] = $this->mails[$i];
			$diary->title = $title;
			
			$diary->create_time = !empty($params['reg_time']) ? $params['reg_time'] : $create_time;
			$diary->uid = $this->req_uid;
			$diary->cid = !empty($params['cid']) ? $params['cid'] : 0 ;
			$diary->openarea = !empty($params['openarea']) ? $params['openarea'] : 0 ;
			
			// body and dohtml are set in got_content()
			$this->get_content($this->structure[$i],$create_time);

			$diary->dohtml = $this->dohtml;
			if ($diary->dohtml == 1) {
				$diary->diary = $this->d3dConf->func->htmlPurifier($this->body);
			} else {
				$diary->diary = $this->body;
			}
			
			$diary->vgids = !empty($params['chk_vgids']) ? $params['chk_vgids'] : "" ;
			$diary->vpids = !empty($params['chk_vpids']) ? $params['chk_vpids'] : "" ;
			
			// insert entry to DB
			//var_dump($diary);
			$diary->bid = $diary->insertdb( $this->mydirname, $params['f_query'] );
			$this->got_mails[$i]['bid'] = $diary->bid;
			
			//var_dump($diary->bid);
			
			// write photo files
			$this->write_photos($diary->bid);
			
			// insert photos to DB
			if( count($this->photos) > 0 ) {
				$_photo->bid = $diary->bid;
				$_photo->uid = $diary->uid;
				$_photo->info = "";
				foreach ($this->photos as $post_p) {
					$_photo->pid = $post_p['pid'];
					$_photo->ptype = $post_p['ptype'];
					$_photo->insertdb( $this->mydirname, $params['f_query'] );
				}
			}
			$this->photos = array();	// clear
			
			// insert tags to DB
			$post_tags = !empty($params['post_tags']) ? $params['post_tags'] : Array() ;
			$entry_tags = array();
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
			$i=0;
			foreach ($arr_tags as $t_tag){
				$entry_tags[] = htmlspecialchars($this->myts->stripslashesGPC($t_tag), ENT_QUOTES) ;
				$i++;
			}
			foreach ($entry_tags as $post_tag) {
				//insert db
				$_tag->bid = intval($diary->bid);
				$_tag->uid = intval($diary->uid);
				$_tag->tag_name = $post_tag;
				$_tag->insertdb($this->mydirname);
			}

			$this->_scc_msg .=  $title."&nbsp;:&nbsp;"._MD_MAIL_REGISTED."<br /><hr />";
		} else {
			$this->_err_msg .= "Error:message is not found. Please retry at step1<br /><hr />";
		}
		
	} // end looping over messages
	return true;
    }
	return false;
}

function get_content($part,$create_time) {
	global $xoopsDB;
	$myts =& $this->d3dConf->myts;
	switch (strtolower($part->ctype_primary))	{
		case 'multipart':
			$meta_return = '';
			foreach ($part->parts as $section) {
				$meta_return = $this->get_content($section,$create_time).$meta_return;
			}
			break;

		case 'text':
			$body_content = $part->body;
			$body_base_code = mb_detect_encoding($body_content);
			$body_content = mb_convert_encoding($body_content, _CHARSET, $body_base_code);
			if (strtolower($part->ctype_secondary=='enriched') || strtolower($part->ctype_secondary=='html')) {
				$this->dohtml = 1;
			} else {
				$this->dohtml = 0;
			}
			$meta_return = $body_content."\n";
			$this->body = $body_content;
			break;

		case 'image':
			$this->photo_parts[] = $part;
			break;
	}
}

function del_list( $params ) {
	//$params['chk_mails'], $params['chk_time'], $params['cid'], $params['openarea'], $params['keep'];

	if ( $params['keep'] != 1 ) {

		for ($j=0; $j<count($params['chk_mails']); $j++) {
			$i = $params['chk_mails'][$j];
			$title = $this->mails[$i]['title'];
			$ret = $this->pop3->delete($i);
			if ( $ret==true ) {
				$this->_scc_msg .=  $title."&nbsp;:&nbsp;"._MD_MAIL_DELETED."<br /><hr />";
			} else {
				$this->_err_msg .=  $title."&nbsp;:&nbsp;".$this->pop3->ERROR."<br /><hr />";
			}
		}
	}
	return $ret;
}

function write_photos( $bid ) {

	$photosdir = XOOPS_ROOT_PATH.'/modules/'.$this->mydirname.'/upimg/';

	foreach ($this->photo_parts as $part) {
			$photo = array();
			$photo['pid'] = $this->req_uid.'_'.$bid.'_'.substr("0".(string)count($this->photos), -2).'_'.rand();
			$photo['ptype'] = '.' . $part->ctype_secondary;
			$filename = $photosdir . $photo['pid'] . $photo['ptype'];
			$fp = fopen($filename, 'w');
			fwrite($fp, $part->body);
			fclose($fp);

			$this->photos[] = $photo;

		// start legacy function
			if(strcasecmp($photo['ptype'], ".png")!=0 and strcasecmp($photo['ptype'], ".jpg")!=0 and
			   strcasecmp($photo['ptype'], ".jpeg")!=0 and strcasecmp($photo['ptype'], ".gif")!=0){
			   continue;
			}

			list($width, $height, $type, $attr) = getimagesize($filename);
			//$this->_err_msg .= $width."-".$height."-".$type."-".$attr;

			// create thumbnail
			$th_file = $photosdir.'t_'.$photo['pid'] . $photo['ptype'];
			if($type == 1){
				$src_id = imagecreatefromgif($filename);
			} elseif($type == 2){
				$src_id = imagecreatefromjpeg($filename);
			} else {
				$src_id = imagecreatefrompng($filename);
			}

			if($this->d3dConf->mod_config['photo_useresize']==1){
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
						imagegif($dst_id, $filename);
					} elseif($type == 2){
						imagejpeg($dst_id, $filename);
					} else {
						imagepng($dst_id, $filename);
					}
					imagedestroy($dst_id);
				}
			}

			// make sumbnail and save
			$_thsize = $this->d3dConf->mod_config['photo_thumbsize'];
			if($width>$_thsize or $height>$_thsize){
				if($width >= $height){
					$th_width  = $_thsize;
					$th_height = $height * $th_width / $width;
				} else {
					$th_height = $_thsize;
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
	}
}

// ==================================================
function yn_rfc2822_mail_address($addr) {
	$addresses = array();
	$quoted    = array();
	// ----- save quoted text -----
	preg_match('/(^|[^\\\\])("([^\\\\"]|\\\\.)*")/', $addr, $m);
	if (!empty($m[2])) {
		$addr = preg_replace("/(^|[^\\\\])$m[2]/", "$1\376\376\376" . count($quoted) . "\376\376\376", $addr, 1);
		$quoted[] = $m[2];
	}

	// ---- remove comments -----
	$addr = preg_replace('/\([^)]*[^\\\\]\)/', '', $addr);
	// ----- remove group name -----
	$addr = preg_replace('/[-\w ]+:([^;]*);/', '$1', $addr);
	// ----- split into each address -----
	foreach (explode(',', $addr) as $a) {
		$a = str_replace(' ', '', $a);
		preg_match('/<([^>]*)>/', $a, $m);
		if (!empty($m[1])) {
			$a = $m[1];
		}
		// ----- restore quoted text -----
		$a = preg_replace('/\376\376\376(\d+)\376\376\376/e', '$quoted[$1]', $a);
		// ----- got address -----
		if ($a) {
			$addresses[] = $a;
		}
	}
	return $addresses;
}

// ==================================================
function yn_read_address($structure) {
	$senders = $this->yn_rfc2822_mail_address(trim($structure->headers['from']));
	$sender = $senders[0];
	if (! $sender) {
		$senders = $this->yn_rfc2822_mail_address($structure->headers['return-path']);
		$sender = $senders[0];
	}
	return $sender;
}

}
}
?>