<?php

function b_d3d_photolist_show( $options ){

	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_entry = empty( $options[1] ) ? 10 : (int)$options[1] ;
	$params['order'] = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$maxsize = empty( $options[3] ) ? 0 : (int)$options[3] ;
	if (!get_magic_quotes_gpc()) {
		$params['categories'] = empty( $options[4] ) ? array() : explode( ',', addslashes($options[4]) ) ;
		$params['tags'] = empty( $options[5] ) ? array() : explode( ',', addslashes($options[5]) ) ;
	} else {
		$params['categories'] = empty( $options[4] ) ? array() : explode( ',', $options[4] ) ;
		$params['tags'] = empty( $options[5] ) ? array() : explode( ',', $options[5] ) ;
	}
	$params['show_pinfo'] = empty( $options[6] ) ? false : true ;
	$params['max_info'] = empty( $options[7] ) ? 80 : intval( $options[7] ) ;
	$limit_self = empty( $options[8] ) ? 0 : (int)$options[8] ;
	$this_template = empty( $options[9] ) ? 'db:'.$mydirname.'_block_photolist.html' : trim( $options[9] ) ;
	$columns = empty( $options[10] ) ? 0 : (int)$options[10] ;
	$show_entrylink = empty( $options[11] ) ? false : true ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
	$constpref = '_MB_' . strtoupper( $mydirname ) ;
	$_enc = _CHARSET;

	require_once dirname( dirname(__FILE__) ).'/class/d3diaryConf.class.php';
	$d3dConf = & D3diaryConf::getInstance($mydirname, 0, "b_diarylist");
	$func =& $d3dConf->func ;
	$myts =& $d3dConf->myts;
	$mod_config =& $d3dConf->mod_config ;
	
	$uid = $d3dConf->uid;
	$req_uid = $d3dConf->req_uid; // overrided by d3dConf
	
	$params['ofst_key'] = "phbofst" ;
	$_offset_ = $func->getpost_param($params['ofst_key']);
	$offset = isset($_offset_) ?(int)$_offset_ : 0;


	if( $limit_self == 1 ) {				// always show all member's photos
		$req_uid = 0 ;
	} elseif( $limit_self == 2 && $req_uid > 0 ) {		// not show for personal page
		return ;
	} elseif( $limit_self == 3 && $req_uid == 0 ) {		// not show except for personal page
		return ;
	}

	if ($req_uid > 0) {
		$ret = $func->get_xoopsuname($req_uid) ;
		$yd_uname = $ret['uname'];
		$yd_name = $ret['name'];
	}

	$clear_num = array();
	if ( $columns > 0 ) {
		for($i=0; $i<$max_entry+5; $i++) {
			if ( ($i+1)%$columns == 0 ) {
				$clear_num[] = $i ;
			}
		}
	}

	$arr_req_uids = $req_uid > 0 ? array($req_uid) : array() ;
	$params['enc'] = _CHARSET ;
	$params['f_truncate']= true ;

	list( $entry, $photonavi ) = $func->get_photolist ( $arr_req_uids, $uid, $max_entry, $offset, $params );
	
/*	if (!empty($entry)) {
		$_enc =  _CHARSET ;
		$i=0;
		foreach ( $entry as $e ){
			if(empty($e)){break;}
			$entry[$i]['info'] = mb_substr(strip_tags($e['info']),0,$max_length,$_enc) ;
			$i++;
		}
	}
*/
		$lang = array();
		$block = array();
		
		switch ($params['order']) {
		case 'random' :
			$block['photonavi'] = "";
			$lang['order'] =  constant($constpref.'_B_ORDERRANDOM'); ;
			break;
		case 'time' :
		default :
			$block['photonavi'] = $photonavi;
			$lang['order'] =  constant($constpref.'_B_ORDERPOSTED'); ;
		}
			$lang['person'] = constant($constpref.'_PERSON');

		$lang['more'] = constant($constpref.'_MORE');
		
//		$block['entry'] = $entry_temp;
		$block['entry'] = $entry;
		$block['yd_uid'] = $req_uid;
		$block['yd_uname'] = !empty($yd_uname) ? htmlSpecialChars($yd_uname, ENT_QUOTES) : "" ;
		$block['yd_name'] = !empty($yd_name) ? htmlSpecialChars($yd_name, ENT_QUOTES) : "" ;
		$block['lang'] = $lang;
		$block['categories'] = implode(',', $params['categories']);
		$block['tags'] = implode(',', $params['tags']);
		$block['mydirname'] = $mydirname;
		$block['mod_config'] = $mod_config;
		//$block['max_length'] = $max_length;
		$block['maxsize'] = $maxsize;
		$block['show_pinfo'] = $params['show_pinfo'];
		$block['show_entrylink'] = $show_entrylink;
		$block['columns'] = $columns;
		$block['clear_num'] = $clear_num;

	$d3dConf->debug_appendtime('b_photolist');

	if( empty( $options['disable_renderer'] ) ) {
		require_once XOOPS_ROOT_PATH.'/class/template.php' ;
		$tpl =& new XoopsTpl() ;
		$tpl->assign( 'block' , $block ) ;
		$ret['content'] = $tpl->fetch( $this_template ) ;
		return $ret ;
	} else {
		return $block ;
	}
}

function b_d3d_photolist_edit( $options )
{
	$mydirname = empty( $options[0] ) ? 'd3diary' : $options[0] ;
	$max_entry = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] ) ;
	$maxsize = empty( $options[3] ) ? 0 : intval( $options[3] ) ;
	$categories = empty( $options[4] ) ? "" : $options[4] ;
	$tags = empty( $options[5] ) ? "" : $options[5] ;
	$show_pinfo = empty( $options[6] ) ? false : true ;
	$max_length = empty( $options[7] ) ? 30 : intval( $options[7] ) ;
	$limit_self = empty( $options[8] ) ? 0 : (int)$options[8] ;
	$this_template = empty( $options[9] ) ? 'db:'.$mydirname.'_block_photolist.html' : trim( $options[9] ) ;
	$columns = empty( $options[10] ) ? 0 : intval( $options[10] ) ;
	$show_entrylink = empty( $options[11] ) ? false : true ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$orders = array(
		'time' => _MB_D3DIARY_ORDERTIMED ,
		'random' => _MB_D3DIARY_ORDERRANDOM ,
	) ;

	$order_options = '' ;
	foreach( $orders as $order_value => $order_name ) {
		$selected = $order_value == $now_order ? "selected='selected'" : "" ;
		$order_options .= "<option value='$order_value' $selected>$order_name</option>\n" ;
	}

	if( $show_pinfo ) {
		$show_pinfoyes_checked = "checked='checked'" ;
		$show_pinfono_checked = "" ;
	} else {
		$show_pinfono_checked = "checked='checked'" ;
		$show_pinfoyes_checked = "" ;
	}

	if( $show_entrylink ) {
		$show_entrylinkyes_checked = "checked='checked'" ;
		$show_entrylinkno_checked = "" ;
	} else {
		$show_entrylinkno_checked = "checked='checked'" ;
		$show_entrylinkyes_checked = "" ;
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
		<label for='o3'>" .sprintf( _MB_D3DIARY_MAXSIZE, "</label><input type='text' size='6' name='options[3]' id='o3' value='".$maxsize."' style='text-align:right;' />" ) . "
		<br />
		<label for='o4'>"._MB_D3DIARY_QUERY_CATEGORY."</label>&nbsp;:
		<input type='text' size='60' name='options[4]' id='o4' value='".htmlspecialchars($categories,ENT_QUOTES)."' />
		<br />
		<label for='o5'>"._MB_D3DIARY_QUERY_TAG."</label>&nbsp;:
		<input type='text' size='60' name='options[5]' id='o5' value='".htmlspecialchars($tags,ENT_QUOTES)."' />
		<br />
		<label for='o6'>"._MB_D3DIARY_SHOW_PHOTOINFO."</label>&nbsp;:
		<input type='radio' name='options[6]' id='o61' value='1' $show_pinfoyes_checked /><label for='o61'>"._YES."</label>
		<input type='radio' name='options[6]' id='o60' value='0' $show_pinfono_checked /><label for='o60'>"._NO."</label>
		<br />
		<label for='o7'>" ._MB_D3DIARY_INFO_MAXLENGTH."</label><input type='text' size='6' name='options[7]' id='o7' value='".$max_length."' style='text-align:right;' />
		<br />
		<label for='limitself'>"._MB_D3DIARY_LIMITSELF."</label>&nbsp;:
		<select name='options[8]' id='limitself'>
			$limitself_options
		</select><br />
		<label for='this_template'>"._MB_D3DIARY_THISTEMPLATE."</label>&nbsp;:
		<input type='text' size='60' name='options[9]' id='this_template' value='".htmlspecialchars($this_template,ENT_QUOTES)."' />
		<br />
		<label for='o10'>".sprintf( _MB_D3DIARY_PHOTO_COLUMNS , "</label><input type='text' size='4' name='options[10]' id='o10' value='".htmlspecialchars($columns,ENT_QUOTES)."' style='text-align:right;' />" ) . "
		<br />"._MB_D3DIARY_PHOTO_COLUMNSDESC."<br />
		<label for='o11'>"._MB_D3DIARY_SHOW_DIARYLINK."</label>&nbsp;:
		<input type='radio' name='options[11]' id='o111' value='1' $show_entrylinkyes_checked /><label for='o111'>"._YES."</label>
		<input type='radio' name='options[11]' id='o110' value='0' $show_entrylinkno_checked /><label for='o110'>"._NO."</label>
		<br />

\n" ;
	return $form;
}
?>