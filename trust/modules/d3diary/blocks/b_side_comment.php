<?php

function b_d3dside_comment_show( $options ){

	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_entry = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$this_template = empty( $options[3] ) ? 'db:'.$mydirname.'_block_side_comment.html' : trim( $options[3] ) ;
	$limit_self = empty( $options[4] ) ? 0 : (int)$options[4] ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	require_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';
	
	$d3dConf = D3diaryConf::getInstance($mydirname, 0, "b_side_comment");
	$func =& $d3dConf->func ;
	$mod_config =& $d3dConf->mod_config ;
	$uid = $d3dConf->uid;
	$req_uid = $d3dConf->req_uid; // overrided by d3dConf
	
	if( $limit_self == 1 ) {				// always show diarylist page
		$req_uid = 0 ;
	} elseif( $limit_self == 2 && $req_uid > 0 ) {		// not show for personal page
		return ;
	} elseif( $limit_self == 3 && $req_uid == 0 ) {		// not show except for personal page
		return ;
	}

		list( $yd_comment, $yd_com_key ) =  $func->get_commentlist ($req_uid,$uid,$max_entry,false);

		$lang = array();
		$constpref = "_MB_" . strtoupper( $mydirname ) ;
		$lang['more'] = constant($constpref.'_COMMENT_LIST');

		$block="";

		$block['yd_comment'] = $yd_comment;
		$block['yd_com_key'] = $yd_com_key;
		$block['yd_uid'] = $req_uid;
		$block['lang'] = $lang;
		$block['mydirname'] = $mydirname;
		$block['mod_config'] = $mod_config ;
	
	$d3dConf->debug_appendtime('b_side_comment');

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

function b_d3dside_comment_edit( $options )
{
	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_entry = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$this_template = empty( $options[3] ) ? 'db:'.$mydirname.'_block_side_comment.html' : trim( $options[3] ) ;
	$limit_self = empty( $options[4] ) ? 0 : (int)$options[4] ;

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

	$limitselfs = array(
		0 => _MB_D3DIARY_LIMITSELF_NO ,
		1 => _MB_D3DIARY_LIMITSELF_ALL ,
		2 => _MB_D3DIARY_LIMITSELF_OTHER ,
		3 => _MB_D3DIARY_LIMITSELF_PERSON ,
	) ;
	
	$limitself_options = '' ;
	foreach( $limitselfs as $limitself_value => $limitself_name ) {
		$selected = $limitself_value == $limit_self ? "selected='selected'" : "" ;
		$limitself_options .= "<option value='$limitself_value' $selected>$limitself_name</option>\n" ;
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
		<input type='text' size='60' name='options[3]' id='this_template' value='".htmlspecialchars($this_template,ENT_QUOTES)."' />
		<br />
		<label for='limitself'>"._MB_D3DIARY_LIMITSELF."</label>&nbsp;:
		<select name='options[4]' id='limitself'>
			$limitself_options
		</select>
		<br />
	\n" ;

	return $form;
}
?>