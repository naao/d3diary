<?php

function b_d3dside_entry_show( $options ){

	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_entry = empty( $options[1] ) ? 10 : (int)$options[1] ;
	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$this_template = empty( $options[3] ) ? 'db:'.$mydirname.'_block_side_entry.html' : trim( $options[3] ) ;
	$limit_self = empty( $options[4] ) ? 0 : (int)$options[4] ;
	$show_category = empty( $options[5] ) ? false : true ;
	if (!get_magic_quotes_gpc()) {
		$params['categories'] = empty( $options[6] ) ? array() : explode( ',', addslashes($options[6]) ) ;
		$params['tags'] = empty( $options[7] ) ? array() : explode( ',', addslashes($options[7]) ) ;
	} else {
		$params['categories'] = empty( $options[6] ) ? array() : explode( ',', $options[6] ) ;
		$params['tags'] = empty( $options[7] ) ? array() : explode( ',', $options[7] ) ;
	}

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	require_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';
	
	$d3dConf = D3diaryConf::getInstance($mydirname, 0, "b_side_entry");
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

		$entry = $func->get_blist ( $req_uid, $uid, $max_entry, true, $params );

		$lang = array();
		$constpref = "_MB_" . strtoupper( $mydirname ) ;
		$lang['more'] = constant($constpref.'_MORE');

		$block="";

		$block['yd_list'] = $entry;
		$block['yd_uid'] = $req_uid;
		$block['show_category'] = $show_category;
		$block['categories'] = implode(',', $params['categories']);
		$block['tags'] = implode(',', $params['tags']);
		$block['lang'] = $lang;
		$block['mydirname'] = $mydirname;
		$block['mod_config'] = $mod_config ;
	
	$d3dConf->debug_appendtime('b_side_entry');

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

function b_d3dside_entry_edit( $options )
{
	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_entry = empty( $options[1] ) ? 10 : (int)$options[1] ;
	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$this_template = empty( $options[3] ) ? 'db:'.$mydirname.'_block_side_entry.html' : trim( $options[3] ) ;
	$limit_self = empty( $options[4] ) ? 0 : (int)$options[4] ;
	$show_category = empty( $options[5] ) ? false : true ;
	$categories = empty( $options[6] ) ? "" : $options[6] ;
	$tags = empty( $options[7] ) ? "" : $options[7] ;

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
	if( $show_category ) {
		$show_categoryyes_checked = "checked='checked'" ;
		$show_categoryno_checked = "" ;
	} else {
		$show_categoryno_checked = "checked='checked'" ;
		$show_categoryyes_checked = "" ;
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
		<label for='o5'>"._MB_D3DIARY_SHOWCATEGORY."</label>&nbsp;:
		<input type='radio' name='options[5]' id='o41' value='1' $show_categoryyes_checked /><label for='o51'>"._YES."</label>
		<input type='radio' name='options[5]' id='o40' value='0' $show_categoryno_checked /><label for='o50'>"._NO."</label>
		<br />
		<label for='o6'>"._MB_D3DIARY_QUERY_CATEGORY."</label>&nbsp;:
		<input type='text' size='60' name='options[6]' id='o6' value='".htmlspecialchars($categories,ENT_QUOTES)."' />
		<br />
		<label for='o7'>"._MB_D3DIARY_QUERY_TAG."</label>&nbsp;:
		<input type='text' size='60' name='options[7]' id='o7' value='".htmlspecialchars($tags,ENT_QUOTES)."' />
		<br />
	\n" ;

	return $form;
}
?>