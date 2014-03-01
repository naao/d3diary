<?php

function b_d3dside_person_show( $options ){

	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_entry = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$this_template = empty( $options[3] ) ? 'db:'.$mydirname.'_block_side_person.html' : trim( $options[3] ) ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	require_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';
	
	$d3dConf = D3diaryConf::getInstance($mydirname, 0, "b_side_person");
	$func =& $d3dConf->func ;
	$mPerm =& $d3dConf->mPerm ;
	$gPerm =& $d3dConf->gPerm ;
	$mod_config =& $d3dConf->mod_config ;

	$uid = $d3dConf->uid;
	$req_uid = $d3dConf->req_uid; // overrided by d3dConf
	//if( $req_uid > 0 || $uid > 0 ) {
	if ( $mPerm->isadmin && 0 < $req_uid ) {
		$query_req_uid = "&amp;req_uid=".$req_uid;
	} else {
		$query_req_uid = "";
	}

	if( $req_uid > 0 ) {
		$yd_avaterurl = $func->get_user_avatar( array( $req_uid ) ) ;
	
		$ret = $func->get_xoopsuname($req_uid) ;
		$yd_uname = $ret['uname'];
		$yd_name = $ret['name'];

		// check mailpost permission for access user's group
		$_tempGperm = $gPerm->getUidsByName( array('allow_mailpost') );
		$allow_mailpost = 0;
		if( $mod_config['use_mailpost']==1 && !empty($_tempGperm['allow_mailpost'])){
			if(isset($_tempGperm['allow_mailpost'][$req_uid])) {
				$allow_mailpost = 1;
			}
		}	unset($_tempGperm);

		$lang = array();
		$lang['person'] = constant('_MD_DIARY_PERSON');
		$lang['avatar'] = constant('_MD_AVATAR');
		$lang['edit'] = constant('_MD_T_EDIT');
		$lang['ucfg'] = constant('_MD_T_TOTAL');
		$lang['cat'] = constant('_MD_CATEGORY');
		$lang['write'] = constant('_MD_NEWENTRY');
		$lang['mailpost'] = constant('_MD_MAIL_OPENMANUAL');

		$block="";

		$block['req_uid'] = $req_uid ;
		$block['query_req_uid'] = $query_req_uid ;
		$block['yd_avaterurl'] = $yd_avaterurl[$req_uid] ;
		$block['yd_uid'] = $req_uid;
		$block['yd_uname'] = $yd_uname;
		$block['yd_name'] = $yd_name;
		$block['yd_cid'] = (int)$func->getpost_param('cid');
		$block['yd_counter'] = $func->get_count_diary($req_uid);
		$block['yd_editperm'] = ($mPerm->isauthor || $mPerm->isadmin) ? 1 : 0 ;
		$block['yd_owner'] = $mPerm->isauthor ? 1 : 0 ;
		$block['yd_mailpost'] = $allow_mailpost;
		$block['lang'] = $lang;
		$block['mydirname'] = $mydirname;
		$block['mod_config'] = $mod_config ;
	
		$d3dConf->debug_appendtime('b_side_person');

		if( empty( $options['disable_renderer'] ) ) {
			require_once XOOPS_ROOT_PATH.'/class/template.php' ;
			$tpl = new XoopsTpl() ;
			$tpl->assign( 'block' , $block ) ;
			$ret['content'] = $tpl->fetch( $this_template ) ;
			return $ret ;
		} else {
			return $block ;
		}
	}
}

function b_d3dside_person_edit( $options )
{
	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	require_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';
	$d3dConf = D3diaryConf::getInstance($mydirname, 0, "b_side_person");
	$func =& $d3dConf->func ;

	$max_entry = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$this_template = empty( $options[3] ) ? 'db:'.$mydirname.'_block_side_person.html' : trim( $options[3] ) ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
	$orders = array(
		'time' => _MB_D3DIARY_ORDERTIMED ,
		'posted' => _MB_D3DIARY_ORDERPOSTED ,
	) ;
	
	$order_options = '' ;
	foreach( $orders as $order_value => $order_name ) {
		$selected = $order_value == $now_order ? "selected='selected'" : "" ;
		$order_options .= "<option value='$order_value' $selected>$order_name</option>\n" ;
	}

	$form = "
		<input type='hidden' name='options[0]' value='$mydirname' />
		<label for='o1'>" . sprintf( _MB_D3DIARY_DISPLAY , "</label><input type='text' size='4' name='options[1]' id='o1' value='$max_entry' style='text-align:right;' />" ) . "
		<br />
		<label for='orderrule'>"._MB_D3DIARY_ORDERRULE."</label>&nbsp;:
		<select name='options[2]' id='orderrule'>
			$order_options
		</select>
		<br />
		<label for='this_template'>"._MB_D3DIARY_THISTEMPLATE."</label>&nbsp;:
		<input type='text' size='60' name='options[3]' id='this_template' value='".$func->htmlspecialchars($this_template)."' />
		<br />
	\n" ;

	return $form;
}
?>